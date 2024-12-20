<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Course;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\LengthAwarePaginator;

class SectionController extends Controller
{
    public function getListAdmin(Request $request)
    {
        $sectionsQuery = Section::query();

        if ($request->has('deleted') && $request->deleted == 1) {
            $sectionsQuery->onlyTrashed();
        } else {
            $sectionsQuery->whereNull('deleted_at');
        }

        if ($request->has('keyword') && !empty($request->keyword)) {
            $sectionsQuery->where('title', 'like', '%' . $request->keyword . '%');
        }

        if ($request->has('course_id') && !empty($request->course_id)) {
            $sectionsQuery->where('course_id', $request->course_id);
        }

        if ($request->has('status') && !is_null($request->status)) {
            $sectionsQuery->where('status', $request->status);
        }

        if ($request->is_instructor == 1) {
            $sectionsQuery->where('created_by', auth()->id());
        }

        if ($request->has('created_by') && !empty($request->created_by)) {
            $sectionsQuery->where('created_by', $request->created_by);
        }

        $order = $request->get('order', 'desc');
        $sectionsQuery->orderBy('created_at', $order);

        $perPage = (int) $request->get('per_page', 10);
        $page = (int) $request->get('page', 1);

        $sections = $sectionsQuery->get();
        $total = $sections->count();
        $paginatedSections = $sections->forPage($page, $perPage)->values();

        $pagination = new LengthAwarePaginator(
            $paginatedSections,
            $total,
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        $sections = $pagination->toArray();

        return formatResponse(STATUS_OK, $sections, '', __('messages.section_fetch_success'));
    }

    public function editForm($id)
    {
        $section = Section::withTrashed()->find($id);

        if (!$section) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.section_not_found'));
        }

        return formatResponse(STATUS_OK, $section, '', __('messages.section_detail_success'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ], [
            'course_id.required' => __('messages.course_id_required'),
            'course_id.exists' => __('messages.course_id_invalid'),
            'title.required' => __('messages.title_required'),
            'status.required' => __('messages.status_required'),
            'status.in' => __('messages.status_invalid'),
        ]);

        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), __('messages.validation_error'));
        }

        $section = new Section();
        $section->course_id = $request->course_id;
        $section->title = $request->title;
        $section->description = $request->description;
        $section->status = $request->status;

        $maxOrder = Section::where('course_id', $request->course_id)->max('order');
        $section->order = ($maxOrder) ? $maxOrder + 1 : 1;

        $section->created_by = $user->id;
        $section->save();

        return formatResponse(STATUS_OK, $section, '', __('messages.section_create_success'));
    }

    public function update(Request $request, $sectionId)
    {
        $section = Section::find($sectionId);

        if (!$section) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.section_not_found'));
        }

        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ], [
            'course_id.required' => __('messages.course_id_required'),
            'course_id.exists' => __('messages.course_id_invalid'),
            'title.required' => __('messages.title_required'),
            'status.required' => __('messages.status_required'),
            'status.in' => __('messages.status_invalid'),
        ]);

        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), __('messages.validation_error'));
        }

        $section->course_id = $request->course_id;
        $section->title = $request->title;
        $section->description = $request->description;
        $section->status = $request->status;
        $section->updated_by = auth()->user()->id;

        $section->save();

        return formatResponse(STATUS_OK, $section, '', __('messages.section_update_success'));
    }

    public function updateSectionAttributes(Request $request, $id)
    {
        $section = Section::find($id);

        if (!$section) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.section_not_found'));
        }

        if ($request->has('course_id')) {
            $courseId = (int) $request->input('course_id');
            $course = Course::find($courseId);
            if (!$course) {
                return formatResponse(STATUS_FAIL, '', '', __('messages.course_not_found'));
            }
            $section->course_id = $courseId;
        }

        if ($request->has('status')) {
            $status = $request->input('status');
            if (!in_array($status, ['active', 'inactive'])) {
                return formatResponse(STATUS_FAIL, '', '', __('messages.invalid_status'));
            }
            $section->status = $status;
        }

        $section->save();

        return formatResponse(STATUS_OK, $section, '', __('messages.section_update_success'));
    }

    public function showSectionsByCourse($courseId)
    {
        $sections = Section::where('course_id', $courseId)
            ->whereNull('deleted_at')
            ->orderBy('order', 'asc')
            ->get()
            ->map(function ($section) {
                // Tính tổng số lecture và quiz của section
                $totalLectures = $section->lectures->count();
                $totalQuizzes = $section->quizzes->count();

                // Gán tổng số vào section
                $section->total_contents = $totalLectures + $totalQuizzes;

                return $section;
            });


        return formatResponse(STATUS_OK, $sections, '', __('messages.sections_fetch_success'));
    }

    public function updateSectionOrder(Request $request)
    {
        $sortedSections = $request->input('sorted_sections');

        if (!$sortedSections || !is_array($sortedSections)) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.invalid_data_format'));
        }

        foreach ($sortedSections as $item) {
            if (!isset($item['id']) || !isset($item['order'])) {
                continue;
            }

            $section = Section::find($item['id']);
            if ($section) {
                $section->order = $item['order'];
                $section->save();
            }
        }

        return formatResponse(STATUS_OK, '', '', __('messages.sections_order_update_success'));
    }

    public function destroy($id)
    {
        $section = Section::find($id);

        if (!$section) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.section_not_found'));
        }

        $section->deleted_by = auth()->user()->id;
        $section->delete();

        return formatResponse(STATUS_OK, $section, '', __('messages.section_soft_delete_success'));
    }

    public function restore($id)
    {
        $section = Section::onlyTrashed()->find($id);

        if (!$section) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.section_not_found'));
        }

        $section->restore();

        return formatResponse(STATUS_OK, $section, '', __('messages.section_restore_success'));
    }

    public function forceDelete($id)
    {
        $section = Section::onlyTrashed()->find($id);

        if (!$section) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.section_not_found'));
        }

        $section->forceDelete();

        return formatResponse(STATUS_OK, $section, '', __('messages.section_force_delete_success'));
    }
}
