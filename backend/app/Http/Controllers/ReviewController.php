<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index($courseId)
    {
        $reviews = Review::where('course_id', $courseId)
            ->where('status', 'active')
            ->with('user')
            ->latest()
            ->get();

        return response()->json(['reviews' => $reviews], 200);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'rating' => 'required|integer|between:1,5',
                'comment' => 'required|string|max:255',
            ]);

            // Kiểm tra người dùng đã mua khóa học này chưa
            $orders = Order::where('user_id', Auth::id())->pluck('id');
            $isBought = OrderItem::whereIn('order_id', $orders)
                ->where('course_id', $request->course_id)
                ->exists();

            if (!$isBought) {
                return response()->json(['message' => 'Bạn phải mua khóa học này mới có thể đánh giá '], 403);
            }

            // Kiểm tra xem người dùng đã bình luận khóa học này chưa
            $isReviewed = Review::where('course_id', $request->course_id)
                ->where('user_id', Auth::id())
                ->exists();

            if ($isReviewed) {
                return response()->json(['message' => 'Bạn đã đánh giá khóa học này rồi'], 403);
            }

            $review = Review::create([
                'user_id' => Auth::id(),
                'course_id' => $request->course_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'status' => 'active', // Mặc định là active
                'created_by' => Auth::id(),
            ]);

            return response()->json(['message' => 'Đánh giá thành công', 'review' => $review], 201);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Có lỗi xảy ra: ' . $exception], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $review = Review::findOrFail($id);

            // Kiểm tra nếu người dùng không phải là người tạo review, không cho sửa
            if ($review->user_id !== Auth::id()) {
                return response()->json(['message' => 'Bạn chỉ có thể cập nhật đánh giá của mình '], 403);
            }

            $review->update($request->only(['rating', 'comment']));

            return response()->json(['message' => 'Cập nhật đánh giá thành công', 'review' => $review]);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Có lỗi xảy ra: ' . $exception], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $review = Review::findOrFail($id);

            if ($review->user_id !== Auth::id()) {
                return response()->json(['message' => 'Bạn chỉ có thể xóa đánh giá của mình '], 403);
            }

            $review->delete();

            return response()->json(['message' => ' Xóa đánh giá thành công ']);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Có lỗi xảy ra: ' . $exception], 500);
        }
    }

    public function restore($id)
    {
        try {
            $review = Review::withTrashed()->findOrFail($id);

            if ($review->user_id !== Auth::id()) {
                return response()->json(['message' => 'Bạn chỉ có thể khôi phục đánh giá của mình '], 403);
            }

            $review->restore();

            return response()->json(['message' => 'Khôi phục đánh giá thành công ']);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Có lỗi xảy ra: ' . $exception], 500);
        }
    }

    public function getAllReviews($courseId) // Lấy tất cả đánh giá bao gồm cả đã xóa
    {
        $reviews = Review::where('course_id', $courseId)
            ->with('user')->withTrashed()->latest()->get();

        return response()->json(['reviews' => $reviews], 200);
    }
}
