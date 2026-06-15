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
        return `Xin chào, **${authUser.value.name}**! 👋 Tôi là trợ lý AI của Trạm Cà Phê. Tôi có thể tra cứu đơn hàng, điểm thưởng, menu và nhiều thông tin khác cho bạn. Bạn cần hỗ trợ gì? ☕`;
    }
    return 'Xin chào! 👋 Tôi là trợ lý AI của Trạm Cà Phê. Tôi có thể giúp bạn tìm hiểu về menu, giá cả, khuyến mãi, giờ mở cửa và nhiều thông tin khác. Bạn muốn hỏi gì nào? ☕';
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
    <div class="fixed bottom-4 right-4 md:bottom-7 md:right-7 z-[9999] font-sans">
        <button
            id="chatbot-toggle-btn"
            class="relative w-14 h-14 rounded-full border-none cursor-pointer flex items-center justify-center shadow-[0_8px_28px_rgba(212,168,83,0.5)] transition-all duration-200 outline-none"
            :class="isOpen ? 'bg-[#2C1810] shadow-[0_8px_28px_rgba(44,24,16,0.4)] hover:bg-[#5C3A1E] hover:shadow-[0_12px_36px_rgba(44,24,16,0.5)]' : 'bg-[#D4A853] hover:scale-105 hover:bg-[#E8C17A] hover:shadow-[0_12px_36px_rgba(212,168,83,0.65)]'"
            @click="toggleChat"
            aria-label="Mở hộp chat AI"
        >
            <!-- Chat icon -->
            <svg v-if="!isOpen" class="w-6 h-6 z-10 transition-all duration-300 text-[#2C1810]" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <!-- Close icon -->
            <svg v-else class="w-6 h-6 z-10 transition-all duration-300 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>

            <!-- Pulse ring (only when closed) -->
            <span v-if="!isOpen" class="absolute inset-[-4px] rounded-full bg-[rgba(212,168,83,0.35)] animate-[ping_2s_ease-out_infinite]"></span>
        </button>

        <!-- Chat Window -->
        <Transition
            enter-active-class="transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)]"
            enter-from-class="opacity-0 scale-90 translate-y-4"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)]"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-90 translate-y-4"
        >
            <div v-if="isOpen" id="chatbot-window" class="absolute bottom-[72px] right-0 w-[calc(100vw-32px)] sm:w-[375px] h-[calc(100vh-120px)] sm:h-[570px] bg-white rounded-2xl shadow-[0_20px_60px_rgba(44,24,16,0.22)] flex flex-col overflow-hidden border border-[#E8D9C5] origin-bottom-right" role="dialog" aria-label="Chatbot AI">

                <!-- Header -->
                <div class="flex items-center justify-between p-3.5 bg-gradient-to-br from-[#2C1810] to-[#5C3A1E] shrink-0 border-b border-[rgba(212,168,83,0.2)]">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 bg-[#D4A853] rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-[#2C1810] fill-[#2C1810]" viewBox="0 0 24 24">
                                <path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white m-0 leading-tight">Coffee AI Assistant</p>
                            <span class="flex items-center gap-1.5 text-[11px] text-white/60">
                                <span class="w-1.5 h-1.5 bg-[#4ade80] rounded-full animate-pulse"></span>
                                Trực tuyến
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-1">
                        <button @click="clearChat" class="w-8 h-8 rounded-md border-none bg-white/10 text-white/70 flex items-center justify-center cursor-pointer transition-colors hover:bg-[#D4A853]/30 hover:text-[#D4A853]" title="Cuộc trò chuyện mới">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                        <button @click="toggleChat" class="w-8 h-8 rounded-md border-none bg-white/10 text-white/70 flex items-center justify-center cursor-pointer transition-colors hover:bg-[#D4A853]/30 hover:text-[#D4A853]" title="Đóng">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Messages -->
                <div id="chatbot-messages" class="flex-1 overflow-y-auto p-4 flex flex-col gap-3 scroll-smooth bg-[#FFFDF9] scrollbar-thin scrollbar-thumb-[#E8D9C5] scrollbar-track-transparent">
                    <!-- Empty state with suggestions -->
                    <div v-if="messages.length === 0" class="flex flex-col items-center justify-center flex-1 py-6 px-2 text-center">
                        <div class="text-5xl mb-2.5 animate-bounce">☕</div>
                        <p class="text-[13px] text-[#8B7355] mb-4 leading-relaxed">Hỏi tôi bất cứ điều gì về Trạm Cà Phê!</p>
                        <div class="flex flex-wrap justify-center gap-1.5">
                            <button
                                v-for="s in suggestions"
                                :key="s"
                                class="bg-[#FAF6F0] border border-[#E8D9C5] rounded-full px-3 py-1.5 text-[11.5px] text-[#5C3A1E] cursor-pointer transition-all duration-150 whitespace-nowrap hover:bg-[#D4A853] hover:border-[#D4A853] hover:text-[#2C1810] hover:-translate-y-px hover:shadow-md"
                                @click="useSuggestion(s)"
                            >{{ s }}</button>
                        </div>
                    </div>

                    <!-- Message list -->
                    <div
                        v-for="msg in messages"
                        :key="msg.id"
                        class="flex items-end gap-2 animate-[fadeInUp_0.25s_ease]"
                        :class="msg.role === 'user' ? 'flex-row-reverse' : ''"
                    >
                        <div v-if="msg.role === 'assistant' || msg.role === 'error'" class="w-7 h-7 rounded-full bg-[#D4A853] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 fill-[#2C1810]" viewBox="0 0 24 24">
                                <path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/>
                            </svg>
                        </div>
                        <div
                            class="max-w-[85%] sm:max-w-[78%] px-3.5 py-2.5 rounded-[14px] text-[13.5px] leading-relaxed break-words"
                            :class="{
                                'bg-gradient-to-br from-[#2C1810] to-[#5C3A1E] text-white rounded-br-sm': msg.role === 'user',
                                'bg-[#FAF6F0] text-[#2C1810] rounded-bl-sm border border-[#E8D9C5]': msg.role === 'assistant',
                                'bg-red-50 text-red-800 rounded-bl-sm border border-red-200': msg.role === 'error'
                            }"
                            v-html="formatText(msg.text)"
                        ></div>
                    </div>

                    <!-- Typing indicator -->
                    <div v-if="isLoading" class="flex items-end gap-2 animate-[fadeInUp_0.25s_ease]">
                        <div class="w-7 h-7 rounded-full bg-[#D4A853] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 fill-[#2C1810]" viewBox="0 0 24 24">
                                <path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/>
                            </svg>
                        </div>
                        <div class="bg-[#FAF6F0] border border-[#E8D9C5] rounded-[14px] rounded-bl-sm px-4 py-3 flex gap-1 items-center">
                            <span class="w-1.5 h-1.5 bg-[#D4A853] rounded-full animate-bounce"></span>
                            <span class="w-1.5 h-1.5 bg-[#D4A853] rounded-full animate-bounce [animation-delay:0.2s]"></span>
                            <span class="w-1.5 h-1.5 bg-[#D4A853] rounded-full animate-bounce [animation-delay:0.4s]"></span>
                        </div>
                    </div>

                    <div ref="messagesEnd"></div>
                </div>

                <!-- Input -->
                <div class="border-t border-[#F2EBE0] shrink-0 bg-white">
                    <!-- Suggestions (show when no messages or few messages) -->
                    <div v-if="messages.length > 0 && messages.length <= 2" class="flex flex-wrap gap-1.5 pt-2 px-3 justify-start border-t border-[#F2EBE0]">
                        <button
                            v-for="s in suggestions"
                            :key="s"
                            class="bg-[#FAF6F0] border border-[#E8D9C5] rounded-full px-2.5 py-1 text-[11px] text-[#5C3A1E] cursor-pointer transition-all duration-150 whitespace-nowrap hover:bg-[#D4A853] hover:border-[#D4A853] hover:text-[#2C1810] hover:-translate-y-px hover:shadow-md"
                            @click="useSuggestion(s)"
                        >{{ s }}</button>
                    </div>
                    <div class="flex items-end gap-2 p-2.5 pt-2">
                        <textarea
                            ref="inputRef"
                            id="chatbot-input"
                            v-model="inputText"
                            class="flex-1 border border-[#E8D9C5] rounded-3xl py-2.5 px-4 text-[13.5px] font-sans resize-none outline-none leading-relaxed min-h-[42px] max-h-[110px] h-auto overflow-y-auto text-[#2C1810] transition-all bg-[#FAF6F0] focus:border-[#D4A853] focus:bg-white focus:ring-[3px] focus:ring-[rgba(212,168,83,0.12)] placeholder:text-[#B5A089] disabled:opacity-50 disabled:cursor-not-allowed"
                            placeholder="Nhập câu hỏi của bạn..."
                            rows="1"
                            :disabled="isLoading"
                            @keydown="handleKeydown"
                            @input="autoResize"
                        ></textarea>
                        <button
                            id="chatbot-send-btn"
                            class="w-[42px] h-[42px] rounded-full border-none bg-[#2C1810] text-white cursor-pointer flex items-center justify-center transition-all shrink-0 hover:not-disabled:bg-[#D4A853] hover:not-disabled:text-[#2C1810] hover:not-disabled:scale-110 hover:not-disabled:shadow-md disabled:opacity-40 disabled:cursor-not-allowed"
                            :disabled="!inputText.trim() || isLoading"
                            @click="sendMessage"
                            aria-label="Gửi"
                        >
                            <svg class="w-4 h-4 ml-0.5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-[10px] text-[#B5A089] text-center pb-2 m-0 tracking-wide">Powered by Dify × Gemini AI</p>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style>
/* Animation classes for Tailwind */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
