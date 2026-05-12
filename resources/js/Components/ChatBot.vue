<script setup>
import { ref, computed, nextTick } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

// ── Inertia page props (auth user info) ────────────────────────────────────
const page = usePage();
const authUser = computed(() => page.props.auth?.user ?? null);

// ── State ──────────────────────────────────────────────────────────────────
const isOpen         = ref(false);
const isLoading      = ref(false);
const inputText      = ref('');
const conversationId = ref(null);   // null = cuộc hội thoại mới (không gửi lên server)
const messages       = ref([]);
const messagesEnd    = ref(null);
const inputRef       = ref(null);
const hasGreeted     = ref(false);

// ── Suggested quick questions (thêm gợi ý cá nhân nếu đã đăng nhập) ───────
const suggestions = computed(() => {
    const base = [
        '☕ Menu có những gì?',
        '💰 Giá cả như thế nào?',
        '🕒 Giờ mở cửa?',
        '📍 Địa chỉ quán?',
    ];
    if (authUser.value) {
        return [
            '📦 Đơn hàng gần đây của tôi?',
            '🏆 Điểm thưởng của tôi?',
            ...base,
        ];
    }
    return base;
});

// ── Greeting cá nhân hóa ──────────────────────────────────────────────────
const greetingText = computed(() => {
    if (authUser.value) {
        return `Xin chào, **${authUser.value.name}**! 👋 Tôi là trợ lý AI của The Coffee Shop. Tôi có thể tra cứu đơn hàng, điểm thưởng, menu và nhiều thông tin khác cho bạn. Bạn cần hỗ trợ gì? ☕`;
    }
    return 'Xin chào! 👋 Tôi là trợ lý AI của The Coffee Shop. Tôi có thể giúp bạn tìm hiểu về menu, giá cả, khuyến mãi, giờ mở cửa và nhiều thông tin khác. Bạn muốn hỏi gì nào? ☕';
});

// ── Methods ────────────────────────────────────────────────────────────────
function toggleChat() {
    isOpen.value = !isOpen.value;
    if (isOpen.value && !hasGreeted.value) {
        hasGreeted.value = true;
        setTimeout(() => {
            messages.value.push({
                id:   Date.now(),
                role: 'assistant',
                text: greetingText.value,
            });
            scrollToBottom();
        }, 300);
    }
    if (isOpen.value) {
        nextTick(() => inputRef.value?.focus());
    }
}

function useSuggestion(text) {
    inputText.value = text;
    sendMessage();
}

async function sendMessage() {
    const query = inputText.value.trim();
    if (!query || isLoading.value) return;

    // Add user message
    messages.value.push({ id: Date.now(), role: 'user', text: query });
    inputText.value = '';
    resetTextareaHeight();   // reset chiều cao textarea về 1 dòng
    isLoading.value = true;
    await scrollToBottom();

    try {
        // Chỉ gửi conversation_id khi có giá trị thực
        const payload = { query };
        if (conversationId.value) {
            payload.conversation_id = conversationId.value;
        }

        const res = await axios.post('/api/chatbot/message', payload);

        // Lưu conversation_id để duy trì ngữ cảnh các tin nhắn tiếp theo
        if (res.data.conversation_id) {
            conversationId.value = res.data.conversation_id;
        }

        messages.value.push({
            id:   Date.now() + 1,
            role: 'assistant',
            text: res.data.answer || 'Xin lỗi, tôi chưa hiểu câu hỏi của bạn.',
        });
    } catch (err) {
        const errMsg = err.response?.data?.error
            || 'Có lỗi xảy ra, vui lòng thử lại sau.';
        messages.value.push({
            id:   Date.now() + 1,
            role: 'error',
            text: errMsg,
        });
    } finally {
        isLoading.value = false;
        await scrollToBottom();
        nextTick(() => inputRef.value?.focus());
    }
}

function handleKeydown(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
}

// Tự động tăng chiều cao textarea theo nội dung
function autoResize(e) {
    const el = e?.target ?? inputRef.value;
    if (!el) return;
    el.style.height = 'auto';          // reset về auto trước
    el.style.height = el.scrollHeight + 'px'; // rồi đặt đúng bằng content
}

// Reset textarea về 1 dòng sau khi gửi
function resetTextareaHeight() {
    const el = inputRef.value;
    if (!el) return;
    el.style.height = 'auto';
}

function clearChat() {
    messages.value      = [];
    conversationId.value = null;  // reset về null để server biết là cuộc hội thoại mới
    hasGreeted.value    = false;
    // Hiển thị greeting lại
    setTimeout(() => {
        hasGreeted.value = true;
        messages.value.push({
            id:   Date.now(),
            role: 'assistant',
            text: greetingText.value,
        });
    }, 100);
}

