<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Services\DifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatbotController extends Controller
{
    public function __construct(
        private DifyService $difyService
    ) {
    }



    public function sendMessage(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:2000',
            'conversation_id' => 'nullable|string|max:100',
        ]);


        $userId = auth()->check()
            ? 'user-' . auth()->id()
            : 'guest-' . substr($request->session()->getId(), 0, 16);


        $context = $this->buildContext();


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
                'answer' => $result['answer'],
                'conversation_id' => $result['conversation_id'],
                'message_id' => $result['message_id'],
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




    private function buildContext(): string
    {
        $lines = [];
        $lines[] = '[THÔNG TIN HỆ THỐNG - Chỉ dùng để trả lời, không hiển thị cho khách]';
        $lines[] = 'Bạn là trợ lý AI của "Trạm Cà Phê". Hãy trả lời bằng tiếng Việt, thân thiện, ngắn gọn và chính xác dựa trên dữ liệu thực dưới đây.';
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


            $nextTier = $tier->nextTier();
            if ($nextTier) {
                $needed = $nextTier->minPoints() - ($user->total_points_earned ?? 0);
                $needed = max(0, $needed);
                $lines[] = "Điểm cần để lên hạng {$nextTier->label()}: {$needed} điểm";
            } else {
                $lines[] = "Đây là hạng cao nhất (Kim cương).";
            }


            $recentOrders = $user->orders()
                ->with('items')
                ->latest()
                ->take(10)
                ->get();

            if ($recentOrders->isNotEmpty()) {
                $lines[] = '';
                $lines[] = '=== ĐƠN HÀNG GẦN ĐÂY ===';
                foreach ($recentOrders as $order) {
                    $itemNames = $order->items->map(fn($i) => "{$i->product_name} (x{$i->quantity})")->implode(', ');
                    $status = $order->status instanceof OrderStatus
                        ? $order->status->label()
                        : $order->status;
                    $total = number_format((float) $order->total, 0, ',', '.') . ' VNĐ';
                    $date = $order->created_at->format('d/m/Y');
                    $lines[] = "- Đơn #{$order->order_number} ({$date}): {$itemNames} | Tổng: {$total} | Trạng thái: {$status}";
                }
            } else {
                $lines[] = 'Khách hàng chưa có đơn hàng nào.';
            }
        } else {
            $lines[] = '=== KHÁCH HÀNG ===';
            $lines[] = 'Khách chưa đăng nhập (khách vãng lai).';
        }


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

        }


        $lines[] = '';
        $lines[] = '=== MENU SẢN PHẨM HIỆN TẠI ===';

        try {
            $categories = Category::with([
                'products' => function ($q) {
                    $q->where('is_active', true)
                        ->with('sizes')
                        ->orderBy('name');
                }
            ])->get();

            foreach ($categories as $cat) {
                $activeProducts = $cat->products->where('is_active', true);
                if ($activeProducts->isEmpty())
                    continue;

                $lines[] = "\n[{$cat->name}]";
                foreach ($activeProducts as $product) {
                    $priceStr = number_format((float) $product->base_price, 0, ',', '.') . ' VNĐ';


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


        $this->buildShopStatistics($lines);

        $lines[] = '';
        $lines[] = '=== THÔNG TIN CỬA HÀNG ===';
        $lines[] = 'Tên: Trạm Cà Phê';
        $lines[] = 'Địa chỉ: 175 Tây Sơn, Đống Đa, Hà Nội';
        $lines[] = 'Điện thoại: 0966461728';
        $lines[] = 'Email: info@coffeeshop.vn';
        $lines[] = 'Giờ mở cửa: Thứ 2 - Thứ 6: 07:00 - 22:00 | Thứ 7 - CN: 08:00 - 23:00';
        $lines[] = '';
        $lines[] = '=== QUY TẮC TRẢ LỜI ===';
        $lines[] = '- Chỉ trả lời dựa trên dữ liệu thực tế ở trên.';
        $lines[] = '- Nếu câu hỏi không liên quan đến cà phê/cửa hàng, lịch sự từ chối và hướng về chủ đề cửa hàng.';
        $lines[] = '- Nếu khách hỏi về đơn hàng, hãy tham chiếu dữ liệu đơn hàng thực ở trên.';
        $lines[] = '- Định dạng giá luôn kèm " VNĐ" và dùng dấu chấm ngăn cách hàng nghìn.';
        $lines[] = '- Khi khách hỏi về sản phẩm bán chạy, yêu thích, đánh giá cao... hãy dùng dữ liệu THỐNG KÊ SHOP ở trên.';
        $lines[] = '- Khi khách hỏi về mã giảm giá, khuyến mãi, hãy dùng dữ liệu MÃ GIẢM GIÁ ở trên.';
        $lines[] = '- Trả lời thống kê một cách tự nhiên, thân thiện, có thể gợi ý sản phẩm cho khách.';

        return implode("\n", $lines);
    }

    private function buildShopStatistics(array &$lines): void
    {
        try {
            $lines[] = '';
            $lines[] = '=== THỐNG KÊ TỔNG QUAN SHOP ===';
            $totalProducts = Product::where('is_active', true)->count();
            $totalCompletedOrders = Order::where('status', OrderStatus::Completed)->count();
            $totalCustomers = User::where('role', 'customer')->count();
            $totalRevenue = Order::where('status', OrderStatus::Completed)->sum('total');
            $lines[] = "Tổng sản phẩm đang bán: {$totalProducts}";
            $lines[] = "Tổng đơn hàng hoàn thành: {$totalCompletedOrders}";
            $lines[] = "Tổng khách hàng: {$totalCustomers}";
            $lines[] = "Tổng doanh thu: " . number_format((float) $totalRevenue, 0, ',', '.') . " VNĐ";

            $this->buildTopSellingProducts($lines);
            $this->buildTopRatedProducts($lines);
            $this->buildActiveCoupons($lines);
        } catch (\Exception $e) {
            $lines[] = '(Không thể tải thống kê lúc này)';
        }
    }

    private function buildTopSellingProducts(array &$lines): void
    {
        $topProducts = OrderItem::select(
            'product_name',
            DB::raw('SUM(quantity) as total_sold'),
            DB::raw('SUM(subtotal) as total_revenue')
        )
            ->whereHas('order', function ($q) {
                $q->where('status', OrderStatus::Completed);
            })
            ->groupBy('product_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        if ($topProducts->isNotEmpty()) {
            $lines[] = '';
            $lines[] = '=== TOP SẢN PHẨM BÁN CHẠY NHẤT ===';
            foreach ($topProducts as $i => $item) {
                $rank = $i + 1;
                $revenue = number_format((float) $item->total_revenue, 0, ',', '.') . ' VNĐ';
                $lines[] = "{$rank}. {$item->product_name} - Đã bán: {$item->total_sold} ly - Doanh thu: {$revenue}";
            }
        }
    }

    private function buildTopRatedProducts(array &$lines): void
    {
        $topRated = Review::select(
            'product_id',
            DB::raw('AVG(rating) as avg_rating'),
            DB::raw('COUNT(*) as review_count')
        )
            ->where('is_approved', true)
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->having('review_count', '>=', 1)
            ->orderByDesc('avg_rating')
            ->orderByDesc('review_count')
            ->limit(5)
            ->with('product:id,name')
            ->get();

        if ($topRated->isNotEmpty()) {
            $lines[] = '';
            $lines[] = '=== TOP SẢN PHẨM ĐƯỢC ĐÁNH GIÁ CAO NHẤT ===';
            foreach ($topRated as $i => $item) {
                $rank = $i + 1;
                $name = $item->product->name ?? 'N/A';
                $avg = round($item->avg_rating, 1);
                $lines[] = "{$rank}. {$name} - Điểm: {$avg}/5 ⭐ ({$item->review_count} đánh giá)";
            }
        }
    }

    private function buildActiveCoupons(array &$lines): void
    {
        $coupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->get();

        if ($coupons->isNotEmpty()) {
            $lines[] = '';
            $lines[] = '=== MÃ GIẢM GIÁ ĐANG CÓ HIỆU LỰC ===';
            foreach ($coupons as $coupon) {
                $discountStr = $coupon->type === 'percentage'
                    ? "Giảm {$coupon->value}%"
                    : 'Giảm ' . number_format((float) $coupon->value, 0, ',', '.') . ' VNĐ';

                $conditions = [];
                if ($coupon->min_order_amount > 0) {
                    $conditions[] = 'Đơn tối thiểu ' . number_format((float) $coupon->min_order_amount, 0, ',', '.') . ' VNĐ';
                }
                if ($coupon->max_discount > 0 && $coupon->type === 'percentage') {
                    $conditions[] = 'Giảm tối đa ' . number_format((float) $coupon->max_discount, 0, ',', '.') . ' VNĐ';
                }
                if ($coupon->expires_at) {
                    $conditions[] = 'HSD: ' . $coupon->expires_at->format('d/m/Y');
                }

                $condStr = $conditions ? ' (' . implode(', ', $conditions) . ')' : '';
                $lines[] = "- Mã: {$coupon->code} | {$coupon->name} | {$discountStr}{$condStr}";
            }
        } else {
            $lines[] = '';
            $lines[] = '=== MÃ GIẢM GIÁ ===';
            $lines[] = 'Hiện tại không có mã giảm giá nào đang hoạt động.';
        }
    }
}
