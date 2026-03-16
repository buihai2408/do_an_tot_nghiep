<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'size_id' => 'nullable|exists:sizes,id',
            'ice_level' => 'nullable|in:none,less,normal,more',
            'sugar_level' => 'nullable|in:none,less,normal,more',
            'topping_ids' => 'nullable|array',
            'topping_ids.*' => 'exists:toppings,id',
            'quantity' => 'nullable|integer|min:1|max:20',
        ];
    }
}
