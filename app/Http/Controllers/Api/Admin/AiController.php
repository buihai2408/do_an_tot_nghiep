<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\DifyService;
use Illuminate\Http\Request;

/**
 * Controller xử lý các tính năng AI cho Admin/Staff.
 * Tách riêng khỏi ProductController để giữ CRUD sản phẩm gọn gàng.
 */
class AiController extends Controller
{
    public function __construct(
        private DifyService $difyService
    ) {}

    /**
     * AI gợi ý mô tả sản phẩm
     * POST /api/admin/ai/generate-description
     */
    public function generateDescription(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
        ]);

        if (!$this->difyService->isConfigured()) {
            return response()->json([
                'error' => 'Chưa cấu hình Dify API. Vui lòng kiểm tra DIFY_API_KEY và DIFY_BASE_URL trong file .env',
            ], 500);
        }

        $productName  = $request->input('name');
        $categoryName = $request->input('category', 'đồ uống');

        $prompt = <<<PROMPT
Bạn là chuyên gia viết mô tả sản phẩm cho quán cà phê. Hãy viết MỘT đoạn mô tả hấp dẫn (2-3 câu, tối đa 150 từ) bằng tiếng Việt cho sản phẩm sau:

- Tên sản phẩm: {$productName}
- Danh mục: {$categoryName}

Yêu cầu:
- Giọng văn gần gũi, cuốn hút, gợi cảm giác thưởng thức
- Nhấn mạnh hương vị, nguyên liệu đặc trưng
- Không dùng icon/emoji, không gạch đầu dòng
- Chỉ trả về đoạn mô tả, không giải thích thêm
PROMPT;

        try {
            $userId = 'admin-' . auth()->id();

            $result = $this->difyService->sendMessage($prompt, $userId, null, 30);

            $description = trim($result['answer']);
            // Loại bỏ dấu ngoặc kép bao quanh nếu AI thêm vào
            $description = trim($description, '"\'');

            return response()->json([
                'description' => $description,
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'error' => 'Không thể kết nối đến dịch vụ AI. Vui lòng thử lại sau.',
            ], 503);
        } catch (\RuntimeException $e) {
            return response()->json([
                'error' => 'Không thể tạo mô tả. Vui lòng thử lại.',
            ], 502);
        }
    }
}
