<?php

namespace App\Http\Controllers;

use App\Models\DiscussionThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DiscussionController extends Controller
{
    public function store(Request $request)
    {
        // Xử lý validation
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'lecture_id' => 'nullable|exists:lectures,id',
            'parent_id' => 'nullable|exists:discussion_threads,id',
            'type' => 'required|in:question,answer',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'FAIL',
                'errors' => $validator->errors(),
                'message' => 'Validation failed.',
            ], 422);
        }

        $validatedData = $validator->validated();

        if ($validatedData['type'] === 'answer') {
            if (empty($validatedData['parent_id'])) {
                return response()->json(['status' => 'FAIL', 'message' => 'An answer must have a valid parent_id pointing to a question.',
                ], 400);
            }
            $parent = DiscussionThread::find($validatedData['parent_id']);
            if (!$parent || $parent->type !== 'question') {
                return response()->json(['status' => 'FAIL', 'message' => 'Answers can only reply to questions.',
                ], 400);
            }
        }
        $user = Auth::user();
        $discussion = DiscussionThread::create(array_merge($validatedData, ['user_id' => $user->id]));
        return response()->json(['status' => 'SUCCESS', 'data' => $discussion, 'message' => 'Discussion created successfully.'
            ,], 201);
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'lecture_id' => 'nullable|exists:lectures,id',
            'type' => 'nullable|in:question,answer',
            'sort' => 'nullable|in:newest,oldest,most_liked',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        // Kiểm tra lỗi
        if ($validator->fails()) {
            return response()->json([
                'status' => 'FAIL',
                'errors' => $validator->errors(),
                'message' => 'Validation failed.',
            ], 422);
        }

        // Lấy dữ liệu đã được validate
        $validatedData = $validator->validated();

        $perPage = $validatedData['per_page'] ?? 10; // Số mục trên mỗi trang, mặc định 10
        $page = $request->get('page', 1); // Trang hiện tại, mặc định là 1
        $offset = ($page - 1) * $perPage;

        $query = DiscussionThread::query()->where('course_id', $validatedData['course_id']);

        if (!empty($validatedData['lecture_id'])) {
            $query->where('lecture_id', $validatedData['lecture_id']);
        }

        if (!empty($validatedData['type'])) {
            $query->where('type', $validatedData['type']);
        }

        if (!empty($validatedData['sort'])) {
            if ($validatedData['sort'] === 'most_liked') {
                $query->withCount('likedBy')->orderBy('liked_by_count', 'desc');
            } elseif ($validatedData['sort'] === 'oldest') {
                $query->orderBy('created_at', 'asc');
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Tổng số bản ghi
        $total = $query->count();

        // Lấy dữ liệu với offset và limit
        $threads = $query->with(['user:id,first_name,last_name', 'likedBy:id'])->offset($offset)->limit($perPage)->get();

        // Format dữ liệu
        $currentUserId = Auth::id();
        $formattedThreads = $threads->map(function ($thread) use ($currentUserId) {
            $isLiked = $thread->likedBy->contains('id', $currentUserId);

            return [
                'id' => $thread->id,
                'type' => $thread->type,
                'course_id' => $thread->course_id,
                'lecture_id' => $thread->lecture_id,
                'user_id' => $thread->user_id,
                'full_name' => $thread->user->first_name . ' ' . $thread->user->last_name,
                'content' => $thread->content,
                'created_at' => $thread->created_at->diffForHumans(),
                'total_likes' => $thread->likedBy->count(),
                'total_replies' => $thread->children->count(),
                'is_liked' => $isLiked,
            ];
        });

        // Gắn pagination
        $pagination = new \Illuminate\Pagination\LengthAwarePaginator(
            $formattedThreads,
            $total,
            $perPage,
            $page,
            [
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        // Convert pagination thành array
        $threadsWithPagination = $pagination->toArray();

        // Trả về response
        return response()->json([
            'status' => 'SUCCESS',
            'data' => $threadsWithPagination['data'],
            'pagination' => [
                'current_page' => $threadsWithPagination['current_page'],
                'last_page' => $threadsWithPagination['last_page'],
                'total' => $threadsWithPagination['total'],
                'per_page' => $threadsWithPagination['per_page'],
                'next_page_url' => $threadsWithPagination['next_page_url'],
                'prev_page_url' => $threadsWithPagination['prev_page_url'],
            ],
        ]);
    }


    public function like($id)
    {
        $user = Auth::user();
        $thread = DiscussionThread::find($id);

        if (!$thread) {
            return response()->json(['status' => 'FAIL', 'message' => 'Discussion not found.'], 404);
        }

        if ($user->likedThreads()->where('discussion_thread_id', $id)->exists()) {
            return response()->json(['status' => 'FAIL', 'message' => 'Already liked.'], 400);
        }

        $user->likedThreads()->attach($id);

        return response()->json(['status' => 'SUCCESS', 'message' => 'Liked successfully.']);
    }

    public function unlike($id)
    {
        $user = Auth::user();
        $thread = DiscussionThread::find($id);

        if (!$thread) {
            return response()->json(['status' => 'FAIL', 'message' => 'Discussion not found.'], 404);
        }

        if (!$user->likedThreads()->where('discussion_thread_id', $id)->exists()) {
            return response()->json(['status' => 'FAIL', 'message' => 'Not liked yet.'], 400);
        }

        $user->likedThreads()->detach($id);

        return response()->json(['status' => 'SUCCESS', 'message' => 'Unliked successfully.']);
    }

    public function getAnswersByQuestion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question_id' => 'required|exists:questions,id',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'FAIL',
                'errors' => $validator->errors(),
                'message' => 'Validation failed.',
            ], 422);
        }

        $validatedData = $validator->validated();

        $perPage = $validatedData['per_page'] ?? 5;
        $page = $validatedData['page'] ?? 1;
        $offset = ($page - 1) * $perPage;
        $currentUserId = Auth::id();
        // Kiểm tra câu hỏi tồn tại
        $question = DiscussionThread::where('id', $validatedData['question_id'])->where('type', 'question')->first();

        if (!$question) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Question not found.',
            ], 404);
        }

        // Lấy danh sách câu trả lời với phân trang
        $query = DiscussionThread::where('parent_id', $validatedData['question_id'])
            ->where('type', 'answer')
            ->with(['user:id,first_name,last_name']);

        $total = $query->count();


        $answers = $query->offset($offset)->limit($perPage)->get()->map(function ($answer) use ($currentUserId) {
            $isLiked = $answer->likedBy->contains('id', $currentUserId);

            return [
                'id' => $answer->id,
                'content' => $answer->content,
                'user_id' => $answer->user_id,
                'full_name' => $answer->user->first_name . ' ' . $answer->user->last_name,
                'created_at' => $answer->created_at->diffForHumans(),
                'total_likes' => $answer->likedBy->count(),
                'is_liked' => $isLiked,
            ];
        });

        // Gắn pagination
        $pagination = new \Illuminate\Pagination\LengthAwarePaginator($answers, $total, $perPage, $page,
            [
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        // Convert pagination thành array
        $answersWithPagination = $pagination->toArray();

        // Trả về response
        return response()->json([
            'status' => 'SUCCESS',
            'data' => $answersWithPagination['data'],
            'pagination' => [
                'current_page' => $answersWithPagination['current_page'],
                'last_page' => $answersWithPagination['last_page'],
                'total' => $answersWithPagination['total'],
                'per_page' => $answersWithPagination['per_page'],
                'next_page_url' => $answersWithPagination['next_page_url'],
                'prev_page_url' => $answersWithPagination['prev_page_url'],
            ],
            'message' => 'Answers retrieved successfully.',
        ]);
    }
}
