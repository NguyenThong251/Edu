<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Wishlist;
use App\Models\Review;
use App\Models\Lecture;
use App\Models\Order;
use Carbon\Carbon;
use App\Http\Controllers\StudyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Tymon\JWTAuth\Facades\JWTAuth;


class DataTrainController extends Controller
{
    public function search(Request $request)
    {
        // Lấy danh sách các khóa học mới, phổ biến, đánh giá cao và yêu thích
        $limitTag = 10;
        $newCourses = Course::orderBy('created_at', 'desc')->take($limitTag)->pluck('id')->toArray();
        $popularCourses = OrderItem::select('course_id', DB::raw('COUNT(*) as purchase_count'))
            ->groupBy('course_id')->orderByDesc('purchase_count')->take($limitTag)->pluck('course_id')->toArray();
        $topRatedCourses = Course::leftJoin('reviews', 'courses.id', '=', 'reviews.course_id')
            ->select('courses.id')->groupBy('courses.id')
            ->orderByRaw('AVG(reviews.rating) DESC')->take($limitTag)->pluck('id')->toArray();
        $favoriteCourses = Wishlist::select('course_id')
            ->groupBy('course_id')->orderByRaw('COUNT(*) DESC')->take($limitTag)->pluck('course_id')->toArray();

        // Fetch all courses with necessary relationships
        $courses = Course::with(['category', 'level', 'creator:id,last_name,first_name', 'sections.lectures', 'reviews'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('courses.status', 'active')
            ->get();

        // Transform data into desired format
        $transformedCourses = $courses->map(function ($course) use ($newCourses, $popularCourses, $topRatedCourses, $favoriteCourses) {
            $tag = 'none';
            if (in_array($course->id, $newCourses)) {
                $tag = __('messages.tag_new');
            } elseif (in_array($course->id, $topRatedCourses)) {
                $tag = __('messages.tag_top_rated');
            } elseif (in_array($course->id, $popularCourses)) {
                $tag = __('messages.tag_popular');
            } elseif (in_array($course->id, $favoriteCourses)) {
                $tag = __('messages.tag_favorite');
            }

            return [
                'id' => $course->id,
                'title' => $course->title,
                'old_price' => round($course->price, 0),
                'current_price' => round($course->price - ($course->sale_value ?? 0), 0),
                'thumbnail' => $course->thumbnail,
                'description' => $course->description,
                'short_description' => $course->short_description,
                'embedding' => $course->embedding,
                'level' => $course->level->name ?? '',
                'creator' => $course->creator ? trim($course->creator->last_name . ' ' . $course->creator->first_name) : '',
                'lectures_count' => $course->sections->sum(fn($s) => $s->lectures->count()),
                'total_duration' => round($course->sections->sum(fn($s) => $s->lectures->sum('duration')) / 3600, 1),
                'rating_avg' => round($course->reviews_avg_rating ?? 0, 2),
                'reviews_count' => $course->reviews_count ?? 0,
                'status' => $course->status,
                'tag' => $tag,
            ];
        });

        return response()->json($transformedCourses);
    }

}
