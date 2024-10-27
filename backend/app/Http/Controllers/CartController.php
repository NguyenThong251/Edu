<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function getCoursesFromCart()
    {
        try {
            // Lấy người dùng đã đăng nhập
            $user = Auth::user();

            // Lấy giỏ hàng của người dùng hoặc tạo mới nếu chưa có
            $cart = $this->getOrCreateUserCart($user);

            // Lấy danh sách các cart items
            $courses = $cart->cartItems()->with('course')->get()->map(function ($item) {
                return $item->course;
            });

            if ($courses->isEmpty()) {
                return $this->formatResponse('success', __('messages.cart_empty'), [], 200);
            }

            return $this->formatResponse('success', __('messages.cart_items_fetched'), $courses, 200);
        } catch (\Exception $e) {
            return $this->formatResponse('error', $e->getMessage(), null, $e->getCode() ?: 500);
        }
    }

    public function addCourseToCart(Request $request)
    {
        try {
            // Xác thực dữ liệu đầu vào
            $validatedData = $request->validate([
                'course_id' => 'required|exists:courses,id',
            ], [
                'course_id.required' => 'DEV: The course_id is required and cannot be empty.',
                'course_id.exists' => 'DEV: The specified course does not exist.',
            ]);

            // Lấy người dùng đã đăng nhập
            $user = Auth::user();

            // Lấy giỏ hàng của người dùng hoặc tạo mới nếu chưa có
            $cart = $this->getOrCreateUserCart($user);

            // Sử dụng transaction
            DB::beginTransaction();

            // Kiểm tra xem khóa học đã có trong giỏ hàng chưa
            $existingCartItem = $cart->cartItems()->where('course_id', $validatedData['course_id'])->first();
            if ($existingCartItem) {
                throw new \Exception(__('messages.course_already_in_cart'), 400);
            }

            // Thêm khóa học vào giỏ hàng
            $course = Course::find($validatedData['course_id']);
            $cart->cartItems()->create([
                'course_id' => $course->id,
                'price' => $course->price,
            ]);

            DB::commit();

            // Lấy danh sách khóa học
            $courses = $cart->cartItems()->with('course')->get()->map(function ($item) {
                return $item->course;
            });

            return $this->formatResponse('success', __('messages.course_added_success'), $courses, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->formatResponse('error', 'Validation Error', $e->errors(), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->formatResponse('error', $e->getMessage(), null, $e->getCode() ?: 500);
        }
    }

    public function removeCourseFromCart($course_id)
    {
        try {
            $user = Auth::user();
            $cart = $this->getOrCreateUserCart($user);
            $cartItem = $cart->cartItems()->where('course_id', $course_id)->first();

            if (!$cartItem) {
                return $this->formatResponse('error', __('messages.course_not_found_in_cart'), null, 404);
            }

            // Xóa khóa học khỏi giỏ hàng
            $cartItem->delete();

            // Lấy danh sách khóa học còn lại trong giỏ hàng
            $courses = $cart->cartItems()->with('course')->get()->map(function ($item) {
                return $item->course;
            });

            return $this->formatResponse('success', __('messages.course_removed_success'), $courses, 200);
        } catch (\Exception $e) {
            return $this->formatResponse('error', $e->getMessage(), null, $e->getCode() ?: 500);
        }
    }

    public function clearCart()
    {
        try {
            // Lấy người dùng đã đăng nhập
            $user = Auth::user();

            // Lấy giỏ hàng của người dùng hoặc tạo mới nếu chưa có
            $cart = $this->getOrCreateUserCart($user);

            // Xóa tất cả các cart items trong giỏ hàng
            $cart->cartItems()->delete();

            return response()->json([
                'status' => 'success',
                'message' => __('messages.cart_cleared')
            ], 200);
        } catch (\Exception $e) {
            // Kiểm tra mã lỗi và trả về mã phù hợp
            $statusCode = (is_int($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600)
                ? $e->getCode()
                : 500;

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    private function getOrCreateUserCart($user)
    {
        // Tạo giỏ hàng nếu chưa tồn tại
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    private function formatResponse($status, $message, $data = null, $code = 200)
    {
        $response = [
            'status' => $status,
            'message' => $message,
        ];

        if ($data) {
            // Tạo đối tượng mới chứa dữ liệu cần thiết
            $formattedData = $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'thumbnail' => $item->thumbnail,
                    'title' => $item->title,
                    'category_name' => $item->category->name,
                    'creator' => $item->creator ? trim($item->creator->last_name . ' ' . $item->creator->first_name) : null,
                    'old_price' => round($item->price),
                    'current_price' => $item->type_sale === 'percent'
                        ? round($item->price - ($item->price * $item->sale_value / 100))
                        : round($item->price - $item->sale_value),
                    'average_rating' => round($item->reviews->avg('rating'), 1),
                    'reviews_count' => $item->reviews->count(),
                    'total_duration' => round($item->sections->reduce(function ($carry, $section) {
                        return $carry + $section->lectures->sum('duration');
                    }, 0) / 3600, 1),
                    'lectures_count' => $item->sections->reduce(function ($carry, $section) {
                        return $carry + $section->lectures->count();
                    }, 0),
                    'level' => $item->level->name,
                ];
            });

            // Gán dữ liệu đã format vào response
            $response['data'] = $formattedData;
        }

        return response()->json($response, $code);
    }
}
