<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\OrderItem;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class CourseController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api',
    //         [
    //             'except' => [
    //                 'filterCourses'
    //             ]
    //         ]);
    // }
    
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $limit = $request->limit ?? 10;
         // Lấy tất cả các khóa học
         dd(Course::where('id', 6)->first()->sections());
         $newCourses = Course::orderBy('created_at', 'desc')->take($limit)->pluck('id')->toArray();

         // Lấy khóa học phổ biến nhất
         $popularCourses = OrderItem::select('course_id', DB::raw('COUNT(*) as purchase_count'))
             ->groupBy('course_id')
             ->orderByDesc('purchase_count')
             ->take($limit)
             ->pluck('course_id')
             ->toArray();
     
         // Lấy khóa học có đánh giá cao nhất
         $topRatedCourses = Course::select('courses.*')
             ->leftJoin('reviews', 'courses.id', '=', 'reviews.course_id')
             ->groupBy('courses.id')
             ->orderByRaw('AVG(reviews.rating) DESC')
             ->take($limit)
             ->pluck('id')
             ->toArray();
     
         // Lấy khóa học yêu thích nhất
         $favoriteCourses = Wishlist::select('course_id')
             ->groupBy('course_id')
             ->orderByRaw('COUNT(*) DESC')
             ->take($limit)
             ->pluck('course_id')
             ->toArray();
     
         // Lấy tất cả khóa học và thông tin cần thiết
         $courses = Course::with([
                 'category',
                 'level',
                 'creator:id,name', // Giả sử creator là user, lấy tên người tạo
                 'sections.lectures' // Lấy các lectures thông qua sections
             ])
             ->withCount(['sections.lectures as lectures_count']) // Đếm số lượng bài học
             ->withSum('sections.lectures as total_duration', 'duration') // Tính tổng thời gian
             ->limit($limit)
             ->get()
             ->map(function ($course) use ($newCourses, $popularCourses, $topRatedCourses, $favoriteCourses) {
                 $tag = 'none'; // Giá trị mặc định
     
                 // Xác định trạng thái của khóa học
                 if (in_array($course->id, $newCourses)) {
                     $tag = 'new';
                 } elseif (in_array($course->id, $topRatedCourses)) {
                     $tag = 'top_rated';
                 } elseif (in_array($course->id, $popularCourses)) {
                     $tag = 'popular';
                 } elseif (in_array($course->id, $favoriteCourses)) {
                     $tag = 'favorite';
                 }
     
                 return [
                     'id' => $course->id,
                     'name' => $course->name,
                     'category' => $course->category,
                     'level' => $course->level,
                     'creator' => $course->creator->name ?? null, // Tên người tạo
                     'lectures_count' => $course->lectures_count, // Số lượng bài học
                     'total_duration' => $course->total_duration, // Tổng thời gian của khóa học
                     'tag' => $tag, // Trả về thuộc tính status
                 ];
             });
     
        $currentPage = $request->get('page', 1);
        $total = $courses->count();

        $courses = $courses->slice(($currentPage - 1) * $perPage, $perPage)->values(); // Lấy các khóa học cho trang hiện tại
        
        // Phân trang
        $paginated = [
            'data' => $courses,
            'current_page' => $currentPage,
            'last_page' => (int) ceil($total / $perPage),
            'per_page' => $perPage,
            'total' => $total,
        ];

        return formatResponse(STATUS_OK, $paginated, '', __('messages.course_fetch_success'));
    }

    public function uploadThumbnail(Request $request)
    {
        // Tải lên tệp hình ảnh
        $path = $request->file('thumbnail')->storePublicly('course-thumbnails');
        if (!$path) {
            return '';
        }

        // Trả về đường dẫn hình ảnh
        $imageUrl = env('URL_IMAGE_S3') . $path;
        return $imageUrl;
    }
    public function deleteThumbnail($thumbnailUrl)
    {
        $currentFilePath = str_replace(env('URL_IMAGE_S3'), '', $thumbnailUrl);

        // Kiểm tra xem tệp có tồn tại trên S3 không
        if (Storage::disk('s3')->exists($currentFilePath)) {
            // Xóa tệp
            Storage::disk('s3')->delete($currentFilePath);
            return formatResponse(STATUS_OK, '', '', __('messages.thumbnail_delete_success'));
        }

        return formatResponse(STATUS_FAIL, '', '', __('messages.thumbnail_not_found'));
    }


    // Tạo mới một khóa học
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'level_id' => 'required|exists:course_levels,id',
            'title' => 'required|string|max:100|unique:courses',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'price' => 'required|numeric',
            'type_sale' => 'required|in:percent,price',
            'sale_value' => 'nullable|numeric',
            'status' => 'required|in:active,inactive',
        ], [
            'title.required' => __('messages.title_required'),
            'title.unique' => __('messages.title_unique'),
            'category_id.required' => __('messages.category_id_required'),
            'category_id.exists' => __('messages.category_id_invalid'),
            'level_id.required' => __('messages.level_id_required'),
            'level_id.exists' => __('messages.level_id_invalid'),
            'thumbnail.required' => __('messages.thumbnail_required'),
            'thumbnail.image' => __('messages.thumbnail_image'), 
            'thumbnail.mimes' => __('messages.thumbnail_mimes'), 
            'thumbnail.max' => __('messages.thumbnail_max'), 
            'price.required' => __('messages.price_required'),
            'type_sale.required' => __('messages.type_sale_required'),
            'status.required' => __('messages.status_required'),
        ]);

        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), __('messages.validation_error'));
        }
        $thumbnailPath = $this->uploadThumbnail($request);
        $course = new Course();
        $course->fill($request->all());
        $course->thumbnail=$thumbnailPath;
        $course->created_by = auth()->id();
        $course->save();

        return formatResponse(STATUS_OK, $course, '', __('messages.course_create_success'));
    }

    // Hiển thị một khóa học cụ thể
    public function show($id)
    {
        $course = Course::with('category', 'level')->find($id);
        if (!$course) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.course_not_found'));
        }

        return formatResponse(STATUS_OK, $course, '', __('messages.course_detail_success'));
    }

    // Cập nhật thông tin khóa học
    public function update(Request $request, $id)
    {
        $course = Course::find($id);
        if (!$course) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.course_not_found'));
        }
        $user = auth()->user();

        if ($user->role === 'instructor') {
            // Kiểm tra xem user có phải là người tạo khóa học không
            if ($course->created_by !== $user->id) {
                return formatResponse(STATUS_FAIL, '', '', __('messages.not_your_course'));
            }
        }
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'level_id' => 'required|exists:course_levels,id',
            'title' => [
                'required',
                'string',
                'max:100',
                Rule::unique('courses')->ignore($course->id),
            ],
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric',
            'type_sale' => 'required|in:percent,price',
            'sale_value' => 'nullable|numeric',
            'status' => 'required|in:active,inactive',
        ], [
            'title.required' => __('messages.title_required'),
            'title.unique' => __('messages.title_unique'),
            'category_id.required' => __('messages.category_id_required'),
            'category_id.exists' => __('messages.category_id_invalid'),
            'thumbnail.image' => __('messages.thumbnail_image'), 
            'thumbnail.mimes' => __('messages.thumbnail_mimes'), 
            'thumbnail.max' => __('messages.thumbnail_max'), 
            'level_id.required' => __('messages.level_id_required'),
            'level_id.exists' => __('messages.level_id_invalid'),
            'price.required' => __('messages.price_required'),
            'type_sale.required' => __('messages.type_sale_required'),
            'status.required' => __('messages.status_required'),
        ]);

        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), __('messages.validation_error'));
        }
        $thumbnail=$course->thumbnail;
        $course->fill($request->all());
        $course->thumbnail=$thumbnail;
        if($request->thumbnail){
            if($course->thumbnail){
                $this->deleteThumbnail($course->thumbnail);
            }
            $thumbnailPath = $this->uploadThumbnail($request);
            $course->thumbnail=$thumbnailPath;
        }
        $course->updated_by = auth()->id();
        $course->save();

        return formatResponse(STATUS_OK, $course, '', __('messages.course_update_success'));
    }

    // Xóa mềm khóa học
    public function destroy($id)
    {
        $course = Course::find($id);
        if (!$course) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.course_not_found'));
        }
        $user = auth()->user();

        if ($user->role === 'instructor') {
            // Kiểm tra xem user có phải là người tạo khóa học không
            if ($course->created_by !== $user->id) {
                return formatResponse(STATUS_FAIL, '', '', __('messages.not_your_course'));
            }
        }
        $course->deleted_by = auth()->id();
        $course->save();

        $course->delete();

        return formatResponse(STATUS_OK, '', '', __('messages.course_soft_delete_success'));
    }

    // Khôi phục khóa học đã bị xóa mềm
    public function restore($id)
    {
        $course = Course::onlyTrashed()->find($id);

        if (!$course) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.course_not_found'));
        }

        $user = auth()->user();

        if ($user->role === 'instructor') {
            // Kiểm tra xem user có phải là người tạo khóa học không
            if ($course->created_by !== $user->id) {
                return formatResponse(STATUS_FAIL, '', '', __('messages.not_your_course'));
            }
        }
        $course->deleted_by = null;
        $course->save();

        $course->restore();

        return formatResponse(STATUS_OK, '', '', __('messages.course_restore_success'));
    }

    // Xóa cứng khóa học
    public function forceDelete($id)
    {
        $course = Course::onlyTrashed()->find($id);
        if (!$course) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.course_not_found'));
        }
        $user = auth()->user();

        if ($user->role === 'instructor') {
            // Kiểm tra xem user có phải là người tạo khóa học không
            if ($course->created_by !== $user->id) {
                return formatResponse(STATUS_FAIL, '', '', __('messages.not_your_course'));
            }
        }
        if($course->thumbnail){
            $this->deleteThumbnail($course->thumbnail);
        }
        $course->forceDelete();

        return formatResponse(STATUS_OK, '', '', __('messages.course_force_delete_success'));
    }

    public function getPopularCourses(Request $request)
    {
        // Kiểm tra xem có giới hạn không, nếu không, mặc định là 10
        $limit = $request->limit ?? 10;

        // Lấy các khóa học được mua nhiều nhất từ bảng order_items
        $popularCourses = OrderItem::select('course_id', DB::raw('COUNT(*) as purchase_count'))
            ->groupBy('course_id')
            ->orderByDesc('purchase_count')
            ->limit($limit)
            ->get();

        if ($popularCourses->isEmpty()) {
            // Nếu không có khóa học nào phổ biến
            return formatResponse(STATUS_FAIL, '', '', __('messages.no_popular_courses'));
        }

        // Lấy chi tiết các khóa học cùng với category và level dựa trên course_id đã gom nhóm
        $courses = Course::with('category', 'level')
            ->whereIn('id', $popularCourses->pluck('course_id'))
            ->get();

        return formatResponse(STATUS_OK, $courses, '', __('messages.popular_courses_found'));
    }
    public function getNewCourses(Request $request)
    {
        // Kiểm tra xem có giới hạn không, nếu không, mặc định là 10
        $limit = $request->limit ?? 10;

        // Lấy các khóa học mới nhất theo ngày tạo
        $courses = Course::with('category', 'level')
            ->orderBy('created_at', 'desc') // Sắp xếp theo ngày tạo giảm dần
            ->limit($limit)
            ->get();

        if ($courses->isEmpty()) {
            // Nếu không có khóa học nào mới
            return formatResponse(STATUS_FAIL, '', '', __('messages.no_new_courses'));
        }

        return formatResponse(STATUS_OK, $courses, '', __('messages.new_courses_found'));
    }
    public function getTopRatedCourses(Request $request)
    {
        // Kiểm tra xem có giới hạn không, nếu không, mặc định là 10
        $limit = $request->limit ?? 10;

        // Lấy các khóa học hàng đầu theo rating trung bình
        $courses = Course::select('courses.*', 
            DB::raw('AVG(reviews.rating) as average_rating'),
            DB::raw('COUNT(reviews.id) as review_count'))
        ->leftJoin('reviews', 'courses.id', '=', 'reviews.course_id')
        ->groupBy('courses.id')
        ->orderByRaw('AVG(reviews.rating) DESC')
        ->limit($limit)
        ->with('category', 'level')
        ->get();

        if ($courses->isEmpty()) {
            // Nếu không có khóa học nào
            return formatResponse(STATUS_FAIL, '', '', __('messages.no_top_rated_courses'));
        }

        return formatResponse(STATUS_OK, $courses, '', __('messages.top_rated_courses_found'));
    }



    public function filterCourses(Request $request)
    {
        $category_id = $request->input('category_id');
        $title = $request->input('title');
        $min_price = $request->input('min_price');
        $max_price = $request->input('max_price');
        $status = $request->input('status');
        $type_sale = $request->input('type_sale');
        $rating = $request->input('rating');
        $duration_range = $request->input('duration_range');


        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        $sort_by = $request->input('sort_by', 'created_at');
        $sort_order = $request->input('sort_order', 'desc');

        $query = Course::with('reviews');
        if ($category_id) {
            $categoryIds = explode(',', $category_id);
            $query->whereIn('category_id', $categoryIds);
        }
        if ($title) {
            $query->where('title', 'like', '%' . $title . '%');
        }
        if ($min_price) {
            $query->where('price', '>=', $min_price);
        }
        if ($max_price) {
            $query->where('price', '<=', $max_price);
        }
        if ($status) {
            $query->where('status', $status);
        }

        if ($rating) {
            $query->whereHas('reviews', function ($q) use ($rating) {
                $q->havingRaw('ROUND(AVG(rating),0) = ?', [$rating]);
            });
        }

        if ($duration_range) {
            $query->whereHas('sections.lectures', function ($q) use ($duration_range) {
                switch ($duration_range) {
                    case '0-2':
                        $q->havingRaw('SUM(duration) <= 120');
                        break;
                    case '3-5':
                        $q->havingRaw('SUM(duration) BETWEEN 180 AND 300');
                        break;
                    case '6-12':
                        $q->havingRaw('SUM(duration) BETWEEN 360 AND 720');
                        break;
                    case '12+':
                        $q->havingRaw('SUM(duration) > 720');
                        break;
                }
            });
        }

        $query->orderBy($sort_by, $sort_order);
        $courses = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'status' => 'success',
            'data' => $courses->items(),
            'pagination' => [
                'total' => $courses->total(),
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
            ],
        ]);
    }
}
