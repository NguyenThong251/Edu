<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Models\Section;
use App\Models\Quiz;
use App\Models\Lecture;
use App\Models\ProgressLecture;
use App\Models\ProgressQuiz;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudyController extends Controller
{
    

    public function getAllContent($userId, $courseId)
{
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
                    ->select('id', 'section_id', 'course_id', 'title', 'order')
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
        
            return [
                'id' => $section->id,
                'title' => $section->title,
                'order' => $section->order,
                'content_course_type' => 'section',
                'content_count' => null, // Ban đầu là null
                'content_done' => null, // Ban đầu là null
                'section_content' => $sectionContent,
            ];
        });
        

    $quizzesForCourse = Quiz::where('course_id', $courseId)
        ->whereNull('section_id')
        ->where('status', 'active')
        ->select('id', 'course_id', 'title', 'order')
        ->orderBy('order', 'asc')
        ->addSelect([
            'content_course_type' => DB::raw('"quiz"'),
        ])
        ->withCount('questions') // Đếm số lượng câu hỏi
        ->with(['progress' => function ($query) use ($userId) {
            $query->select('quiz_id', 'questions_done', 'percent')
                ->where('status', 'active')
                ->where('user_id', $userId);
        }])
        ->get()
        ->map(function ($quiz) {
            $progress = $quiz->progress ?? null;

            return [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'order' => $quiz->order,
                'content_course_type' => 'quiz',
                'total_question_count' => $quiz->questions_count, // Sử dụng questions_count từ withCount
                'section_content' => [],
                'questions_done' => $progress && isset($progress->questions_done) ? $progress->questions_done : null,
                'percent' => $progress && isset($progress->percent) ? $progress->percent : null,
            ];
        });

    $sections = $sections->map(function ($section) {
        $contentCount = $section['section_content']->count(); // Tổng số nội dung trong section
        $contentDone = $section['section_content']->where('percent', 100)->count(); // Tổng số nội dung hoàn thành

        $section['content_count'] = $contentCount;
        $section['content_done'] = $contentDone;

        return $section;
    });

    $totalContentCount = $sections->sum('content_count');
    $totalContentDone = $sections->sum('content_done');

    $quizContentCount = $quizzesForCourse->count();
    $quizContentDone = $quizzesForCourse->where('percent', 100)->count();

    $totalContentCount += $quizContentCount;
    $totalContentDone += $quizContentDone;

    $progress = $totalContentCount > 0 ? ($totalContentDone / $totalContentCount) * 100 : 0;

    $allContent = $sections->merge($quizzesForCourse)->sortBy('order')->values();

    $responseData = [
        'allContent' => $allContent,
        'total_content_count' => $totalContentCount,
        'total_content_done' => $totalContentDone,
        'progress' => round($progress, 2),
    ];

    return $responseData;
}

