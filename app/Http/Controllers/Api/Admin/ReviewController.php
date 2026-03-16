<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class ReviewController extends Controller
{
    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);
        return response()->json(['message' => 'Đã duyệt đánh giá!']);
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return response()->json(['message' => 'Đã xóa đánh giá!']);
    }
}
