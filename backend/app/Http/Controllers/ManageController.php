<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\Course;

class ManageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    // Lấy tất cả user có role là 'admin'
    public function getAdmins(Request $request) {
        $perPage = $request->input('per_page', 10); // Số lượng admin trên mỗi trang, mặc định là 10
        $page = $request->input('page', 1); // Trang hiện tại, mặc định là trang 1

        $admins = User::where('role', 'admin')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'data' => $admins->items(),
            'pagination' => [
                'total' => $admins->total(), // Tổng số user
                'current_page' => $admins->currentPage(), // Trang hiện tại
                'last_page' => $admins->lastPage(), // Trang cuối cùng
                'per_page' => $admins->perPage(), // Số lượng user trên mỗi trang
            ],
        ]);
    }

    //Sửa tài khoản và mật khẩu
    public function updateUserAccount(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'required|min:8',
        ]);
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'fail',
                'message' => 'User không tồn tại',
            ], 404);
        }
        // Update email
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        // Lưu vào db
        $user->save();
        // In phản hồi
        return response()->json([
            'status' => 'success',
            'message' => 'Tài khoản đã được cập nhật thành công',
            'data' => $user,
        ]);
    }

    //Sửa, thêm thông tin nền tảng user role "admin"
    public function updateFoundationAccount(Request $request, $id){
        $request->validate([
            'name' => 'required|string|max:511',
            'biography' => 'nullable|string',
            'phone_number' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);
        $nameParts = explode(' ', trim($request->input('name')));
        $firstName = array_shift($nameParts); // Lấy phần tử đầu tiên làm first_name
        $lastName = implode(' ', $nameParts); // Các phần tử còn lại làm last_name

        $user = User::find($id);

        if (!$user) {
            return response()->json([
               "status" => "fail",
               "message" =>"Tài khoản không tồn tại",
            ],404);
        }

        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->biography = $request->input('biography', $user->biography);
        $user->phone_number = $request->input('phone_number', $user->phone_number);
        $user->address = $request->input('address', $user->address);
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $fileName); // Di chuyển file vào thư mục images
            $user->background_image = $fileName;
        }
        $user->save();
        return response()->json([
            'status' => 'success',
            'message' => $id ? 'Thông tin user đã được cập nhật thành công' : 'Tài khoản này không tồn tại',
            'data' => $user,
        ]);
    }

    //Sửa, thêm thông tin liên lạc
    public function updateContactInfo(Request $request, $id)
    {
        $request->validate([
            'facebook' => 'nullable|string|url',
            'linkedin' => 'nullable|string|url',
        ]);

        // Tìm user theo ID
        $user = User::find($id);

        if ($user) {
            $contactInfo = $user->contact_info ?: [];

            if ($request->has('facebook')) {
                $contactInfo['facebook'] = $request->input('facebook');
            }

            if ($request->has('linkedin')) {
                $contactInfo['linkedin'] = $request->input('linkedin');
            }

            $user->contact_info = $contactInfo;

            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Thông tin liên lạc đã được cập nhật',
                'data' => $user->contact_info,
            ]);
        }
        return response()->json(['status' => 'error', 'message' => 'User không tồn tại'], 404);
    }

    //Delete user follow id
    public function delUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.user_not_found'));
        }
        $user->save();
        $user->delete();

        return formatResponse(STATUS_OK, '', '', __('messages.user_soft_delete_success'));
    }

    //Report Payment
    public function getAdminRpPayment(Request $request)
    {
        $userId = Auth::id();
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $orders = Order::where('user_id', $userId)
            ->with(['orderItems.course'])
            ->paginate($perPage, ['*'], 'page', $page);

        $result = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'total_price' => $order->total_price, //Tổng tiền
                'admin_revenue' => $order->total_price, // Doanh thu admin
                'created_at' => $order->created_at, // Ngày có order
                'courses' => $order->orderItems->map(function ($item) {
                    return $item->course->name; // Tên khóa học
                }),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $result,
            'pagination' => [
                'total' => $orders->total(), // Tổng số đơn hàng
                'current_page' => $orders->currentPage(), // Trang hiện tại
                'last_page' => $orders->lastPage(), // Trang cuối cùng
                'per_page' => $orders->perPage(), // Số lượng đơn hàng trên mỗi trang
            ],
        ]);
    }

    public function getInstructorRp(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $instructors = User::where('role', 'instructor')
            ->with(['orders.orderItems.course'])
            ->paginate($perPage, ['*'], 'page', $page);

        $result = $instructors->map(function ($instructor) {
            return [
                'instructor_name' => $instructor->name,
                'instructor_email' => $instructor->email,
                'orders' => $instructor->orders->map(function ($order) {
                    return [
                        'order_id' => $order->id,
                        'total_price' => $order->total_price,
                        'created_at' => $order->created_at,
                        'courses' => $order->orderItems->map(function ($item) {
                            return $item->course->name;
                        }),
                    ];
                }),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $result,
            'pagination' => [
                'total' => $instructors->total(), // Tổng số instructor
                'current_page' => $instructors->currentPage(), // Trang hiện tại
                'last_page' => $instructors->lastPage(), // Trang cuối cùng
                'per_page' => $instructors->perPage(), // Số lượng instructor trên mỗi trang
            ],
        ]);
    }

    //Filter cho Report Admin
    /*private function applyDateFilter($query, Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate && $endDate) {
            // Đảm bảo định dạng ngày trước khi lọc
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
    }*/
    //Xóa báo cáo doanh thu
    public function delInstructorRp(Request $request, $orderId)
    {
        $userId = Auth::id();

        // Kiểm tra xem order có tồn tại và thuộc về admin hiện tại không
        $order = Order::where('id', $orderId)->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy đơn hàng.',
            ], 404);
        }

        // Xóa order
        $order->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã xóa báo cáo doanh thu thành công.',
        ]);
    }

    //Order history, Order detail
    public function getOrderHistory(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $orders = Order::with(['user', 'orderItems.course']) // Dùng Eager Loading để lấy dữ liệu người dùng và khóa học
        ->paginate($perPage, ['*'], 'page', $page);

        $result = $orders->getCollection()->map(function($order) {
            return [
                'id' => $order->id,
                'user_name' => $order->user->name, // Lấy tên người dùng
                'user_email' => $order->user->email, // Lấy email người dùng
                'courses' => $order->orderItems->map(function($item) {
                    return $item->course->name; // Lấy tên của khóa học
                }),
                'total_price' => $order->total_price, // Lấy tổng số tiền
                'payment_method' => $order->payment_method, // Lấy phương thức thanh toán
                'created_at' => $order->created_at->format('d-m-Y'), // Ngày tạo đơn hàng
            ];
        });

        // Trả về phản hồi JSON
        return response()->json([
            'status' => 'success',
            'data' => $result,
            'pagination' => [
                'total' => $orders->total(), // Tổng số đơn hàng
                'current_page' => $orders->currentPage(), // Trang hiện tại
                'last_page' => $orders->lastPage(), // Trang cuối cùng
                'per_page' => $orders->perPage(), // Số lượng đơn hàng trên mỗi trang
            ],
        ]);
    }

    //Lấy user role "instructor"
    public function getInstructor(Request $request) {
        $perPage = $request->input('per_page', 10); // Số lượng admin trên mỗi trang, mặc định là 10
        $page = $request->input('page', 1); // Trang hiện tại, mặc định là trang 1

        $instructors = User::where('role', 'instructor')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'data' => $instructors->items(),
            'pagination' => [
                'total' => $instructors->total(), // Tổng số user
                'current_page' => $instructors->currentPage(), // Trang hiện tại
                'last_page' => $instructors->lastPage(), // Trang cuối cùng
                'per_page' => $instructors->perPage(), // Số lượng user trên mỗi trang
            ],
        ]);
    }
    //Lấy user role "student"
    public function getStudent(Request $request) {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $studens = User::where('role', 'student')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'data' => $studens->items(),
            'pagination' => [
                'total' => $studens->total(),
                'current_page' => $studens->currentPage(),
                'last_page' => $studens->lastPage(),
                'per_page' => $studens->perPage(),
            ],
        ]);
    }


    // Wishlist
    public function addToWishlist(Request $request)
    {
        $userId = Auth::id();
        $courseId = $request->course_id;

        // Kiểm tra xem khóa học đã tồn tại trong wishlist chưa
        $exists = Wishlist::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Khóa học đã có trong wishlist'], 400);
        }
        if (!Course::where('id', $courseId)->exists()) {
            return response()->json(['message' => 'Khóa học không tồn tại'], 400);
        }

        // Tạo mới wishlist
        Wishlist::create([
            'user_id' => $userId,
            'course_id' => $courseId
        ]);

        return response()->json(['message' => 'Đã thêm khóa học vào wishlist'], 201);
    }
    public function getWishlist()
    {
        $userId = Auth::id();
        $wishlistItems = Wishlist::where('user_id', $userId)
            ->with(['course' => function ($query) {
                $query->select('id', 'title', 'thumbnail', 'price', 'created_by');
            }])
            ->get();

        return response()->json($wishlistItems);
    }



}
