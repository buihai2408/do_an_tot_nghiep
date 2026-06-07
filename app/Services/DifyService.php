<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service dùng chung để giao tiếp với Dify AI API.
 * Được sử dụng bởi: ChatbotController, AiController (admin).
 */
class DifyService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.dify.api_key', '');
        $this->baseUrl = config('services.dify.base_url', '');
    }

    /**
     * Kiểm tra đã cấu hình API chưa.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->baseUrl);
    }

    /**
     * Gửi tin nhắn đến Dify Chat API (blocking mode).
     *
     * @param  string      $query          Nội dung truy vấn
     * @param  string      $userId         ID người dùng (VD: 'user-1', 'admin-3')
     * @param  string|null $conversationId ID cuộc hội thoại (nếu tiếp tục)
     * @param  int         $timeout        Thời gian timeout (giây)
     * @return array       ['answer' => string, 'conversation_id' => string, 'message_id' => string]
     *
     * @throws \RuntimeException
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function sendMessage(
        string  $query,
        string  $userId,
        ?string $conversationId = null,
        int     $timeout = 60
    ): array {
        $payload = [
            'inputs'        => new \stdClass(),
            'query'         => $query,
            'response_mode' => 'blocking',
            'user'          => $userId,
        ];

        if (!empty($conversationId)) {
            $payload['conversation_id'] = $conversationId;
        }

        $response = Http::withToken($this->apiKey)
            ->timeout($timeout)
            ->post("{$this->baseUrl}/chat-messages", $payload);

        if ($response->failed()) {
            Log::error('Dify API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            $errorBody = $response->json();
            $errorMsg  = $errorBody['message'] ?? ('Lỗi từ AI service: HTTP ' . $response->status());

            throw new \RuntimeException($errorMsg, $response->status());
        }

        $data = $response->json();

        return [
            'answer'          => $data['answer'] ?? '',
            'conversation_id' => $data['conversation_id'] ?? '',
            'message_id'      => $data['id'] ?? '',
        ];
    }
}
