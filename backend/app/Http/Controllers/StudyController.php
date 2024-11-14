<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Models\Section;
use App\Models\Quiz;
use App\Models\Lecture;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudyController extends Controller
{
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

        $userId=$currentUser->id;

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
                      ->with(['progress' => function ($progressQuery) use ($userId) {
                          $progressQuery->select('quiz_id', 'questions_done', 'percent')
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
                        return [
                            'id' => $item->id,
                            'title' => $item->title,
                            'order' => $item->order,
                            'content_section_type' => 'lecture',
                            'learned' => $progress && isset($progress->learned) ? $progress->learned : null,
                            'percent' => $progress && isset($progress->percent) ? $progress->percent : null,
                        ];
                    } elseif ($item instanceof Quiz) {
                        return [
                            'id' => $item->id,
                            'title' => $item->title,
                            'order' => $item->order,
                            'content_section_type' => 'quiz',
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
        ->with(['progress' => function ($query) use ($userId) {
            $query->select('quiz_id', 'questions_done', 'percent')
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

        // Tổng content_count và content_done từ sections
        $totalContentCount = $sections->sum('content_count');
        $totalContentDone = $sections->sum('content_done');

        // Thêm quiz bên ngoài section vào tổng số
        $quizContentCount = $quizzesForCourse->count(); // Số lượng quiz bên ngoài section
        $quizContentDone = $quizzesForCourse->where('percent', 100)->count(); // Quiz hoàn thành

        $totalContentCount += $quizContentCount;
        $totalContentDone += $quizContentDone;

        // Tính progress của toàn khóa học
        $progress = $totalContentCount > 0 ? ($totalContentDone / $totalContentCount) * 100 : 0;

        
        // Nếu cần merge thêm quizzes bên ngoài section
        $allContent = $sections->merge($quizzesForCourse)->sortBy('order')->values();

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
                                $currentContent = array_merge(
                                    $lecture->toArray(),
                                    [
                                        'learned' => $sectionContent['learned'] ?? null,
                                        'percent' => $sectionContent['percent'] ?? null
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
                        $currentContent = array_merge(
                            $lecture->toArray(),
                            [
                                'learned' => $sectionContent['learned'] ?? null,
                                'percent' => $sectionContent['percent'] ?? null
                            ]
                        );
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
}
