<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Section;
use App\Models\Lecture;
use App\Models\ProgressLecture;
use App\Models\ProgressQuiz;
use Illuminate\Support\Facades\DB;


class ManangeStudentController extends Controller
{
    private function formatDuration($seconds)
    {
        if ($seconds < 60) {
            return "{$seconds} giây"; // Dưới 1 phút
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60; // Tính số giây lẻ

        if ($minutes < 60) {
            return $remainingSeconds > 0
                ? "{$minutes} phút {$remainingSeconds} giây" // Hiển thị phút và giây lẻ nếu có
                : "{$minutes} phút"; // Chỉ hiển thị phút
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes > 0 || $remainingSeconds > 0) {
            return "{$hours} giờ"
                . ($remainingMinutes > 0 ? " {$remainingMinutes} phút" : ""); // Chỉ thêm giây nếu khác 0
        }

        return "{$hours} giờ"; // Chỉ hiển thị giờ nếu không có phút và giây
    }
    public function getStudentsByTeacher(Request $request)
    {
        // Lấy ID của giáo viên đang đăng nhập
        $teacherId = auth()->user()->id;

        // Lấy tham số các bộ lọc
        $courseId = $request->get('course_id'); // Lọc theo ID khóa học
        $keyword = $request->get('keyword'); // Từ khóa để lọc theo email hoặc full_name
        $progressMin = (float) ($request->get('progress_min') && !empty($request->get('progress_min')) 
        ? $request->get('progress_min') && !empty($request->get('progress_min')) : 0); // Tiến độ tối đa
        $progressMax = (float) ($request->get('progress_max') && !empty($request->get('progress_max')) 
        ? $request->get('progress_max') && !empty($request->get('progress_max')) : 100); // Tiến độ tối đa
        $purchaseDateMin = $request->get('purchase_date_min'); // Ngày mua tối thiểu
        $purchaseDateMax = $request->get('purchase_date_max'); // Ngày mua tối đa
        $sortColumn = $request->get('sort_column', 'course_purchase_date'); // Mặc định sắp xếp theo ngày mua
        $sortDirection = strtolower($request->get('sort_direction', 'desc'));
        // return $sortProgress;

        // Tạo query để lấy danh sách học viên
        $query = User::select(
            'users.id as student_id',
            'users.first_name as student_first_name',
            'users.last_name as student_last_name',
            'users.email as student_email',
            'users.phone_number as student_phone_number',
            'courses.title as course_title',
            'courses.id as course_id',
            'orders.payment_status',
            'orders.created_at as course_purchase_date'
        )
            ->join('orders', 'users.id', '=', 'orders.user_id') // Liên kết học viên với đơn hàng
            ->join('order_items', 'orders.id', '=', 'order_items.order_id') // Liên kết đơn hàng với order item
            ->join('courses', 'order_items.course_id', '=', 'courses.id') // Liên kết order item với khóa học
            ->where('courses.created_by', $teacherId) // Khóa học do giáo viên tạo
            ->where('orders.payment_status', 'paid'); // Lọc thanh toán đã hoàn tất

        // Lọc theo ID khóa học
        if ($courseId) {
            $query->where('courses.id', $courseId);
        }

        // Lọc theo keyword (email hoặc full_name)
        if ($keyword) {
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->whereRaw("CONCAT(users.first_name, ' ', users.last_name) LIKE ?", ["%$keyword%"])
                        ->orWhere('users.email', 'LIKE', "%$keyword%");
            });
        }

        // Lọc theo ngày mua tối thiểu và tối đa
        if ($purchaseDateMin) {
            $query->where('orders.created_at', '>=', $purchaseDateMin);
        }
        if ($purchaseDateMax) {
            $query->where('orders.created_at', '<=', $purchaseDateMax);
        }

        // Lấy tất cả dữ liệu từ query
        $students = $query->get();