async function scrollToBottom() {
    await nextTick();
    messagesEnd.value?.scrollIntoView({ behavior: 'smooth' });
}

// Format text: **bold**, *italic*, newlines -> <br>
function formatText(text) {
    if (!text) return '';
    return text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/\n/g, '<br>');
}
</script>


<template>
    <!-- Floating Button -->
    <div class="chatbot-wrapper">
        <button
            id="chatbot-toggle-btn"
            class="chatbot-fab"
            :class="{ 'chatbot-fab--open': isOpen }"
            @click="toggleChat"
            aria-label="Mở hộp chat AI"
        >
            <!-- Chat icon -->
            <svg v-if="!isOpen" class="chatbot-fab__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <!-- Close icon -->
            <svg v-else class="chatbot-fab__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>

            <!-- Pulse ring (only when closed) -->
            <span v-if="!isOpen" class="chatbot-fab__pulse"></span>
        </button>

        <!-- Chat Window -->
        <Transition name="chatbot-slide">
            <div v-if="isOpen" id="chatbot-window" class="chatbot-window" role="dialog" aria-label="Chatbot AI">

                <!-- Header -->
                <div class="chatbot-header">
                    <div class="chatbot-header__info">
                        <div class="chatbot-header__avatar">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="chatbot-header__name">Coffee AI Assistant</p>
                            <span class="chatbot-header__status">
                                <span class="chatbot-header__dot"></span>
                                Trực tuyến
                            </span>
                        </div>
                    </div>
                    <div class="chatbot-header__actions">
                        <button @click="clearChat" class="chatbot-header__btn" title="Cuộc trò chuyện mới">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                        <button @click="toggleChat" class="chatbot-header__btn" title="Đóng">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Messages -->
                <div id="chatbot-messages" class="chatbot-messages">
                    <!-- Empty state with suggestions -->
                    <div v-if="messages.length === 0" class="chatbot-welcome">
                        <div class="chatbot-welcome__icon">☕</div>
                        <p class="chatbot-welcome__text">Hỏi tôi bất cứ điều gì về The Coffee Shop!</p>
                        <div class="chatbot-suggestions">
                            <button
                                v-for="s in suggestions"
                                :key="s"
                                class="chatbot-suggestion"
                                @click="useSuggestion(s)"
                            >{{ s }}</button>
                        </div>
                    </div>

                    <!-- Message list -->
                    <div
                        v-for="msg in messages"
                        :key="msg.id"
                        class="chatbot-msg"
                        :class="`chatbot-msg--${msg.role}`"
                    >
                        <div v-if="msg.role === 'assistant' || msg.role === 'error'" class="chatbot-msg__avatar">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/>
                            </svg>
                        </div>
                        <div class="chatbot-msg__bubble" v-html="formatText(msg.text)"></div>
                    </div>

                    <!-- Typing indicator -->
                    <div v-if="isLoading" class="chatbot-msg chatbot-msg--assistant">
                        <div class="chatbot-msg__avatar">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/>
                            </svg>
                        </div>
                        <div class="chatbot-typing">
                            <span></span><span></span><span></span>
                        </div>
                    </div>

                    <div ref="messagesEnd"></div>
                </div>

                <!-- Input -->
                <div class="chatbot-input-area">
                    <!-- Suggestions (show when no messages or few messages) -->
                    <div v-if="messages.length > 0 && messages.length <= 2" class="chatbot-suggestions chatbot-suggestions--inline">
                        <button
                            v-for="s in suggestions"
                            :key="s"
                            class="chatbot-suggestion chatbot-suggestion--sm"
                            @click="useSuggestion(s)"
                        >{{ s }}</button>
                    </div>
                    <div class="chatbot-input-row">
                        <textarea
                            ref="inputRef"
                            id="chatbot-input"
                            v-model="inputText"
                            class="chatbot-input"
                            placeholder="Nhập câu hỏi của bạn..."
                            rows="1"
                            :disabled="isLoading"
                            @keydown="handleKeydown"
                            @input="autoResize"
                        ></textarea>
                        <button
                            id="chatbot-send-btn"
                            class="chatbot-send"
                            :disabled="!inputText.trim() || isLoading"
                            @click="sendMessage"
                            aria-label="Gửi"
                        >
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                        </button>
                    </div>
                    <p class="chatbot-footer-note">Powered by Dify × Gemini AI</p>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
/* ── Variables ─────────────────────────────────────────────────────────────── */
:root {
    --cb-gold:      #D4A853;
    --cb-dark:      #2C1810;
    --cb-mid:       #5C3A1E;
    --cb-cream:     #FAF6F0;
    --cb-border:    #E8D9C5;
    --cb-radius:    16px;
    --cb-shadow:    0 20px 60px rgba(44,24,16,0.22);
}

