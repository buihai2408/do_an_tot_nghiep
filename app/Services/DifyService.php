<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;





class DifyService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.dify.api_key', '');
        $this->baseUrl = config('services.dify.base_url', '');
    }




    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->baseUrl);
    }


    public function sendMessage(
        string $query,
        string $userId,
        ?string $conversationId = null,
        int $timeout = 60
    ): array {
        $payload = [
            'inputs' => new \stdClass(),
            'query' => $query,
            'response_mode' => 'blocking',
            'user' => $userId,
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
                'body' => $response->body(),
            ]);

            $errorBody = $response->json();
            $errorMsg = $errorBody['message'] ?? ('Lỗi từ AI service: HTTP ' . $response->status());

            throw new \RuntimeException($errorMsg, $response->status());
        }

        $data = $response->json();

        return [
            'answer' => $data['answer'] ?? '',
            'conversation_id' => $data['conversation_id'] ?? '',
            'message_id' => $data['id'] ?? '',
        ];
    }
}
