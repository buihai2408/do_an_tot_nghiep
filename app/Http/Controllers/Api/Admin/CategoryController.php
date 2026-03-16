<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create($data);
        return response()->json(['message' => 'Đã tạo danh mục!', 'category' => $category], 201);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('image')) {
            if ($category->image) Storage::disk('public')->delete($category->image);
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);
        return response()->json(['message' => 'Đã cập nhật danh mục!', 'category' => $category]);
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return response()->json(['message' => 'Không thể xóa danh mục có sản phẩm!'], 422);
        }
        if ($category->image) Storage::disk('public')->delete($category->image);
        $category->delete();
        return response()->json(['message' => 'Đã xóa danh mục!']);
    }
}
