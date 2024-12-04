<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\LengthAwarePaginator;

class QuestionController extends Controller
{
    public function getListAdmin(Request $request)
    {
        $questionsQuery = Question::query();

        if ($request->has('deleted') && $request->deleted == 1) {
            $questionsQuery->onlyTrashed();
        } else {
            $questionsQuery->whereNull('deleted_at');
        }

        if ($request->has('keyword') && !empty($request->keyword)) {
            $questionsQuery->where('question', 'like', '%' . $request->keyword . '%');
        }

        if ($request->has('quiz_id') && !empty($request->quiz_id)) {
            $questionsQuery->where('quiz_id', $request->quiz_id);
        }

        if ($request->has('status') && !is_null($request->status)) {
            $questionsQuery->where('status', $request->status);
        }
        if ($request->is_instructor == 1) {
            // Lọc theo người dùng hiện tại và đảm bảo trường `created_at` không null
            $questionsQuery->where('created_by', auth()->id());
        }
        if ($request->has('created_by') && !empty($request->created_by)) {
            $questionsQuery->where('created_by', $request->created_by);
        }

        $order = $request->get('order', 'desc');
        $questionsQuery->orderBy('created_at', $order);

        $perPage = (int) $request->get('per_page', 10);
        $page = (int) $request->get('page', 1);

        $questions = $questionsQuery->get();
        $total = $questions->count();
        $paginatedQuestions = $questions->forPage($page, $perPage)->values();

        $pagination = new LengthAwarePaginator(
            $paginatedQuestions,
            $total,
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        $questions = $pagination->toArray();

        return formatResponse(STATUS_OK, $questions, '', __('messages.question_fetch_success'));
    }

    public function editForm($id)
    {
        $question = Question::withTrashed()->find($id);

        // $question->options = json_decode($question->options);
        if (!$question) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.question_not_found'));
        }

        return formatResponse(STATUS_OK, $question, '', __('messages.question_detail_success'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'quiz_id' => 'required|exists:quizzes,id',
            'question' => 'required|string',
            'options' => 'required|array', // Kiểm tra options là một mảng
            'answer' => 'required|string',
            'status' => 'required|in:active,inactive',
        ], [
            'quiz_id.required' => __('messages.quiz_id_required'),
            'quiz_id.exists' => __('messages.quiz_id_invalid'),
            'question.required' => __('messages.question_required'),
            'options.required' => __('messages.options_required'),
            'options.array' => __('messages.options_invalid_format'), // Thông báo nếu options không phải là mảng
            'answer.required' => __('messages.answer_required'),
            'status.required' => __('messages.status_required'),
            'status.in' => __('messages.status_invalid'),
        ]);

        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), __('messages.validation_error'));
        }

        $question = new Question();
        $question->quiz_id = $request->quiz_id;
        $question->question = $request->question;
        $question->options = $request->options; // Encode mảng options thành JSON
        $question->answer = $request->answer;
        $question->status = $request->status;

        $maxOrder = Question::where('quiz_id', $request->quiz_id)->max('order');
        $question->order = ($maxOrder) ? $maxOrder + 1 : 1;

        $question->created_by = $user->id;
        $question->save();

        // Decode options để trả về dữ liệu thân thiện
        // $question->options = json_decode($question->options);

        return formatResponse(STATUS_OK, $question, '', __('messages.question_create_success'));
    }

    public function update(Request $request, $questionId)
    {
        $question = Question::find($questionId);

        if (!$question) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.question_not_found'));
        }

        $validator = Validator::make($request->all(), [
            'quiz_id' => 'required|exists:quizzes,id',
            'question' => 'required|string',
            'options' => 'required|array', // Kiểm tra options là một mảng
            'answer' => 'required|string',
            'status' => 'required|in:active,inactive',
        ], [
            'quiz_id.required' => __('messages.quiz_id_required'),
            'quiz_id.exists' => __('messages.quiz_id_invalid'),
            'question.required' => __('messages.question_required'),
            'options.required' => __('messages.options_required'),
            'options.array' => __('messages.options_invalid_format'), // Thông báo nếu options không phải là mảng
            'answer.required' => __('messages.answer_required'),
            'status.required' => __('messages.status_required'),
            'status.in' => __('messages.status_invalid'),
        ]);

        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), __('messages.validation_error'));
        }

        $question->quiz_id = $request->quiz_id;
        $question->question = $request->question;
        $question->options = $request->options; // Encode mảng options thành JSON
        $question->answer = $request->answer;
        $question->status = $request->status;
        $question->updated_by = auth()->user()->id;

        $question->save();

        // Decode options để trả về dữ liệu thân thiện
        // $question->options = json_decode($question->options);

        return formatResponse(STATUS_OK, $question, '', __('messages.question_update_success'));
    }


    public function updateQuestionAttributes(Request $request, $id)
    {
        // Tìm câu hỏi cần cập nhật
        $question = Question::find($id);
        if (!$question) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.question_not_found'));
        }

        // Cập nhật quiz_id nếu có
        if ($request->has('quiz_id')) {
            $quizId = (int)$request->input('quiz_id');
            $quiz = Quiz::find($quizId);
            if (!$quiz) {
                return formatResponse(STATUS_FAIL, '', '', __('messages.quiz_not_found'));
            }
            $question->quiz_id = $quizId;
        }

        // Cập nhật trạng thái nếu có
        if ($request->has('status')) {
            $status = $request->input('status');
            if (!in_array($status, ['active', 'inactive'])) {
                return formatResponse(STATUS_FAIL, '', '', __('messages.invalid_status'));
            }
            $question->status = $status;
        }

        // Lưu các thay đổi
        $question->save();
        // $question->options = json_decode($question->options);

        return formatResponse(STATUS_OK, $question, '', __('messages.question_update_success'));
    }

    public function showQuestionsByQuiz($quizId)
    {
        // Lấy tất cả các câu hỏi thuộc quiz
        $questions = Question::where('quiz_id', $quizId)
            ->whereNull('deleted_at') // Lọc các câu hỏi chưa bị xóa
            ->orderBy('order', 'asc') // Sắp xếp theo order tăng dần
            ->get();

        // Trả về danh sách câu hỏi
        return formatResponse(STATUS_OK, $questions, '', __('messages.questions_fetch_success'));
    }
    public function updateQuestionOrder(Request $request)
    {
        // Lấy dữ liệu từ request
        $sortedQuestions = $request->input('sorted_questions');

        // Kiểm tra nếu không có 'sorted_questions' hoặc không phải là mảng
        if (!$sortedQuestions || !is_array($sortedQuestions)) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.invalid_data_format'));
        }

        // Duyệt qua từng item trong mảng data và cập nhật lại order cho từng câu hỏi
        foreach ($sortedQuestions as $item) {
            // Kiểm tra nếu phần tử không có id hoặc order
            if (!isset($item['id']) || !isset($item['order'])) {
                continue; // Bỏ qua phần tử không hợp lệ
            }

            // Cập nhật order cho câu hỏi
            $question = Question::find($item['id']);
            if ($question) {
                $question->order = $item['order']; // Cập nhật order mới
                $question->save();
            }
        }

        // Trả về phản hồi thành công
        return formatResponse(STATUS_OK, '', '', __('messages.questions_order_update_success'));
    }



    public function destroy($id)
    {
        $question = Question::find($id);

        if (!$question) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.question_not_found'));
        }

        $question->deleted_by = auth()->user()->id;
        $question->delete();

        $question = Question::onlyTrashed()->find($id);
        // $question->options = json_decode($question->options);

        return formatResponse(STATUS_OK, $question, '', __('messages.question_soft_delete_success'));
    }

    public function restore($id)
    {
        $question = Question::onlyTrashed()->find($id);

        if (!$question) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.question_not_found'));
        }

        $question->deleted_by = auth()->user()->id;
        $question->restore();
        // $question->options = json_decode($question->options);

        return formatResponse(STATUS_OK, $question, '', __('messages.question_restore_success'));
    }

    public function forceDelete($id)
    {
        $question = Question::onlyTrashed()->find($id);

        if (!$question) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.question_not_found'));
        }

        $question->forceDelete();
        // $question->options = json_decode($question->options);

        return formatResponse(STATUS_OK, $question, '', __('messages.question_force_delete_success'));
    }
}
