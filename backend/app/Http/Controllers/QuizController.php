<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Section;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\LengthAwarePaginator;

class QuizController extends Controller
{
    public function getListAdmin(Request $request)
    {
        $quizzesQuery = Quiz::query();

        if ($request->has('deleted') && $request->deleted == 1) {
            $quizzesQuery->onlyTrashed();
        } else {
            $quizzesQuery->whereNull('deleted_at');
        }

        if ($request->has('keyword') && !empty($request->keyword)) {
            $quizzesQuery->where('title', 'like', '%' . $request->keyword . '%');
        }

        if ($request->has('section_id') && !empty($request->section_id)) {
            $quizzesQuery->where('section_id', $request->section_id);
        }
        if ($request->has('created_by') && !empty($request->created_by)) {
            $quizzesQuery->where('created_by', $request->created_by);
        }

        if ($request->has('status') && !is_null($request->status)) {
            $quizzesQuery->where('status', $request->status);
        }

        if ($request->is_instructor == 1) {
            // Lọc theo người dùng hiện tại và đảm bảo trường `created_at` không null
            $quizzesQuery->where('created_by', auth()->id());
        }
        
        $order = $request->get('order', 'desc');
        $quizzesQuery->orderBy('created_at', $order);

        $perPage = (int) $request->get('per_page', 10);
        $page = (int) $request->get('page', 1);

        $quizzes = $quizzesQuery->get();
        $total = $quizzes->count();
        $paginatedQuizzes = $quizzes->forPage($page, $perPage)->values();

        $pagination = new LengthAwarePaginator(
            $paginatedQuizzes,
            $total,
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        $quizzes = $pagination->toArray();

        return formatResponse(STATUS_OK, $quizzes, '', __('messages.quiz_fetch_success'));
    }

    public function editForm($id)
    {
        // Tìm quiz theo id, bao gồm cả các bản ghi đã bị xóa mềm
        $quiz = Quiz::withTrashed()->find($id);

        // Nếu không tìm thấy quiz
        if (!$quiz) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.quiz_not_found'));
        }

        // Trả về thông tin của quiz
        return formatResponse(STATUS_OK, $quiz, '', __('messages.quiz_detail_success'));
    }


    public function store(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'title' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ], [
            'section_id.required' => __('messages.section_id_required'),
            'section_id.exists' => __('messages.section_id_invalid'),
            'title.required' => __('messages.title_required'),
            'title.max' => __('messages.title_max'),
            'status.required' => __('messages.status_required'),
            'status.in' => __('messages.status_invalid'),
        ]);

        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), __('messages.validation_error'));
        }

        $quiz = new Quiz();
        $quiz->section_id = $request->section_id;
        $quiz->title = $request->title;
        $quiz->status = $request->status;

        $maxOrder = Quiz::where('section_id', $request->section_id)->max('order');
        $quiz->order = ($maxOrder) ? $maxOrder + 1 : 1;

        $quiz->created_by = $user->id;
        $quiz->save();

        return formatResponse(STATUS_OK, $quiz, '', __('messages.quiz_create_success'));
    }

    public function showOne($id)
    {
        $quiz = Quiz::with('questions')->where('id', $id)->first();
        if (!$quiz) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.section_not_found'));
        }
        return formatResponse(STATUS_OK, $quiz, '', __('messages.course_found'));
    }


    public function update(Request $request, $quizId)
    {
        $quiz = Quiz::find($quizId);
        if (!$quiz) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.quiz_not_found'));
        }

        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'title' => 'required|string|max:255',
            // 'status' => 'required|in:active,inactive',
        ], [
            'section_id.required' => __('messages.section_id_required'),
            'section_id.exists' => __('messages.section_id_invalid'),
            'title.required' => __('messages.title_required'),
            'title.max' => __('messages.title_max'),
            // 'status.required' => __('messages.status_required'),
            // 'status.in' => __('messages.status_invalid'),
        ]);

        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), __('messages.validation_error'));
        }

        $quiz->section_id = $request->section_id;
        $quiz->title = $request->title;
        // $quiz->status = $request->status;
        $quiz->updated_by = auth()->user()->id;

        $quiz->save();

        return formatResponse(STATUS_OK, $quiz, '', __('messages.quiz_update_success'));
    }

    public function destroy($id)
    {
        $quiz = Quiz::find($id);

        if (!$quiz) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.quiz_not_found'));
        }

        $quiz->deleted_by = auth()->user()->id;
        $quiz->delete();

        $quiz = Quiz::onlyTrashed()->find($id);

        return formatResponse(STATUS_OK, $quiz, '', __('messages.quiz_soft_delete_success'));
    }

    public function updateQuizSection(Request $request, $id)
    {
        // Tìm bài giảng cần cập nhật
        $quiz = Quiz::find($id);
        if (!$quiz) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.quiz_not_found'));
        }
        // Lấy section_id từ request (có thể trả về 404 nếu không có)
        $sectionId = (int)$request->input('section_id');

        // Kiểm tra xem section có tồn tại không
        $section = Section::find($sectionId);
        if (!$section) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.section_not_found'));

        }

        // Cập nhật lại section cho bài giảng
        $quiz->section_id = $sectionId;
        $quiz->save();

        return formatResponse(STATUS_OK, $quiz, '', __('messages.lecture_section_updated'));
    }

    public function updateQuizStatus(Request $request, $id)
    {
        // Tìm bài giảng cần cập nhật
        $quiz = Quiz::find($id);
        if (!$quiz) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.quiz_not_found'));
        }
        // Lấy trạng thái mới từ request
        $status = $request->input('status');

        // Kiểm tra trạng thái hợp lệ (active hoặc inactive)
        if (!in_array($status, ['active', 'inactive'])) {
            throw new \Exception(__('messages.invalid_status'));
        }

        // Cập nhật trạng thái
        $quiz->status = $status;
        $quiz->save();

        return formatResponse(STATUS_OK, $quiz, '', __('messages.lecture_status_updated'));
    }

    public function restore($id)
    {
        $quiz = Quiz::onlyTrashed()->find($id);

        if (!$quiz) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.quiz_not_found'));
        }

        $quiz->deleted_by = auth()->user()->id;
        $quiz->restore();

        $quiz = Quiz::find($id);

        return formatResponse(STATUS_OK, $quiz, '', __('messages.quiz_restore_success'));
    }

    public function forceDelete($id)
    {
        $quiz = Quiz::onlyTrashed()->find($id);

        if (!$quiz) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.quiz_not_found'));
        }

        $quiz->forceDelete();

        return formatResponse(STATUS_OK, $quiz, '', __('messages.quiz_force_delete_success'));
    }
}
