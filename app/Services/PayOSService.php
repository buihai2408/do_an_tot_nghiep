<?php

namespace App\Services;

use App\Models\Order;
use PayOS\PayOS;
use PayOS\Models\V2\PaymentRequests\CreatePaymentLinkRequest;

class PayOSService
{
    protected PayOS $payOS;

    public function __construct()
    {
        $this->payOS = new PayOS(
            clientId: config('services.payos.client_id'),
            apiKey: config('services.payos.api_key'),
            checksumKey: config('services.payos.checksum_key'),
        );
    }

    public function createPaymentLink(Order $order, string $returnUrl, string $cancelUrl): string
    {
        $orderCode = (int) preg_replace('/\D/', '', $order->order_number);
        if ($orderCode === 0) {
            $orderCode = $order->id + 1000000;
        }

        $description = mb_substr("DH {$order->order_number}", 0, 25);

        $paymentData = new CreatePaymentLinkRequest(
            orderCode: $orderCode,
            amount: (int) $order->total,
            description: $description,
            returnUrl: $returnUrl,
            cancelUrl: $cancelUrl,
        );

        $result = $this->payOS->paymentRequests->create($paymentData);

        return $result->checkoutUrl;
    }

    public function verifyWebhook(array $payload): object
    {
        return $this->payOS->webhooks->verify($payload);
    }

    public function getPaymentInfo(int $orderCode): object
    {
        return $this->payOS->paymentRequests->get(strval($orderCode));
    }
}
