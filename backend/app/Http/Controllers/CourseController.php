<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api',
            [
                'except' => [
                    'filterCourses'
                ]
            ]);
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
