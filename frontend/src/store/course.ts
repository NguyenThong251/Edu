// stores/courseStore.ts
import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/axiosConfig'
import type {
  TContentOfSection,
  TCardCourse,
  TCardMyCourse,
  TLecture,
  TSectionOfCourse,
  TSection
} from '@/interfaces/course.interface'
import type { TChangeContent } from '@/interfaces/ui.interface'
import { ElMessage, ElMessageBox } from 'element-plus'
import { id } from 'element-plus/es/locale/index.mjs'

export const useCourseStore = defineStore('courseStore', () => {
  // State
  const course = ref<any>()
  const listCourseTeacher = ref<any>()
  const isLoading = ref<boolean>(false)
  const error = ref<string | null>(null)
  const myCourses = ref<TCardMyCourse[]>([])
  // study
  const currentContent = ref<any>({})
  const allContent = ref<any[]>([])
  const courseStudySearch = ref<any[]>([])
  const progress = ref(0)
  const activeNames = ref(['0'])
  const studyCourse = ref<any>(null)
  const listContentOfSection = ref<TContentOfSection[]>([])
  const listLecturesAdmin = ref<TContentOfSection[]>([])
  const dataForm = ref<TContentOfSection>()
  const loading = ref(false)
  // Actions

  const fetchCourseDetail = async (courseId: string) => {
    isLoading.value = true
    try {
      const response = await api.get(`/courses/${courseId}`)
      course.value = response.data.data
      error.value = null
    } catch (err: any) {
      error.value = 'Đã có lỗi xảy ra khi lấy thông tin khóa học'
    } finally {
      isLoading.value = false
    }
  }
  const fetchMyCourseFilter = async (title: string, creator: string) => {
    isLoading.value = true
    try {
      const response = await api.get('/auth/get-user-courses', {
        params: {
          title: title || undefined,
          creator: creator || undefined
        }
      })
      myCourses.value = response.data.data
      error.value = null
    } catch (err: any) {
      error.value = 'Đã có lỗi xảy ra khi tìm kiếm khóa học'
    } finally {
      isLoading.value = false
    }
  }
  const fetchMyCourse = async () => {
    isLoading.value = true
    try {
      const res = await api.get('/auth/get-user-courses')
      myCourses.value = res.data.data.map((course: any) => ({
        id: course.id,
        thumbnail: course.thumbnail,
        title: course.title,
        creator: course.creator,
        total_lectures: course.total_lectures,
        completed_lectures: course.completed_lectures,
        progress_percent: course.progress_percent
      }))
      error.value = null
    } catch (err: any) {
      console.error('Fetch My Courses Error:', err)
      error.value = 'Đã có lỗi xảy ra khi lấy danh sách khóa học'
    } finally {
      isLoading.value = false
    }
  }
  const fetchStudyCourse = async (courseId: number) => {
    try {
      isLoading.value = true
      const response = await api.get('/auth/study-course', {
        params: { course_id: courseId }
      })
      studyCourse.value = response.data.data
      currentContent.value = response.data.data.currentContent
      allContent.value = response.data.data.allContent
      progress.value = response.data.data.progress_percent
      error.value = null
    } catch (err: any) {
      console.error('Fetch Study Course Error:', err)
      error.value = 'Đã có lỗi xảy ra khi lấy thông tin khóa học'
    } finally {
      isLoading.value = false
    }
  }
  const changeContent = async (data: TChangeContent) => {
    try {
      const response = await api.get('/auth/change-content', {
        params: {
          course_id: data.course_id,
          content_type: data.content_type,
          content_id: data.content_id,
          learned: data.learned || 0,
          content_old_type: data.content_old_type || currentContent.value.type,
          content_old_id: currentContent.value.id
        }
      })
      currentContent.value = response.data.data.currentContent
      allContent.value = response.data.data.allContent // Cập nhật toàn bộ nội dung mới
      progress.value = response.data.data.progress_percent // Cập nhật tiến trình
    } catch (err) {
      console.error('Error changing content:', err)
      error.value = 'Không thể chuyển bài học.'
    }
  }

  const searchLetureStudy = async (course_id: number, content_keyword: string) => {
    try {
      const response = await api.get('/auth/change-content', {
        params: {
          course_id: course_id,
          content_keyword: content_keyword
        }
      })
      courseStudySearch.value = response.data.data.allContent
    } catch (error) {
      console.error('Error changing content:', error)
    }
  }

  // Teacher
  const fetchTeacherCourse = async (params: any = {}) => {
    try {
      const response = await api.get('/auth/instructor/course', { params })
      listCourseTeacher.value = response.data.data
    } catch (error) {
      console.error(error)
    }
  }

  // ADMIM COURSE

  //course

  // lecture

  const showContentOfSection = async (id: number) => {
    try {
      const res = await api.get(`/auth/show-content-of-section/${id}`)
      listContentOfSection.value = res.data.data
    } catch (error) {
      console.log(error)
    }
  }

  const fetchListLecturesAdmin = async (params: any) => {
    try {
      const res = await api.get('/auth/lectures', { params })
      listLecturesAdmin.value = res.data.data
    } catch (error) {
      console.log(error)
    }
  }

  const lectureEditFrom = async (id: number) => {
    try {
      const res = await api.get(`auth/lectures/edit-form/${id}`)
      dataForm.value = res.data.data
    } catch (error) {
      console.log(error)
    }
  }

  const createLecture = async (id: number, data: FormData) => {
    try {
      loading.value = true
      const res = await api.post('auth/lectures', data, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })

      if (res.data.status === 'FAIL') {
        ElMessage.error('Thêm bài học thất bại')
      } else {
        ElMessage.success('Thêm bài học thành công')
        await showContentOfSection(id)
        // await fetchListLecturesAdmin()
      }
    } catch (error) {
      ElMessage.error('Thêm bài học thất bại')
      console.log(error)
    } finally {
      loading.value = false // Tắt trạng thái loading
    }
  }

  const updateLecture = async (id: number, id_lecture: number, data: TLecture) => {
    try {
      const res = await api.post(`auth/lectures/${id_lecture}`, data)

      if (res.data.status === 'FAIL') {
        ElMessage.error('Cập nhật bài học thất bại')
      } else {
        ElMessage.success('Cập nhật bài học thành công')
        await showContentOfSection(id)
        // await fetchListLecturesAdmin()
      }
    } catch (error) {
      ElMessage.error('Cập nhật bài học thất bại')
      console.log(error)
    }
  }

  const deleteLecture = async (id: number, id_lecture: number) => {
    try {
      const res = await api.delete(`auth/lectures/${id_lecture}`)

      if (res.data.status === 'FAIL') {
        ElMessage.error('Xóa bài học thất bại')
      } else {
        ElMessage.success('Xóa bài học thành công')
        await showContentOfSection(id)
        // await fetchListLecturesAdmin()
      }
    } catch (error) {
      ElMessage.error('Xóa bài học thất bại')
      console.log(error)
    }
  }

  const updateSectionLecture = async (
    id: number,
    id_lecture: number,
    data: { section_id: number }
  ) => {
    try {
      const res = await api.patch(`auth/lectures/${id_lecture}/section`, {
        params: data
      })

      if (res.data.status === 'FAIL') {
        ElMessage.error('Cập nhật bài học thất bại')
      } else {
        ElMessage.success('Cập nhật bài học thành công')
        await showContentOfSection(id)
        // await fetchListLecturesAdmin()
      }
    } catch (error) {
      ElMessage.error('Cập nhật bài học thất bại')
      console.log(error)
    }
  }
  const updateStatusLecture = async (
    id: number,
    id_lecture: number,
    data: { status: 'active' | 'inactive' }
  ) => {
    try {
      const res = await api.patch(`auth/lectures/${id_lecture}/status`, {
        params: data
      })

      if (res.data.status === 'FAIL') {
        ElMessage.error('Cập nhật bài học thất bại')
      } else {
        ElMessage.success('Cập nhật bài học thành công')
        await showContentOfSection(id)
        // await fetchListLecturesAdmin()
      }
    } catch (error) {
      ElMessage.error('Cập nhật bài học thất bại')
      console.log(error)
    }
  }
  const sortContentOfSection = async (
    id: number,
    data: { sorted_content: TContentOfSection[] }
  ) => {
    try {
      const res = await api.put('auth/sort-content-of-section', {
        params: data
      })

      if (res.data.status === 'FAIL') {
        ElMessage.error('Cập nhật bài học thất bại')
      } else {
        ElMessage.success('Cập nhật bài học thành công')
        await showContentOfSection(id)
        // await fetchListLecturesAdmin()
      }
    } catch (error) {
      ElMessage.error('Cập nhật bài học thất bại')
      console.log(error)
    }
  }

  // quizz

  // section

  const listSection = ref<TSectionOfCourse[]>([])

  const showSectionOfCourse = async (id: number) => {
    try {
      const res = await api.get(`auth/show-sections-of-course/${id}`)
      listSection.value = res.data.data
    } catch (error) {
      console.log(error)
    }
  }

  const createSection = async (id: number, data: TSection) => {
    try {
      const res = await api.post('auth/sections', data)
      if (res.data.status === 'FAIL') {
        ElMessage.error('Thêm chương thất bại')
      } else {
        ElMessage.success('Thêm chương thành công')
        await showSectionOfCourse(id)
      }
    } catch (error) {
      console.log(error)
    }
  }

  const updateSection = async (id: number, section_id: number, data: TSection) => {
    try {
      const res = await api.put(`auth/sections/${section_id}`, data)
      if (res.data.status === 'FAIL') {
        ElMessage.error('Cập nhật chương thất bại')
      } else {
        ElMessage.success('Cập nhật chương thành công')
        await showSectionOfCourse(id)
      }
    } catch (error) {
      console.log(error)
    }
  }
  const deleteSection = async (id: number, section_id: number) => {
    try {
      await ElMessageBox.confirm(
        'Bạn có chắc chắn muốn xóa chương học này không?',
        'Xác nhận xóa',
        {
          confirmButtonText: 'Xóa',
          cancelButtonText: 'Hủy',
          type: 'info'
        }
      )
      const res = await api.delete(`auth/sections/${section_id}`)
      if (res.data.status === 'FAIL') {
        ElMessage.error('Xóa chương thất bại')
      } else {
        ElMessage.success('Xóa chương thành công')
        await showSectionOfCourse(id)
      }
    } catch (error) {
      console.log(error)
    }
  }
  const deletePermanentSection = async (id: number, section_id: number) => {
    try {
      await ElMessageBox.confirm(
        'Bạn có chắc chắn muốn xóa vĩnh viễn chương học này không?',
        'Xác nhận xóa',
        {
          confirmButtonText: 'Xóa',
          cancelButtonText: 'Hủy',
          type: 'info'
        }
      )
      const res = await api.delete(`auth/sections/permanent-delete/${section_id}`)
      if (res.data.status === 'FAIL') {
        ElMessage.error('Xóa chương thất bại')
      } else {
        ElMessage.success('Xóa chương thành công')
        await showSectionOfCourse(id)
      }
    } catch (error) {
      console.log(error)
    }
  }

  const sortSectionsOfCourse = async (sortedData: TSection[]) => {
    try {
      await api.put('/auth/sort-sections-of-course', {
        sorted_sections: sortedData
      })
      ElMessage.success('Cập nhật thứ tự chương học thành công')
    } catch (error) {
      console.error('Lỗi khi sắp xếp chương học:', error)
      ElMessage.error('Không thể cập nhật thứ tự chương học')
    }
  }

  // Getter
  const getCourse = () => course.value
  fetchMyCourse()
  return {
    loading,
    listCourseTeacher,
    courseStudySearch,
    studyCourse,
    course,
    myCourses,
    isLoading,
    error,
    currentContent,
    allContent,
    progress,
    fetchCourseDetail,
    getCourse,
    fetchMyCourse,
    fetchStudyCourse,
    changeContent,
    fetchMyCourseFilter,
    searchLetureStudy,
    fetchTeacherCourse,
    // admin
    // lecture
    listContentOfSection,
    listLecturesAdmin,
    dataForm,
    showContentOfSection,
    fetchListLecturesAdmin,
    lectureEditFrom,
    createLecture,
    updateLecture,
    deleteLecture,
    updateSectionLecture,
    updateStatusLecture,
    sortContentOfSection,
    // section
    listSection,
    showSectionOfCourse,
    createSection,
    updateSection,
    deleteSection,

    sortSectionsOfCourse
  }
})
