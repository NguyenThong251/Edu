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
            // Lấy người dùng đã đăng nhập (hoặc mặc định là User 1 cho test)
            $user = Auth::user();

            // Lấy giỏ hàng của người dùng hoặc tạo mới nếu chưa có
            $cart = $this->getOrCreateUserCart($user);

            // Lấy danh sách các cart items
            $courses = $cart->cartItems()->with('course')->get()->map(function ($item) {
                return $item->course;
            });

            if ($courses->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.cart_empty')
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'courses' => $courses
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

            return response()->json([
                'status' => 'success',
                'message' => __('messages.course_added_success'),
                'courses' => $courses
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Trả về lỗi validate chi tiết cho developer
            return response()->json([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

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

    public function removeCourseFromCart($course_id)
    {
        try {
            // Lấy người dùng đã đăng nhập
            $user = Auth::user();

            // Lấy giỏ hàng của người dùng hoặc tạo mới nếu chưa có
            $cart = $this->getOrCreateUserCart($user);

            // Kiểm tra xem khóa học có trong giỏ hàng không
            $cartItem = $cart->cartItems()->where('course_id', $course_id)->first();

            if (!$cartItem) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.course_not_found_in_cart'),
                ], 404);
            }

            // Xóa khóa học khỏi giỏ hàng
            $cartItem->delete();

            // Lấy danh sách các khóa học còn lại trong giỏ hàng
            $courses = $cart->cartItems()->with('course')->get()->map(function ($item) {
                return $item->course;
            });

            return response()->json([
                'status' => 'success',
                'message' => __('messages.course_removed_success'),
                'courses' => $courses
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
}