/* ── Wrapper ─────────────────────────────────────────────────────────────── */
.chatbot-wrapper {
    position: fixed;
    bottom: 28px;
    right: 28px;
    z-index: 9999;
    font-family: 'Inter', sans-serif;
}

/* ── FAB Button ──────────────────────────────────────────────────────────── */
.chatbot-fab {
    position: relative;
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: #D4A853;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 28px rgba(212,168,83,0.5);
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s;
    outline: none;
}
.chatbot-fab:hover {
    transform: scale(1.08);
    box-shadow: 0 12px 36px rgba(212,168,83,0.65);
    background: #E8C17A;
}
.chatbot-fab--open {
    background: #2C1810;
    box-shadow: 0 8px 28px rgba(44,24,16,0.4);
}
.chatbot-fab--open:hover {
    background: #5C3A1E;
    box-shadow: 0 12px 36px rgba(44,24,16,0.5);
}
.chatbot-fab__icon {
    width: 26px;
    height: 26px;
    color: #2C1810;
    z-index: 1;
    transition: color 0.2s, transform 0.3s;
}
.chatbot-fab--open .chatbot-fab__icon { color: #fff; }
.chatbot-fab__pulse {
    position: absolute;
    inset: -4px;
    border-radius: 50%;
    background: rgba(212,168,83,0.35);
    animation: pulse 2s ease-out infinite;
}
@keyframes pulse {
    0%   { transform: scale(1);   opacity: 0.7; }
    100% { transform: scale(1.7); opacity: 0;   }
}

/* ── Window ──────────────────────────────────────────────────────────────── */
.chatbot-window {
    position: absolute;
    bottom: 72px;
    right: 0;
    width: 375px;
    height: 570px;
    background: #fff;
    border-radius: var(--cb-radius);
    box-shadow: var(--cb-shadow);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid #E8D9C5;
}

/* ── Slide transition ────────────────────────────────────────────────────── */
.chatbot-slide-enter-active, .chatbot-slide-leave-active {
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.chatbot-slide-enter-from, .chatbot-slide-leave-to {
    opacity: 0;
    transform: scale(0.9) translateY(16px);
    transform-origin: bottom right;
}

/* ── Header ──────────────────────────────────────────────────────────────── */
.chatbot-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 13px 15px;
    background: linear-gradient(135deg, #2C1810 0%, #5C3A1E 100%);
    flex-shrink: 0;
    border-bottom: 1px solid rgba(212,168,83,0.2);
}
.chatbot-header__info { display: flex; align-items: center; gap: 10px; }
.chatbot-header__avatar {
    width: 36px; height: 36px;
    background: #D4A853;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.chatbot-header__avatar svg { width: 19px; height: 19px; color: #2C1810; fill: #2C1810; }
.chatbot-header__name { font-size: 14px; font-weight: 600; color: #fff; margin: 0; line-height: 1.2; }
.chatbot-header__status { display: flex; align-items: center; gap: 5px; font-size: 11px; color: rgba(255,255,255,0.6); }
.chatbot-header__dot { width: 6px; height: 6px; background: #4ade80; border-radius: 50%; animation: blink 2s infinite; }
@keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
.chatbot-header__actions { display: flex; gap: 4px; }
.chatbot-header__btn {
    width: 30px; height: 30px; border-radius: 6px; border: none;
    background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7);
    cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.15s;
}
.chatbot-header__btn:hover { background: rgba(212,168,83,0.3); color: #D4A853; }
.chatbot-header__btn svg { width: 14px; height: 14px; }

/* ── Messages ────────────────────────────────────────────────────────────── */
.chatbot-messages {
    flex: 1; overflow-y: auto; padding: 16px;
    display: flex; flex-direction: column; gap: 12px; scroll-behavior: smooth;
    background: #FFFDF9;
}
.chatbot-messages::-webkit-scrollbar { width: 4px; }
.chatbot-messages::-webkit-scrollbar-track { background: transparent; }
.chatbot-messages::-webkit-scrollbar-thumb { background: #E8D9C5; border-radius: 4px; }

/* ── Welcome state ───────────────────────────────────────────────────────── */
.chatbot-welcome {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    flex: 1; padding: 24px 8px; text-align: center;
}
.chatbot-welcome__icon { font-size: 52px; margin-bottom: 10px; animation: float 3s ease-in-out infinite; }
@keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
.chatbot-welcome__text { font-size: 13px; color: #8B7355; margin-bottom: 16px; line-height: 1.5; }

/* ── Suggestions ─────────────────────────────────────────────────────────── */
.chatbot-suggestions { display: flex; flex-wrap: wrap; gap: 6px; justify-content: center; }
.chatbot-suggestions--inline { padding: 8px 12px 4px; justify-content: flex-start; border-top: 1px solid #F2EBE0; }
.chatbot-suggestion {
    background: #FAF6F0; border: 1px solid #E8D9C5; border-radius: 20px;
    padding: 5px 12px; font-size: 11.5px; color: #5C3A1E;
    cursor: pointer; transition: all 0.15s; font-family: 'Inter', sans-serif; white-space: nowrap;
}
.chatbot-suggestion:hover {
    background: #D4A853; border-color: #D4A853; color: #2C1810;
    transform: translateY(-1px); box-shadow: 0 3px 10px rgba(212,168,83,0.35);
}
.chatbot-suggestion--sm { font-size: 11px; padding: 4px 10px; }

/* ── Individual message ──────────────────────────────────────────────────── */
.chatbot-msg { display: flex; align-items: flex-end; gap: 8px; animation: msgIn 0.25s ease; }
@keyframes msgIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
.chatbot-msg--user { flex-direction: row-reverse; }
.chatbot-msg__avatar {
    width: 28px; height: 28px; border-radius: 50%; background: #D4A853;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.chatbot-msg__avatar svg { width: 15px; height: 15px; fill: #2C1810; }
.chatbot-msg__bubble {
    max-width: 78%; padding: 10px 14px; border-radius: 14px;
    font-size: 13.5px; line-height: 1.55; word-break: break-word;
}
.chatbot-msg--user .chatbot-msg__bubble {
    background: linear-gradient(135deg, #2C1810 0%, #5C3A1E 100%);
    color: #fff; border-bottom-right-radius: 4px;
}
.chatbot-msg--assistant .chatbot-msg__bubble {
    background: #FAF6F0; color: #2C1810;
    border-bottom-left-radius: 4px; border: 1px solid #E8D9C5;
}
.chatbot-msg--error .chatbot-msg__bubble {
    background: #fef2f2; color: #991b1b;
    border: 1px solid #fecaca; border-bottom-left-radius: 4px;
}

/* ── Typing dots ─────────────────────────────────────────────────────────── */
.chatbot-typing {
    background: #FAF6F0; border: 1px solid #E8D9C5;
    border-radius: 14px; border-bottom-left-radius: 4px;
    padding: 12px 16px; display: flex; gap: 4px; align-items: center;
}
.chatbot-typing span { width: 7px; height: 7px; background: #D4A853; border-radius: 50%; animation: typing 1.2s ease-in-out infinite; }
.chatbot-typing span:nth-child(2) { animation-delay: 0.2s; }
.chatbot-typing span:nth-child(3) { animation-delay: 0.4s; }
@keyframes typing {
    0%, 60%, 100% { transform: translateY(0);    opacity: 0.4; }
    30%           { transform: translateY(-6px); opacity: 1;   }
}

/* ── Input area ──────────────────────────────────────────────────────────── */
.chatbot-input-area { border-top: 1px solid #F2EBE0; flex-shrink: 0; background: #fff; }
.chatbot-input-row { display: flex; align-items: flex-end; gap: 8px; padding: 10px 12px 8px; }
.chatbot-input {
    flex: 1; border: 1.5px solid #E8D9C5; border-radius: 22px;
    padding: 9px 14px; font-size: 13.5px; font-family: 'Inter', sans-serif;
    resize: none; outline: none; line-height: 1.5;
    min-height: 38px; max-height: 110px; height: auto;
    overflow-y: auto; overflow-x: hidden;
    color: #2C1810; transition: border-color 0.2s, box-shadow 0.2s;
    background: #FAF6F0; word-break: break-word; white-space: pre-wrap; box-sizing: border-box;
}
.chatbot-input:focus { border-color: #D4A853; background: #fff; box-shadow: 0 0 0 3px rgba(212,168,83,0.12); }
.chatbot-input::placeholder { color: #B5A089; }
.chatbot-input:disabled { opacity: 0.5; cursor: not-allowed; }

.chatbot-send {
    width: 38px; height: 38px; border-radius: 50%; border: none;
    background: #2C1810; color: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s ease; flex-shrink: 0;
}
.chatbot-send:hover:not(:disabled) {
    background: #D4A853; color: #2C1810;
    transform: scale(1.08); box-shadow: 0 4px 12px rgba(212,168,83,0.45);
}
.chatbot-send:disabled { opacity: 0.4; cursor: not-allowed; }
.chatbot-send svg { width: 17px; height: 17px; margin-left: 2px; }

.chatbot-footer-note {
    font-size: 10px; color: #B5A089; text-align: center;
    padding-bottom: 8px; margin: 0; letter-spacing: 0.02em;
}

/* ── Responsive ──────────────────────────────────────────────────────────── */
@media (max-width: 480px) {
    .chatbot-wrapper { bottom: 16px; right: 16px; }
    .chatbot-window { width: calc(100vw - 32px); right: 0; bottom: 70px; }
}
</style>
