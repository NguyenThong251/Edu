<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lecture;
use App\Models\Quiz;
use App\Models\Section;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use FFMpeg\FFMpeg;
use getID3;

class LectureController extends Controller
{
    public function getListAdmin(Request $request)
    {
        // Query cơ bản lấy danh sách Lectures
        $lecturesQuery = Lecture::query();

        // Kiểm tra tham số `deleted`
        if ($request->has('deleted') && $request->deleted == 1) {
            // Lấy các bài giảng đã xóa
            $lecturesQuery->onlyTrashed();
        } else {
            // Chỉ lấy các bài giảng chưa xóa (mặc định)
            $lecturesQuery->whereNull('deleted_at');
        }

        // Lọc theo keyword (nếu có)
        if ($request->has('keyword') && !empty($request->keyword)) {
            $keyword = $request->keyword;
            $lecturesQuery->where('title', 'like', '%' . $keyword . '%');
        }
        if ($request->has('created_by') && !empty($request->created_by)) {
            $lecturesQuery->where('created_by', $request->created_by);
        }
        // Lọc theo section_id (nếu có)
        if ($request->has('section_id') && !empty($request->section_id)) {
            $sectionId = $request->section_id;
            $lecturesQuery->where('section_id', $sectionId);
        }

        // Lọc theo status (nếu có)
        if ($request->has('status') && !is_null($request->status)) {
            $lecturesQuery->where('status', $request->status);
        }
        if ($request->is_instructor == 1) {
            // Lọc theo người dùng hiện tại và đảm bảo trường `created_at` không null
            $lecturesQuery->where('created_by', auth()->id());
        }
        // Sắp xếp theo `created_at` (mặc định là `desc`)
        $order = $request->get('order', 'desc'); // Giá trị mặc định là desc
        $lecturesQuery->orderBy('created_at', $order);

        // Phân trang với per_page và page
        $perPage = (int) $request->get('per_page', 10); // Số lượng bản ghi mỗi trang, mặc định 10
        $page = (int) $request->get('page', 1); // Trang hiện tại, mặc định 1

        // Lấy danh sách đã lọc
        $lectures = $lecturesQuery->get();

        // Tổng số lượng bản ghi
        $total = $lectures->count();

        // Phân trang thủ công
        $paginatedLectures = $lectures->forPage($page, $perPage)->values();

        // Tạo đối tượng LengthAwarePaginator
        $pagination = new LengthAwarePaginator(
            $paginatedLectures, // Dữ liệu cho trang hiện tại
            $total,              // Tổng số bản ghi
            $perPage,            // Số lượng bản ghi mỗi trang
            $page,               // Trang hiện tại
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(), // Đường dẫn chính
                'query' => $request->query() // Lấy tất cả query parameters hiện tại
            ]
        );

        // Chuyển đổi dữ liệu phân trang thành mảng
        $lectures = $pagination->toArray();