        // Transform và tính toán progress
        $transformedStudents = $students->map(function ($student) {
            $totalQuizzes = Quiz::where('status', 'active')
                ->whereIn('section_id', Section::where('course_id', $student->course_id)->pluck('id'))
                ->count();

            $totalLectures = $totalQuizzes + Lecture::where('status', 'active')
                ->whereIn('section_id', Section::where('course_id', $student->course_id)->pluck('id'))
                ->count();

            $completedQuizzes = ProgressQuiz::where('user_id', $student->student_id)
                ->where('percent', '>=', 100) // Chỉ tính các quiz hoàn thành
                ->whereIn('quiz_id', Quiz::where('status', 'active')
                    ->whereIn('section_id', Section::where('course_id', $student->course_id)->pluck('id'))
                    ->pluck('id'))
                ->count();

            $completedLectures = $completedQuizzes + ProgressLecture::where('user_id', $student->student_id)
                ->where('percent', '>=', 100) // Chỉ tính các bài giảng hoàn thành
                ->whereIn('lecture_id', Lecture::where('status', 'active')
                    ->whereIn('section_id', Section::where('course_id', $student->course_id)->pluck('id'))
                    ->pluck('id'))
                ->count();

            $progressPercent = $totalLectures > 0
                ? round(($completedLectures / $totalLectures) * 100, 2)
                : 0;

            return [
                'student_id' => $student->student_id,
                'student_full_name' => trim($student->student_last_name . ' ' . $student->student_first_name),
                'student_email' => $student->student_email,
                'student_phone_number' => $student->student_phone_number,
                'course_id' => $student->course_id,
                'course_title' => $student->course_title,
                'course_payment_status' => $student->payment_status,
                'course_purchase_date' => $student->course_purchase_date,
                'total_lectures' => $totalLectures,
                'completed_lectures' => $completedLectures,
                'progress_percent' => $progressPercent,
            ];
        })->filter(function ($student) use ($progressMin, $progressMax) {
            // Lọc theo progress_min và progress_max
            return $student['progress_percent'] >= $progressMin && $student['progress_percent'] <= $progressMax;
        })->values();

        // Sắp xếp theo progress nếu cần
        if ($sortColumn && $sortDirection) {
            $transformedStudents = $transformedStudents->sortBy(function ($student) use ($sortColumn) {
                return $student[$sortColumn];
            }, SORT_REGULAR, $sortDirection === 'desc')->values();
        }
        // Phân trang thủ công
        $perPage = (int) $request->get('per_page', 10);
        $page = (int) $request->get('page', 1);
        $total = $transformedStudents->count();
        $paginatedStudents = $transformedStudents->forPage($page, $perPage);

        // Tạo LengthAwarePaginator
        $pagination = new LengthAwarePaginator(
            $paginatedStudents,
            $total,
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        // Trả về kết quả
        return formatResponse(
            STATUS_OK,
            $pagination,
            '',
            __('messages.student_fetch_success')
        );
    }

