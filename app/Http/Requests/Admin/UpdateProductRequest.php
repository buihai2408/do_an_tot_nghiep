<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'category_id' => 'sometimes|required|exists:categories,id',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|max:2048',
            'delete_images' => 'nullable',
            'primary_image_id' => 'nullable|integer',
            'base_price' => 'sometimes|required|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'has_ice_level' => 'nullable|boolean',
            'has_sugar_level' => 'nullable|boolean',
            'sizes' => 'nullable|array',
            'sizes.*.size_id' => 'required|exists:sizes,id',
            'sizes.*.price' => 'required|numeric|min:0',
            'topping_ids' => 'nullable|array',
            'topping_ids.*' => 'exists:toppings,id',
        ];
    }
}