        // Trả về kết quả với đầy đủ thông tin filter, order và phân trang
        return formatResponse(
            STATUS_OK,
            $lectures,
            '',
            __('messages.lecture_fetch_success')
        );
    }

    public function showContentBySection($sectionId)
    {
        // Lấy tất cả lectures thuộc section
        $lectures = Lecture::where('section_id', $sectionId)
            ->whereNull('deleted_at') // Lọc các bài giảng chưa bị xóa (hoặc có thể điều chỉnh nếu muốn lấy cả bài giảng đã xóa)
            ->get();
    
        // Thêm thuộc tính content_type cho lecture
        $lectures->each(function ($lecture) {
            $lecture->content_type = 'lecture'; // Gán giá trị 'lecture' cho content_type
        });
    
        // Lấy tất cả quizzes thuộc section
        $quizzes = Quiz::where('section_id', $sectionId)
            ->whereNull('deleted_at') // Lọc các quiz chưa bị xóa (hoặc có thể điều chỉnh nếu muốn lấy cả quiz đã xóa)
            ->get();
    
        // Thêm thuộc tính content_type cho quiz
        $quizzes->each(function ($quiz) {
            $quiz->content_type = 'quiz'; // Gán giá trị 'quiz' cho content_type
        });
    
        // Kết hợp (merge) lectures và quizzes lại với nhau
        $content = $lectures->merge($quizzes);
    
        // Sắp xếp theo order (tăng dần)
        $content = $content->sortBy('order');
    
        // Trả về kết quả
        return formatResponse(STATUS_OK, $content, '', __('messages.content_fetch_success'));
    }
    
    public function updateOrder(Request $request)
    {
        // Kiểm tra xem dữ liệu 'data' có tồn tại trong request không
        $sortedContent = json_decode($request->input('sorted_content'), true);
        // Kiểm tra nếu không có 'sorted_content' hoặc nó không phải là mảng
        if (!$sortedContent || !is_array($sortedContent)) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.invalid_data_format'));
        }

        // Duyệt qua từng item trong mảng data và cập nhật lại order cho từng lecture/quiz
        foreach ($sortedContent as $item) {
            // Kiểm tra nếu phần tử không có id hoặc order
            if (!isset($item['id']) || !isset($item['order'])) {
                continue; // Bỏ qua phần tử không hợp lệ
            }

            // Cập nhật order cho lecture hoặc quiz
            if ($item['content_type'] == 'lecture') {
                $lecture = Lecture::find($item['id']);
                if ($lecture) {
                    $lecture->order = $item['order']; // Cập nhật order mới
                    $lecture->save();
                }
            } elseif ($item['content_type'] == 'quiz') {
                $quiz = Quiz::find($item['id']);
                if ($quiz) {
                    $quiz->order = $item['order']; // Cập nhật order mới
                    $quiz->save();
                }
            }
        }

        // Trả về phản hồi thành công
        return formatResponse(STATUS_OK, '', '', __('messages.order_update_success'));
    }

    public function editForm($id)
    {
        // Tìm quiz theo id, bao gồm cả các bản ghi đã bị xóa mềm
        $lecture = Lecture::withTrashed()->find($id);

        // Nếu không tìm thấy quiz
        if (!$lecture) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.lecture_not_found'));
        }

        // Trả về thông tin của quiz
        return formatResponse(STATUS_OK, $lecture, '', __('messages.lecture_detail_success'));
    }

    public function index(Request $request)
    {
        // Pagination setup
        $perPage = $request->get('per_page', 10);
        $currentPage = $request->get('page', 1);

        // Fetch lectures with related section
        $lectures = Lecture::with('section')->get();

        $total = $lectures->count();
        $paginatedLectures = $lectures->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginationData = [
            'data' => $paginatedLectures,
            'current_page' => $currentPage,
            'last_page' => (int) ceil($total / $perPage),
            'per_page' => $perPage,
            'total' => $total,
        ];

        return formatResponse(STATUS_OK, $paginationData, '', __('messages.lecture_fetch_success'));
    }

    public function show($id)
    {
        $lecture = Lecture::find($id);
        if (!$lecture) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.lecture_not_found'));
        }

        return formatResponse(STATUS_OK, $lecture, '', __('messages.lecture_detail_success'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'type' => 'required|in:video,file',
            'title' => 'required|string|max:255',
            'duration' => 'required',
            'content' => [
                'required',
                'file',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'video' && (!$value->isValid() || $value->getMimeType() !== 'video/mp4' || $value->getSize() > 2048 * 1024 * 1024)) {
                        $fail(__('messages.content_video_invalid'));
                    } elseif ($request->type === 'file' && (!$value->isValid() || $value->getMimeType() !== 'application/pdf' || $value->getSize() > 50 * 1024 * 1024)) {
                        $fail(__('messages.content_file_invalid'));
                    }
                },
            ],
            'preview' => 'required|in:can,cant',
            'status' => 'required|in:active,inactive',
        ], [
            'section_id.required' => __('messages.section_id_required'),
            'duration.required' => __('messages.duration_required'),
            'section_id.exists' => __('messages.section_id_invalid'),
            'type.required' => __('messages.type_required'),
            'type.in' => __('messages.type_invalid'),
            'title.required' => __('messages.title_required'),
            'title.max' => __('messages.title_max'),
            'content.required' => __('messages.content_required'),
            'content.file' => __('messages.content_file'),
            'preview.required' => __('messages.preview_required'),
            'preview.in' => __('messages.preview_invalid'),
            'status.required' => __('messages.status_required'),
            'status.in' => __('messages.status_invalid'),
            'content_video_invalid' => __('messages.content_video_invalid'),
            'content_file_invalid' => __('messages.content_file_invalid'),
        ]);

        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), __('messages.validation_error'));
        }
        


        $lecture = new Lecture();
        $lecture->section_id = $request->section_id;
        $lecture->type = $request->type;
        $lecture->title = $request->title;
        $contentPath = $this->uploadContent($request);
        $lecture->content_link = $contentPath;
        $lecture->duration = $request->duration;

        $sectionId = $request->section_id;

        // Fetch the largest order for lectures in the section
        $maxLectureOrder = Lecture::where('section_id', $sectionId)->max('order');
    
        // Fetch the largest order for quizzes in the section (assuming you have a Quiz model)
        $maxQuizOrder = Quiz::where('section_id', $sectionId)->max('order');
    
        // Determine the largest order (either lecture or quiz)
        $maxOrder = max($maxLectureOrder, $maxQuizOrder);
    
        // Calculate the order for the new lecture (if there are no lectures or quizzes, set order to 1)
        $lecture->order = ($maxOrder) ? $maxOrder + 1 : 1;

        

        $lecture->preview = $request->preview;
        $lecture->status = $request->status;
        $lecture->created_by = $user->id;
        $lecture->save();

        return formatResponse(STATUS_OK, $lecture, '', __('messages.lecture_create_success'));
    }

    public function updateLectureSection(Request $request, $lectureId)
    {
        // Tìm bài giảng cần cập nhật
        $lecture = Lecture::findOrFail($lectureId);

        // Lấy section_id từ request (có thể trả về 404 nếu không có)
        $sectionId = (int)$request->input('section_id');

        // Kiểm tra xem section có tồn tại không
        $section = Section::find($sectionId);
        if (!$section) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.section_not_found'));

        }

        // Cập nhật lại section cho bài giảng
        $lecture->section_id = $sectionId;
        $lecture->save();

        return formatResponse(STATUS_OK, $lecture, '', __('messages.lecture_section_updated'));
    }

    public function updateLectureStatus(Request $request, $lectureId)
    {
        // Tìm bài giảng cần cập nhật
        $lecture = Lecture::findOrFail($lectureId);

        // Lấy trạng thái mới từ request
        $status = $request->input('status');

        // Kiểm tra trạng thái hợp lệ (active hoặc inactive)
        if (!in_array($status, ['active', 'inactive'])) {
            throw new \Exception(__('messages.invalid_status'));
        }

        // Cập nhật trạng thái
        $lecture->status = $status;
        $lecture->save();

        return formatResponse(STATUS_OK, $lecture, '', __('messages.lecture_status_updated'));
    }


    private function uploadContent(Request $request)
    {
        // Xác định thư mục lưu trữ dựa trên loại file
        $folder = $request->type === 'video' ? 'lectures/videos' : 'lectures/files';

        // Tải lên file vào thư mục tương ứng
        $path = $request->file('content')->storePublicly($folder);

        if (!$path) {
            throw new \Exception(__('messages.content_upload_failed'));
        }

        // Trả về đường dẫn đầy đủ của file
        $contentUrl = env('URL_IMAGE_S3') . $path;
        return $contentUrl;
    }

    private function updateContent(Request $request, $lectureId)
    {
        // Tìm bài giảng cần cập nhật
        $lecture = Lecture::findOrFail($lectureId);
        
        // Kiểm tra nếu có file mới và có file cũ cần xóa
        if ($request->hasFile('content')) {
            // Lấy đường dẫn của file cũ (nếu có)
            $oldFile = $lecture->content_link;
            
            // Nếu file cũ tồn tại trên S3, xóa nó
            if ($oldFile) {
                $this->deleteContent($oldFile);
            }

            // Tải lên file mới
            $contentPath = $this->uploadContent($request);

            // Cập nhật đường dẫn mới
            $lecture->content_link = $contentPath;
        }

        // Trả về URL mới của file
        return $lecture->content_link;
    }


    public function deleteContent($contentLink)
    {
        // Lấy đường dẫn hiện tại của tệp (cắt bỏ URL gốc từ S3)
        $currentFilePath = str_replace(env('URL_IMAGE_S3'), '', $contentLink);

        // Kiểm tra xem tệp có tồn tại trên S3 không
        if (Storage::disk('s3')->exists($currentFilePath)) {
            // Xóa tệp khỏi S3
            Storage::disk('s3')->delete($currentFilePath);
        }
    }


    public function update(Request $request, $lectureId)
    {
        // Tìm bài giảng cần cập nhật
        $lecture = Lecture::findOrFail($lectureId);

        $user = auth()->user();

        // Validate đầu vào
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'type' => 'required|in:video,file',
            'title' => 'required|string|max:255',
            'duration' => 'required',
            'content' => [
                'nullable', // content không bắt buộc, chỉ yêu cầu khi có file
                'file',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'video' && (!$value->isValid() || $value->getMimeType() !== 'video/mp4' || $value->getSize() > 2048 * 1024 * 1024)) {
                        $fail(__('messages.content_video_invalid'));
                    } elseif ($request->type === 'file' && (!$value->isValid() || $value->getMimeType() !== 'application/pdf' || $value->getSize() > 50 * 1024 * 1024)) {
                        $fail(__('messages.content_file_invalid'));
                    }
                },
            ],
            'preview' => 'required|in:can,cant',
            'status' => 'required|in:active,inactive',
        ], [
            'section_id.required' => __('messages.section_id_required'),
            'duration.required' => __('messages.duration_required'),
            'section_id.exists' => __('messages.section_id_invalid'),
            'type.required' => __('messages.type_required'),
            'type.in' => __('messages.type_invalid'),
            'title.required' => __('messages.title_required'),
            'title.max' => __('messages.title_max'),
            'content.required' => __('messages.content_required'),
            'content.file' => __('messages.content_file'),
            'preview.required' => __('messages.preview_required'),
            'preview.in' => __('messages.preview_invalid'),
            'status.required' => __('messages.status_required'),
            'status.in' => __('messages.status_invalid'),
            'content_video_invalid' => __('messages.content_video_invalid'),
            'content_file_invalid' => __('messages.content_file_invalid'),
        ]);

        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), __('messages.validation_error'));
        }

        // Cập nhật các trường khác
        $lecture->section_id = $request->section_id;
        $lecture->type = $request->type;
        $lecture->title = $request->title;
        $lecture->duration = $request->duration;
        $lecture->preview = $request->preview;
        $lecture->status = $request->status;

        // Kiểm tra nếu có file mới
        if ($request->hasFile('content')) {
            // Cập nhật nội dung (file) mới
            $contentPath = $this->updateContent($request, $lectureId);
            $lecture->content_link = $contentPath; // Cập nhật URL file mới
        }

        $lecture->save(); // Lưu lại các thay đổi

        return formatResponse(STATUS_OK, $lecture, '', __('messages.lecture_update_success'));
    }

    public function destroy($id)
    {
        $lecture = Lecture::find($id);
        
        // Kiểm tra xem bài giảng có tồn tại không
        if (!$lecture) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.lecture_not_found'));
        }

        // Gán thông tin người xóa
        $lecture->deleted_by = auth()->id();
        
        // Xóa bài giảng (soft delete)
        $lecture->delete();

        // Lấy lại bài giảng đã bị xóa mềm
        $lecture = Lecture::onlyTrashed()->find($id);

        return formatResponse(STATUS_OK, $lecture, '', __('messages.lecture_soft_delete_success'));
    }


    public function restore($id)
    {
        $lecture = Lecture::onlyTrashed()->find($id);

        // Kiểm tra bài giảng có bị xóa mềm hay không
        if (!$lecture) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.lecture_not_found'));
        }

        // Khôi phục bài giảng
        $lecture->deleted_by = null;  // Bỏ thông tin người xóa
        $lecture->restore();
        
        // Lấy lại bài giảng sau khi đã khôi phục
        $lecture = Lecture::find($id);
        
        return formatResponse(STATUS_OK, $lecture, '', __('messages.lecture_restore_success'));
    }
    public function forceDelete($id)
    {
        $lecture = Lecture::onlyTrashed()->find($id);

        // Kiểm tra bài giảng có tồn tại trong danh sách đã xóa mềm hay không
        if (!$lecture) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.lecture_not_found'));
        }

        // Xóa vĩnh viễn bài giảng
        $lecture->forceDelete();

        return formatResponse(STATUS_OK, $lecture, '', __('messages.lecture_force_delete_success'));
    }


}
