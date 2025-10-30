<?php

namespace App\Payments\Contracts;

use App\Models\Order;

interface PaymentGateway
{
    // Khởi tạo thanh toán thông qua GateWay
    public function createPayment(Order $order): string;

    // xử lý callback khi thanh toán xong
    public function handleReturn(array $payload): array;

    // xử lý webhook tùy theo nhà cung cấp (momo, stripe, paypal, vnpay, ...)
    public function handleWebhook(array $payload, ?string $signature = null): array;
}
