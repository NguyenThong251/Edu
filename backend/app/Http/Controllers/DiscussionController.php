<?php

namespace App\Http\Controllers;

use App\Models\DiscussionThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'lecture_id' => 'nullable|exists:lectures,id',
            'parent_id' => 'nullable|exists:discussion_threads,id',
            'type' => 'required|in:question,answer',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
        ]);

        $user = Auth::user();

        $discussion = DiscussionThread::create(array_merge($validatedData, ['user_id' => $user->id]));

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $discussion,
            'message' => 'Discussion created successfully.',
        ], 201);
    }

    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'lecture_id' => 'nullable|exists:lectures,id',
            'type' => 'nullable|in:question,answer',
            'sort' => 'nullable|in:newest,oldest,most_liked',
        ]);

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

        $threads = $query->with(['likedBy:id,email'])->paginate(10);

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $threads->map(function ($thread) {
                $thread->is_liked = $thread->likedBy->contains(Auth::id());
                $thread->total_likes = $thread->likedBy->count();
                return $thread;
            }),
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

    public function getAnswers(Request $request, $id)
    {
        $validatedData = $request->validate([
            'sort' => 'nullable|in:newest,oldest,most_liked',
        ]);

        // Kiểm tra xem câu hỏi có tồn tại không
        $question = DiscussionThread::find($id);

        if (!$question || $question->type !== 'question') {
            return response()->json(['status' => 'FAIL', 'message' => 'Question not found.'], 404);
        }

        // Truy vấn câu trả lời của câu hỏi
        $query = DiscussionThread::query()
            ->where('parent_id', $id)
            ->where('type', 'answer')
            ->with(['likedBy:id,email']);

        // Sắp xếp theo yêu cầu
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

        // Lấy danh sách câu trả lời
        $answers = $query->get();

        // Thêm trạng thái is_liked và total_likes cho mỗi câu trả lời
        $answers->map(function ($answer) {
            $answer->is_liked = $answer->likedBy->contains(Auth::id());
            $answer->total_likes = $answer->likedBy->count();
            return $answer;
        });

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $answers,
        ]);
    }
}
