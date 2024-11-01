<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\Course;
use App\Models\OrderItem;

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
    public function getAdmin(Request $request) {
        $perPage = $request->input('per_page', 10); // Số lượng admin trên mỗi trang, mặc định là 10
        $page = $request->input('page', 1); // Trang hiện tại, mặc định là trang 1

        $admins = User::where('role', 'admin')->paginate($perPage, ['*'], 'page', $page);

        $pagination = [
            'total' => $admins->total(),
            'current_page' => $admins->currentPage(),
            'last_page' => $admins->lastPage(),
            'per_page' => $admins->perPage(),
        ];
        return formatResponse(STATUS_OK,[
            'data' => $admins,
            'pagination' => $pagination,
        ], '', __('messages.getUsers'));
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
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'

        ));
        $user->save();
        return formatResponse(STATUS_OK, $user, '', __('messages.updateUser'));
    }

    //Sửa, thêm thông tin nền tảng user role "admin"
    public function updateFoundationAccount(Request $request, $id){
        $request->validate([
            'name' => 'required|string|max:511',
            'biography' => 'nullable|string',
            'phone_number' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $nameParts = explode(' ', trim($request->input('name')));
        $firstName = array_shift($nameParts); // Lấy phần tử đầu tiên làm first_name
        $lastName = implode(' ', $nameParts); // Các phần tử còn lại làm last_name

        $user = User::find($id);

        if (!$user) {
            return formatResponse(STATUS_FAIL, null, '', __('messages.user_not_found'));
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

        return formatResponse(STATUS_OK, $user, '', __('messages.updateUser'));
    }

    //Sửa, thêm thông tin liên lạc
    public function updateContactInfo(Request $request, $id)
    {
        $request->validate([
            'facebook' => 'nullable|string|url',
            'linkedin' => 'nullable|string|url',
        ]);

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

            return formatResponse(STATUS_OK, $user->contact_info, '', __('messages.update_success'));
        }
        return formatResponse(CODE_NOT_FOUND, null, 404, __('messages.user_not_found'));
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

    //Report Payment (Đang sai, chưa hoàn thành)
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
        $pagination = [
            'total' => $orders->total(),
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'per_page' => $orders->perPage(),
        ];
        return formatResponse(STATUS_OK,[
            'data' => $result,
            'pagination' => $pagination,
        ], '', __('messages.getUsers'));
    }

    public function getInstructorRp(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $orderItems = OrderItem::with(['course'])
            ->paginate($perPage, ['*'], 'page', $page);

        $result = $orderItems->getCollection()->map(function ($item) {
            return [
                'order_id' => $item->id,
                'course_name' => $item->course->title,
                'instructor_name' => $item->course->creator->last_name.' ' . $item->course->creator->first_name ?? 'N/A',
                'total_price' => $item->price,
                'admin_revenue' => $item->price * 0.3,
                'instructor_email' => $item->course->creator->email ?? 'N/A',
                'created_date' => $item->created_at->format('d/m/Y'),
            ];
        });
        $pagination = [
            'total' => $orderItems->total(),
            'current_page' => $orderItems->currentPage(),
            'last_page' => $orderItems->lastPage(),
            'per_page' => $orderItems->perPage(),
        ];
        return formatResponse(STATUS_OK, [
            'data' => $result,
            'pagination' => $pagination,
        ], '', __('messages.getUsers'));
    }

    //Filter cho Report Admin (Đang sai, chưa hoàn thành)
    /*private function applyDateFilter($query, Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $query = Order::where('created_at');
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

        $orders = Order::with(['user', 'orderItems.course'])
            ->paginate($perPage, ['*'], 'page', $page);

        $result = $orders->getCollection()->map(function ($item) {
            return [
                'order_id' => $item->id,
                'user_name' => $item->user->last_name.' '.$item->user->first_name ?? 'N/A',
                'user_email' => $item->user->email ?? 'N/A',
                'course_name' => $item->course->title,
                'total_price' => $item->price,
                'payment_method' => $item->payment_method,
                'created_date' => $item->created_at->format('d/m/Y'),
            ];
        });
        $pagination = [
            'total' => $orders->total(),
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'per_page' => $orders->perPage(),
        ];
        return formatResponse(STATUS_OK, [
            'data' => $result,
            'pagination' => $pagination,
        ], '', __('messages.getUsers'));
    }
    public function getOrderDetail(Request $request){
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $orderItems = OrderItem::with(['course.user'])
            ->paginate($perPage, ['*'], 'page', $page);

        $result = $orderItems->getCollection()->map(function ($item) {
            return [
                'course_name' => $item->course->title,
                'instructor_name' => $item->course->user->name ?? 'N/A',
                'total_price' => $item->price,
                'admin_revenue' => $item->price * 0.3,
                'instructor_email' => $item->course->user->email ?? 'N/A',
                'created_date' => $item->created_at->format('d/m/Y'),
            ];
        });
        return formatResponse(STATUS_OK, $result, '', __('messages.getUsers'));
    }

    //Lấy user role "instructor"
    public function getInstructor(Request $request) {
        $perPage = $request->input('per_page', 10); // Số lượng admin trên mỗi trang, mặc định là 10
        $page = $request->input('page', 1); // Trang hiện tại, mặc định là trang 1

        $instructors = User::where('role', 'instructor')->paginate($perPage, ['*'], 'page', $page);
        $pagination = [
            'total' => $instructors->total(),
            'current_page' => $instructors->currentPage(),
            'last_page' => $instructors->lastPage(),
            'per_page' => $instructors->perPage(),
        ];
        return formatResponse(STATUS_OK,[
            'data'=> $instructors,
            'pagination' => $pagination,
        ], '', __('messages.getUsers'));
    }
    //Lấy user role "student"
    public function getStudent(Request $request) {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $studens = User::where('role', 'student')->paginate($perPage, ['*'], 'page', $page);
        $pagination = [
            'total' => $studens->total(),
            'current_page' => $studens->currentPage(),
            'last_page' => $studens->lastPage(),
            'per_page' => $studens->perPage(),
        ];
        return formatResponse(STATUS_OK,['data' => $studens, 'pagination' => $pagination], '', __('messages.getUsers'));
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

//        return response()->json(['message' => 'Đã thêm khóa học vào wishlist'], 201);
        return formatResponse(STATUS_OK, '', 201, __('messages.course_added_success'));
    }
    public function getWishlist()
    {
        $userId = Auth::id();
        $wishlistItems = Wishlist::where('user_id', $userId)
            ->with(['course' => function ($query) {
                $query->select('id', 'title', 'thumbnail', 'price', 'created_by');
            }])
            ->get();

        return formatResponse(STATUS_OK, $wishlistItems, '', __('messages.course_update_success'));
    }


}
