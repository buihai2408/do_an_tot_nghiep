<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $slug = Str::slug($data['name']);
        $data['slug'] = Product::withTrashed()->where('slug', $slug)->exists()
            ? $slug . '-' . uniqid()
            : $slug;

        $product = Product::create(collect($data)->except(['sizes', 'topping_ids', 'images'])->toArray());

        $this->handleImages($request, $product, true);

        if (!empty($data['sizes'])) {
            foreach ($data['sizes'] as $size) {
                $product->sizes()->attach($size['size_id'], ['price' => $size['price']]);
            }
        }

        if (!empty($data['topping_ids'])) {
            $product->toppings()->attach($data['topping_ids']);
        }

        return response()->json(['message' => 'Đã tạo sản phẩm!', 'product' => $product->load(['sizes', 'toppings', 'images'])], 201);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        if (isset($data['name'])) {
            $slug = Str::slug($data['name']);
            $data['slug'] = Product::withTrashed()->where('slug', $slug)->where('id', '!=', $product->id)->exists()
                ? $slug . '-' . uniqid()
                : $slug;
        }

        $product->update(collect($data)->except(['sizes', 'topping_ids', 'images', 'delete_images'])->toArray());

        if ($request->has('delete_images')) {
            $deleteIds = is_array($request->delete_images) ? $request->delete_images : json_decode($request->delete_images, true);
            if (!empty($deleteIds)) {
                $imagesToDelete = ProductImage::whereIn('id', $deleteIds)
                    ->where('product_id', $product->id)
                    ->get();
                foreach ($imagesToDelete as $img) {
                    Storage::disk('public')->delete($img->path);
                    $img->delete();
                }
            }
        }

        $this->handleImages($request, $product, false);

        if ($request->has('primary_image_id')) {
            $product->images()->update(['is_primary' => false]);
            $product->images()->where('id', $request->primary_image_id)->update(['is_primary' => true]);
        }

        if (isset($data['sizes'])) {
            $syncData = [];
            foreach ($data['sizes'] as $size) {
                $syncData[$size['size_id']] = ['price' => $size['price']];
            }
            $product->sizes()->sync($syncData);
        }

        if (isset($data['topping_ids'])) {
            $product->toppings()->sync($data['topping_ids']);
        }

        return response()->json(['message' => 'Đã cập nhật sản phẩm!', 'product' => $product->fresh(['sizes', 'toppings', 'images'])]);
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        $product->delete();
        return response()->json(['message' => 'Đã xóa sản phẩm!']);
    }

    private function handleImages(Request $request, Product $product, bool $isNew): void
    {
        if (!$request->hasFile('images')) return;

        $files = $request->file('images');
        $hasPrimary = $product->images()->where('is_primary', true)->exists();
        $order = $product->images()->max('sort_order') ?? 0;

        foreach ($files as $i => $file) {
            $path = $file->store('products', 'public');
            $order++;
            $product->images()->create([
                'path' => $path,
                'is_primary' => !$hasPrimary && $i === 0,
                'sort_order' => $order,
            ]);
            if ($i === 0 && !$hasPrimary) $hasPrimary = true;
        }
    }
}