    public function getProgressOfStudent(Request $request)
    {

        $userId=$request->user_id;
        $courseId=$request->course_id;
        $contentKeyword=$request->keyword;
        $sections = Section::where('course_id', $courseId)
            ->where('status', 'active')
            ->orderBy('order', 'asc')
            ->with([
                'lectures' => function ($query) use ($userId) {
                    $query->where('status', 'active')
                        ->select('id', 'section_id', 'title', 'order', 'duration', 'type')
                        ->orderBy('order', 'asc')
                        ->addSelect([
                            'content_section_type' => DB::raw('"lecture"'),
                        ])
                        ->with(['progress' => function ($progressQuery) use ($userId) {
                            $progressQuery->select('lecture_id', 'learned', 'percent')
                                ->where('status', 'active')
                                ->where('user_id', $userId);
                        }]);
                },
                'quizzes' => function ($query) use ($userId) {
                    $query->where('status', 'active')
                        ->select('id', 'section_id', 'title', 'order')
                        ->orderBy('order', 'asc')
                        ->addSelect([
                            'content_section_type' => DB::raw('"quiz"'),
                        ])
                        ->withCount('questions') // Đếm số lượng câu hỏi
                        ->with(['progress' => function ($progressQuery) use ($userId) {
                            $progressQuery->select('quiz_id', 'questions_done', 'percent')
                                ->where('status', 'active')
                                ->where('user_id', $userId);
                        }]);
                }
            ])->get();

        $sections = $sections->map(function ($section) {
            $sectionContent = collect($section->lectures)
                ->merge($section->quizzes)
                ->sortBy('order')
                ->values()
                ->map(function ($item) {
                    $progress = $item->progress->first() ?? null;

                    if ($item instanceof Lecture) {
                        $durationDisplay = null;

                        if ($item->type === 'video') {
                            $durationDisplay = $this->formatDuration($item->duration); // Chuyển đổi thời gian
                        } elseif ($item->type === 'file') {
                            $durationDisplay = $item->duration . " trang"; // Gắn thêm "trang"
                        }

                        return [
                            'id' => $item->id,
                            'title' => $item->title,
                            'order' => $item->order,
                            'content_section_type' => 'lecture',
                            'type' => $item->type,
                            'duration' => $item->duration, // Giữ nguyên duration
                            'duration_display' => $durationDisplay, // Tùy theo type
                            'learned' => $progress && isset($progress->learned) ? $progress->learned : null,
                            'percent' => $progress && isset($progress->percent) ? $progress->percent : null,
                        ];
                    } elseif ($item instanceof Quiz) {
                        return [
                            'id' => $item->id,
                            'title' => $item->title,
                            'order' => $item->order,
                            'content_section_type' => 'quiz',
                            'total_question_count' => $item->questions_count,
                            'percent' => $progress && isset($progress->percent) ? $progress->percent : null,
                            'questions_done' => $progress && isset($progress->questions_done) ? $progress->questions_done : null,
                        ];
                    }
                    return [];
                });

            // Tính tổng duration cho các lecture có type là 'video'
            $totalVideoDuration = $section->lectures
                ->where('type', 'video')
                ->sum('duration');

            return [
                'id' => $section->id,
                'title' => $section->title,
                'order' => $section->order,
                'content_course_type' => 'section',
                'content_count' => null, // Ban đầu là null
                'content_done' => null, // Ban đầu là null
                'duration_display' => $this->formatDuration($totalVideoDuration), // Tổng thời lượng định dạng
                'section_content' => $sectionContent,
            ];
        });

        $sections = $sections->map(function ($section) {
            // Lọc chỉ các lecture trong section_content
            $lectures = $section['section_content']->where('content_section_type', 'lecture');

            $contentCount = $lectures->count(); // Tổng số lecture trong section
            $contentDone = $lectures->where('percent', '>=', 100)->count(); // Tổng số lecture hoàn thành

            $section['content_count'] = $contentCount;
            $section['content_done'] = $contentDone;

            return $section;
        });
        $sections = $sections->map(function ($section) {
            // Lọc lecture và quiz trong section_content
            $lectures = $section['section_content']->where('content_section_type', 'lecture');
            $quizzes = $section['section_content']->where('content_section_type', 'quiz');
        
            // Tổng số lecture và lecture hoàn thành
            $contentCount = $lectures->count();
            $contentDone = $lectures->where('percent', '>=', 100)->count();
        
            // Tổng số quiz và quiz hoàn thành
            $quizCount = $quizzes->count();
            $quizDone = $quizzes->where('percent', '>=', 100)->count();
        
            // Tổng cộng tất cả nội dung và hoàn thành
            $totalCount = $contentCount + $quizCount; // Tổng số nội dung (lecture + quiz)
            $totalDone = $contentDone + $quizDone;   // Tổng số nội dung hoàn thành
        
            // Gắn thông tin vào section
            $section['total_count'] = $totalCount;
            $section['total_done'] = $totalDone;
            $section['percent'] = $totalDone * 100 / $totalCount;
        
            return $section;
        });
        

        // Tổng hợp từ tất cả các section
        $totalContentCount = $sections->sum('total_count'); // Tổng số lecture
        $totalContentDone = $sections->sum('total_done');  // Tổng số lecture hoàn thành

        // Tính phần trăm tiến độ
        $progress = $totalContentCount > 0 ? ($totalContentDone / $totalContentCount) * 100 : 0;

        // Gộp tất cả nội dung (section và quiz)
        $allContent = $sections->sortBy('order')->values();
        $allContent = $allContent->map(function ($section) use ($contentKeyword) {
            // Nếu $contentKeyword rỗng hoặc null, trả về toàn bộ section
            if (empty($contentKeyword)) {
                return $section;
            }
        
            // Kiểm tra từ khóa có khớp với tiêu đề section không
            $sectionMatches = stripos($section['title'], $contentKeyword) !== false;
        
            // Lọc lecture và quiz trong section_content dựa trên từ khóa
            $filteredLectures = $section['section_content']->filter(function ($content) use ($contentKeyword) {
                return stripos($content['title'], $contentKeyword) !== false;
            });
        
            // Nếu từ khóa khớp với tiêu đề section, trả về toàn bộ section
            if ($sectionMatches) {
                return $section; // Trả về toàn bộ section và nội dung bên trong
            }
        
            // Nếu từ khóa khớp với nội dung lecture hoặc quiz
            if ($filteredLectures->isNotEmpty()) {
                // Trả về section, nhưng chỉ giữ các nội dung khớp
                return [
                    'id' => $section['id'],
                    'title' => $section['title'],
                    'order' => $section['order'],
                    'content_course_type' => $section['content_course_type'],
                    'section_content' => $filteredLectures->values(), // Nội dung khớp
                ];
            }
        
            // Nếu không có gì khớp, bỏ qua section này (trả về null)
            return null;
        })->filter(); // Lọc bỏ các giá trị null
         // Lọc bỏ các giá trị null
        

        // Chuẩn bị dữ liệu trả về
        $responseData = [
            'allContent' => $allContent,
            'total_lecture_count' => $totalContentCount,
            'total_lecture_done' => $totalContentDone,
            'progress_percent' => round($progress, 2),
        ];

        return $responseData;
    }



}