private function formatDuration($seconds)
{
    $minutes = floor($seconds / 60);
    if ($minutes < 60) {
        return "{$minutes} phút"; // Dưới 1 giờ
    }

    $hours = floor($minutes / 60);
    $remainingMinutes = $minutes % 60;

    if ($remainingMinutes > 0) {
        return "{$hours} giờ {$remainingMinutes} phút"; // Đủ giờ và phút lẻ
    }

    return "{$hours} giờ"; // Chỉ giờ
}


    public function studyCourse(Request $request)
    {
        // Lấy user hiện tại
        $currentUser = Auth::user();

        // Lấy course_id từ request
        $courseId = $request->input('course_id');

        // Kiểm tra xem user đã mua khóa học chưa
        $orderItem = OrderItem::where('course_id', $courseId)
            ->where('status', 'active')
            ->whereHas('order', function ($query) use ($currentUser) {
                $query->where('user_id', $currentUser->id)
                    ->where('status', 'active')
                    ->where('payment_status', 'paid');
            })
            ->first();


        // Nếu không tìm thấy orderItem, báo lỗi
        if (!$orderItem) {
            return formatResponse(
                STATUS_FAIL,
                '',
                '',
                __('messages.course_not_purchased')
            );
        }

        $userId = $currentUser->id;        
        $data = $this->getAllContent($userId, $courseId);
        $allContent=$data['allContent'];
        $totalContentCount=$data['total_content_count'];
        $totalContentDone=$data['total_content_done'];
        $progress=$data['progress'];

        
        $currentContent = null;

    foreach ($allContent as $content) {
        // Nếu là section, duyệt qua section_content
        if ($content['content_course_type'] === 'section') {
            foreach ($content['section_content'] as $sectionContent) {
                // Chỉ lấy content nếu percent khác 100 hoặc là null
                if (!isset($sectionContent['percent']) || $sectionContent['percent'] < 100) {
                    if ($sectionContent['content_section_type'] === 'lecture') {
                        $lecture = Lecture::find($sectionContent['id']);
                        if ($lecture) {
                            $durationDisplay = $lecture->type === 'video'
                                ? $this->formatDuration($lecture->duration)
                                : ($lecture->type === 'file' ? $lecture->duration . " trang" : null);

                            $currentContent = array_merge(
                                $lecture->toArray(),
                                [
                                    'current_content_type' => 'lecture',
                                    'learned' => $sectionContent['learned'] ?? null,
                                    'percent' => $sectionContent['percent'] ?? null,
                                    'duration_display' => $durationDisplay, // Gắn thêm duration_display
                                ]
                            );
                        }
                    } elseif ($sectionContent['content_section_type'] === 'quiz') {
                        $quiz = Quiz::with('questions')
                            ->where('id', $sectionContent['id'])
                            ->where('status', 'active')
                            ->first();

                        if ($quiz) {
                            $questions = $quiz->questions;
                            $nextQuestionIndex = $sectionContent['questions_done'] ?? 0;
                            $nextQuestion = $questions[$nextQuestionIndex] ?? null;

                            $currentContent = array_merge(
                                $quiz->toArray(),
                                [
                                    'current_content_type' => 'quiz',
                                    'questions_done' => $sectionContent['questions_done'] ?? null,
                                    'percent' => $sectionContent['percent'] ?? null,
                                    'current_question' => $nextQuestion && $nextQuestion->status === 'active'
                                        ? $nextQuestion->toArray()
                                        : null
                                ]
                            );
                        }
                    }
                    break 2; // Dừng cả hai vòng lặp
                }
            }
        }

        // Nếu là quiz bên ngoài section
        if ($content['content_course_type'] === 'quiz') {
            if (!isset($content['percent']) || $content['percent'] < 100) {
                $quiz = Quiz::with('questions')
                    ->where('id', $content['id'])
                    ->where('status', 'active')
                    ->first();

                if ($quiz) {
                    $questions = $quiz->questions;
                    $nextQuestionIndex = $content['questions_done'] ?? 0;
                    $nextQuestion = $questions[$nextQuestionIndex] ?? null;

                    $currentContent = array_merge(
                        $quiz->toArray(),
                        [
                            'current_content_type' => 'quiz',
                            'questions_done' => $content['questions_done'] ?? null,
                            'percent' => $content['percent'] ?? null,
                            'current_question' => $nextQuestion && $nextQuestion->status === 'active'
                                ? $nextQuestion->toArray()
                                : null
                        ]
                    );
                }
                break; // Dừng vòng lặp
            }
        }
    }

    // Nếu không tìm thấy content nào phù hợp, lấy phần tử đầu tiên
    if (!$currentContent) {
        foreach ($allContent as $content) {
            if ($content['content_course_type'] === 'section' && !empty($content['section_content'])) {
                $sectionContent = $content['section_content'][0];
                if ($sectionContent['content_section_type'] === 'lecture') {
                    $lecture = Lecture::find($sectionContent['id']);
                    if ($lecture) {
                        $durationDisplay = $lecture->type === 'video'
                            ? $this->formatDuration($lecture->duration)
                            : ($lecture->type === 'file' ? $lecture->duration . " trang" : null);

                        $currentContent = array_merge(
                            $lecture->toArray(),
                            [
                                'current_content_type' => 'lecture',
                                'learned' => $sectionContent['learned'] ?? null,
                                'percent' => $sectionContent['percent'] ?? null,
                                'duration_display' => $durationDisplay, // Gắn thêm duration_display
                            ]
                        );
                    }
                } elseif ($sectionContent['content_section_type'] === 'quiz') {
                    $quiz = Quiz::with('questions')
                        ->where('id', $sectionContent['id'])
                        ->where('status', 'active')
                        ->first();

                    if ($quiz) {
                        $questions = $quiz->questions;
                        $nextQuestionIndex = $sectionContent['questions_done'] ?? 0;
                        $nextQuestion = $questions[$nextQuestionIndex] ?? null;

                        $currentContent = array_merge(
                            $quiz->toArray(),
                            [
                                'current_content_type' => 'quiz',
                                'questions_done' => $sectionContent['questions_done'] ?? null,
                                'percent' => $sectionContent['percent'] ?? null,
                                'current_question' => $nextQuestion && $nextQuestion->status === 'active'
                                    ? $nextQuestion->toArray()
                                    : null
                            ]
                        );
                    }
                }
                break;
            }

            if ($content['content_course_type'] === 'quiz') {
                $quiz = Quiz::with('questions')
                    ->where('id', $content['id'])
                    ->where('status', 'active')
                    ->first();

                if ($quiz) {
                    $questions = $quiz->questions;
                    $nextQuestionIndex = $content['questions_done'] ?? 0;
                    $nextQuestion = $questions[$nextQuestionIndex] ?? null;

                    $currentContent = array_merge(
                        $quiz->toArray(),
                        [
                            'current_content_type' => 'quiz',
                            'questions_done' => $content['questions_done'] ?? null,
                            'percent' => $content['percent'] ?? null,
                            'current_question' => $nextQuestion && $nextQuestion->status === 'active'
                                ? $nextQuestion->toArray()
                                : null
                        ]
                    );
                }
                break;
            }
        }
    }


        $responseData = [
            'currentContent' => $currentContent,
            'allContent' => $allContent,
            'total_content_count' => $totalContentCount,
            'total_content_done' => $totalContentDone,
            'progress' => round($progress, 2), // Làm tròn đến 2 chữ số thập phân
        ];


        // Nếu đã mua khóa học, tiếp tục xử lý logic khác
        return formatResponse(STATUS_OK, $responseData, '', __('messages.course_access_granted'));
    }
    public function changeContent(Request $request)
    {
        // Lấy user hiện tại
        $currentUser = Auth::user();

        // Lấy course_id từ request
        $courseId = $request->input('course_id');

        // Kiểm tra xem user đã mua khóa học chưa
        $orderItem = OrderItem::where('course_id', $courseId)
            ->where('status', 'active')
            ->whereHas('order', function ($query) use ($currentUser) {
                $query->where('user_id', $currentUser->id)
                    ->where('status', 'active')
                    ->where('payment_status', 'paid');
            })
            ->first();

        // Nếu không tìm thấy orderItem, báo lỗi
        if (!$orderItem) {
            return formatResponse(
                STATUS_FAIL,
                '',
                '',
                __('messages.course_not_purchased')
            );
        }

        $userId = $currentUser->id;

        // Dữ liệu từ request
        $contentType = $request->input('content_type');
        $contentId = $request->input('content_id');
        $contentOldType = $request->input('content_old_type');
        $contentOldId = $request->input('content_old_id');
        $learned = $request->input('learned');

        // Xử lý nội dung cũ
        if ($contentOldType === 'lecture') {
            $lecture = Lecture::where('id', $contentOldId)
                ->where('status', 'active')
                ->first();
    
            if ($lecture) {
                $percent = ($learned / $lecture->duration) * 100;
                $progressLecture = ProgressLecture::where('lecture_id', $contentOldId)
                    ->where('user_id', $userId)
                    ->first();
    
                if (!$progressLecture || $progressLecture->percent < 100) {
                    ProgressLecture::updateOrCreate(
                        [
                            'lecture_id' => $contentOldId,
                            'user_id' => $userId,
                        ],
                        [
                            'learned' => $learned,
                            'percent' => $progressLecture && $percent > $progressLecture->percent ? round($percent, 2) : $progressLecture->percent,
                            'status' => 'active',
                        ]
                    );
                }
            }
        } elseif ($contentOldType === 'quiz') {
            $quiz = Quiz::withCount('questions')
                ->where('id', $contentOldId)
                ->where('status', 'active')
                ->first();
    
            if ($quiz) {
                $percent = ($learned / $quiz->questions_count) * 100;
                $progressQuiz = ProgressQuiz::where('quiz_id', $contentOldId)
                    ->where('user_id', $userId)
                    ->first();
    
                if (!$progressQuiz || $progressQuiz->percent < 100) {
                    ProgressQuiz::updateOrCreate(
                        [
                            'quiz_id' => $contentOldId,
                            'user_id' => $userId,
                        ],
                        [
                            'questions_done' => $learned,
                            'percent' => $progressQuiz && $percent > $progressQuiz->percent ? round($percent, 2) : $progressQuiz->percent,
                            'status' => 'active',
                        ]
                    );
                }
            }
        }

        // Hiển thị current_content cho nội dung mới
        $currentContent = null;
        if ($contentType === 'lecture') {
            $lecture = Lecture::where('id', $contentId)
                ->where('status', 'active')
                ->first();

            if ($lecture) {
                $progressLecture = ProgressLecture::where('lecture_id', $contentId)
                    ->where('user_id', $userId)
                    ->where('status', 'active')
                    ->first();

                $currentContent = array_merge(
                    $lecture->toArray(),
                    [
                        'current_content_type' => 'lecture',
                        'learned' => $progressLecture->learned ?? null,
                        'percent' => $progressLecture->percent ?? null,
                    ]
                );
            }
        } elseif ($contentType === 'quiz') {
            $quiz = Quiz::with('questions')
                ->where('id', $contentId)
                ->where('status', 'active')
                ->first();

            if ($quiz) {
                $progressQuiz = ProgressQuiz::where('quiz_id', $contentId)
                    ->where('user_id', $userId)
                    ->where('status', 'active')
                    ->first();

                $nextQuestionIndex = $progressQuiz['questions_done'] ?? 0;
                $currentQuestion = $quiz->questions[$nextQuestionIndex] ?? null;

                $currentContent = array_merge(
                    $quiz->toArray(),
                    [
                        'current_content_type' => 'quiz',
                        'questions_done' => $progressQuiz->questions_done ?? null,
                        'percent' => $progressQuiz->percent ?? null,
                        'current_question' => $currentQuestion ? $currentQuestion->toArray() : null,
                    ]
                );
            }
        }

        // Lấy dữ liệu tổng quan
        $data = $this->getAllContent($userId, $courseId);
        $allContent = $data['allContent'];
        $totalContentCount = $data['total_content_count'];
        $totalContentDone = $data['total_content_done'];
        $progress = $data['progress'];

        $responseData = [
            'currentContent' => $currentContent,
            'allContent' => $allContent,
            'total_content_count' => $totalContentCount,
            'total_content_done' => $totalContentDone,
            'progress' => round($progress, 2),
        ];

        // Trả về response
        return formatResponse(STATUS_OK, $responseData, '', __('messages.course_access_granted'));
    }
}
