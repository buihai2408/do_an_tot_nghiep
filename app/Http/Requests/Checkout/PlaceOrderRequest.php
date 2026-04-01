<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'order_type' => 'required|in:delivery,pickup',
            'payment_method' => 'required|in:cod,bank_transfer',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'shipping_address' => 'required_if:order_type,delivery|nullable|string',
            'coupon_code' => 'nullable|string',
            'points_used' => 'nullable|integer|min:0',
            'note' => 'nullable|string|max:500',
            'save_address' => 'nullable|boolean',
            'address_label' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Vui lòng nhập tên người nhận.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại.',
            'shipping_address.required_if' => 'Vui lòng nhập địa chỉ giao hàng.',
        ];
    }
}
