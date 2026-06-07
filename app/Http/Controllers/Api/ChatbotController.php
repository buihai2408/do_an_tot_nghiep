<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Services\DifyService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(
        private DifyService $difyService
    ) {}

    /**
     * Proxy gửi tin nhắn đến Dify Chat API
     * POST /api/chatbot/message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'query'           => 'required|string|max:2000',
            'conversation_id' => 'nullable|string|max:100',
        ]);

        // ── User identification ───────────────────────────────────────────
        $userId = auth()->check()
            ? 'user-' . auth()->id()
            : 'guest-' . substr($request->session()->getId(), 0, 16);

        // ── Xây dựng context từ dữ liệu thực ─────────────────────────────
        $context = $this->buildContext();

        // ── Ghép context vào query (chỉ cho lần đầu của mỗi conversation) ─
        $conversationId = $request->input('conversation_id');
        $isNewConversation = empty($conversationId);

        $finalQuery = $isNewConversation
            ? $context . "\n\n---\nCâu hỏi của khách hàng: " . $request->input('query')
            : $request->input('query');

        try {
            $result = $this->difyService->sendMessage(
                $finalQuery,
                $userId,
                $conversationId,
                60
            );

            return response()->json([
                'answer'          => $result['answer'],
                'conversation_id' => $result['conversation_id'],
                'message_id'      => $result['message_id'],
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'error' => 'Không thể kết nối đến dịch vụ AI. Vui lòng thử lại sau.',
            ], 503);
        } catch (\RuntimeException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 502);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Xây dựng ngữ cảnh hệ thống để AI hiểu dữ liệu thực của cửa hàng
    // ─────────────────────────────────────────────────────────────────────────
    private function buildContext(): string
    {
        $lines = [];
        $lines[] = '[THÔNG TIN HỆ THỐNG - Chỉ dùng để trả lời, không hiển thị cho khách]';
        $lines[] = 'Bạn là trợ lý AI của "The Coffee Shop". Hãy trả lời bằng tiếng Việt, thân thiện, ngắn gọn và chính xác dựa trên dữ liệu thực dưới đây.';
        $lines[] = '';

        if (auth()->check()) {
            $user = auth()->user()->load('orders');
            $tier = $user->loyaltyTier;

            $lines[] = '=== THÔNG TIN KHÁCH HÀNG ===';
            $lines[] = "Tên: {$user->name}";
            $lines[] = "Email: {$user->email}";
            $lines[] = "Số điện thoại: " . ($user->phone ?: 'Chưa cập nhật');
            $lines[] = "Điểm thưởng hiện tại: {$user->loyalty_points} điểm";
            $lines[] = "Tổng điểm tích lũy: {$user->total_points_earned} điểm";
            $lines[] = "Hạng thành viên: {$tier->icon()} {$tier->label()}";

            // Lên hạng tiếp theo
            $nextTier = $tier->nextTier();
            if ($nextTier) {
                $needed = $nextTier->minPoints() - ($user->total_points_earned ?? 0);
                $needed = max(0, $needed);
                $lines[] = "Điểm cần để lên hạng {$nextTier->label()}: {$needed} điểm";
            } else {
                $lines[] = "Đây là hạng cao nhất (Kim cương).";
            }

            // Đơn hàng gần đây (tối đa 5)
            $recentOrders = $user->orders()
                ->with('items')
                ->latest()
                ->take(5)
                ->get();

            if ($recentOrders->isNotEmpty()) {
                $lines[] = '';
                $lines[] = '=== ĐƠN HÀNG GẦN ĐÂY ===';
                foreach ($recentOrders as $order) {
                    $itemNames = $order->items->map(fn($i) => "{$i->product_name} (x{$i->quantity})")->implode(', ');
                    $status    = $order->status instanceof \App\Enums\OrderStatus
                        ? $order->status->label()
                        : $order->status;
                    $total     = number_format((float) $order->total, 0, ',', '.') . ' VNĐ';
                    $date      = $order->created_at->format('d/m/Y');
                    $lines[]   = "- Đơn #{$order->order_number} ({$date}): {$itemNames} | Tổng: {$total} | Trạng thái: {$status}";
                }
            } else {
                $lines[] = 'Khách hàng chưa có đơn hàng nào.';
            }
        } else {
            $lines[] = '=== KHÁCH HÀNG ===';
            $lines[] = 'Khách chưa đăng nhập (khách vãng lai).';
        }

        // ── 1.5. Thông tin giỏ hàng hiện tại ────────────────────────────
        try {
            $cartService = app(\App\Services\CartService::class);
            $cart = $cartService->getCartWithItems();
            
            $lines[] = '';
            $lines[] = '=== GIỎ HÀNG HIỆN TẠI ===';
            
            if ($cart && $cart->items->isNotEmpty()) {
                $summary = $cartService->getCartSummary();
                foreach ($cart->items as $item) {
                    $productName = $item->product ? $item->product->name : 'Sản phẩm';
                    $sizeInfo = $item->size ? " (Size {$item->size->name})" : "";
                    $priceStr = number_format((float) $item->unit_price, 0, ',', '.') . ' VNĐ';
                    
                    $toppingStr = '';
                    if ($item->toppings->isNotEmpty()) {
                        $toppingNames = $item->toppings->pluck('name')->implode(', ');
                        $toppingStr = " + Topping: {$toppingNames}";
                    }
                    
                    $lines[] = "- {$productName}{$sizeInfo} x{$item->quantity} ({$priceStr}/sp){$toppingStr}";
                }
                $lines[] = "Tổng phụ: " . number_format((float) $summary['subtotal'], 0, ',', '.') . " VNĐ";
                $lines[] = "Phí ship: " . number_format((float) $summary['shipping_fee'], 0, ',', '.') . " VNĐ";
                $lines[] = "Tổng cộng: " . number_format((float) $summary['total'], 0, ',', '.') . " VNĐ";
            } else {
                $lines[] = 'Giỏ hàng đang trống.';
            }
        } catch (\Exception $e) {
            // Không làm sập chatbot nếu lỗi lấy giỏ hàng
        }

        // ── 2. Menu sản phẩm thực từ database ───────────────────────────
        $lines[] = '';
        $lines[] = '=== MENU SẢN PHẨM HIỆN TẠI ===';

        try {
            $categories = Category::with(['products' => function ($q) {
                $q->where('is_active', true)
                  ->with('sizes')
                  ->orderBy('name');
            }])->get();

            foreach ($categories as $cat) {
                $activeProducts = $cat->products->where('is_active', true);
                if ($activeProducts->isEmpty()) continue;

                $lines[] = "\n[{$cat->name}]";
                foreach ($activeProducts as $product) {
                    $priceStr = number_format((float) $product->base_price, 0, ',', '.') . ' VNĐ';

                    // Giá theo size nếu có
                    if ($product->sizes->isNotEmpty()) {
                        $sizePrices = $product->sizes->map(function ($s) {
                            return $s->name . ': ' . number_format((float) $s->pivot->price, 0, ',', '.') . ' VNĐ';
                        })->implode(' / ');
                        $lines[] = "  - {$product->name}: {$sizePrices}";
                    } else {
                        $lines[] = "  - {$product->name}: {$priceStr}";
                    }
                }
            }
        } catch (\Exception $e) {
            $lines[] = '(Không thể tải menu lúc này)';
        }

        // ── 3. Thông tin cửa hàng ─────────────────────────────────────────
        $lines[] = '';
        $lines[] = '=== THÔNG TIN CỬA HÀNG ===';
        $lines[] = 'Tên: The Coffee Shop';
        $lines[] = 'Địa chỉ: Số 99, Đường Cà Phê, Quận 1, TP.HCM';
        $lines[] = 'Điện thoại: 0901 234 567';
        $lines[] = 'Email: info@coffeeshop.vn';
        $lines[] = 'Giờ mở cửa: Thứ 2 - Thứ 6: 07:00 - 22:00 | Thứ 7 - CN: 08:00 - 23:00';
        $lines[] = '';
        $lines[] = '=== QUY TẮC TRẢ LỜI ===';
        $lines[] = '- Chỉ trả lời dựa trên dữ liệu thực tế ở trên.';
        $lines[] = '- Nếu câu hỏi không liên quan đến cà phê/cửa hàng, lịch sự từ chối và hướng về chủ đề cửa hàng.';
        $lines[] = '- Nếu khách hỏi về đơn hàng, hãy tham chiếu dữ liệu đơn hàng thực ở trên.';
        $lines[] = '- Định dạng giá luôn kèm " VNĐ" và dùng dấu chấm ngăn cách hàng nghìn.';

        return implode("\n", $lines);
    }
}
