<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|max:2048',
            'base_price' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'sizes' => 'nullable|array',
            'sizes.*.size_id' => 'required|exists:sizes,id',
            'sizes.*.price' => 'required|numeric|min:0',
            'topping_ids' => 'nullable|array',
            'topping_ids.*' => 'exists:toppings,id',
        ];
    }
}
