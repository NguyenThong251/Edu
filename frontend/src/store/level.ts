import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/axiosConfig'
import type { Tlevel } from '@/interfaces'
import { ElMessage } from 'element-plus'

export interface CourseLevel {
  id?: number
  name: string
  status: string
}

interface CourseLevelState {
  courseLevels: CourseLevel[]
  deletedCourseLevels: CourseLevel[]
  error: string | null
  total: number
}

export const useCourseLevelStore = defineStore('courseLevel', () => {
  const state = ref<CourseLevelState>({
    courseLevels: [],
    deletedCourseLevels: [],
    error: null,
    total: 0
  })

  const fetchCourseLevels = async (params: any = {}) => {
    try {
      const response = await api.get('/auth/list-course-levels-admin', { params })
      state.value.courseLevels = response.data.data.data
      state.value.total = response.data.data.total
    } catch (error) {
      state.value.error = 'Không thể tải danh sách cấp độ!'
    }
  }

  const fetchDeletedCourseLevels = async () => {
    try {
      const response = await api.get('/auth/list-course-levels-admin?deleted=1')
      state.value.deletedCourseLevels = response.data.data.data
    } catch (error) {
      state.value.error = 'Không thể tải danh sách cấp độ đã xóa!'
    }
  }

  const createCourseLevel = async (courseLevelData: FormData) => {
    try {
      const res = await api.post('/auth/course-levels', courseLevelData)
      if (res.data.status === 'FAIL') {
        ElMessage({ type: 'error', message: res.data.error.name[0] })
      } else {
        ElMessage({ type: 'success', message: 'Tạo mới thành công!' })
      }
      await fetchCourseLevels()
    } catch (error) {
      state.value.error = 'Không thể tạo mới cấp độ!'
    }
  }

  const updateCourseLevel = async (courseLevelData: Tlevel) => {
    try {
      const res = await api.put(`/auth/course-levels/${courseLevelData.id}`, courseLevelData)
      if (res.data.status === 'FAIL') {
        ElMessage({ type: 'error', message: res.data.error.name[0] })
      } else {
        ElMessage({ type: 'success', message: 'Tạo mới thành công!' })
      }
      await fetchCourseLevels()
    } catch (error) {
      state.value.error = 'Không thể cập nhật cấp độ!'
    }
  }

  const deleteCourseLevel = async (id: number) => {
    try {
      await api.delete(`/auth/course-levels/${id}`)
      await fetchCourseLevels()
    } catch (error) {
      state.value.error = 'Không thể xóa cấp độ!'
    }
  }

  const restoreCourseLevel = async (id: number) => {
    try {
      await api.get(`/auth/course-levels/restore/${id}`)
      await fetchCourseLevels()
      await fetchDeletedCourseLevels()
    } catch (error) {
      state.value.error = 'Không thể khôi phục cấp độ!'
    }
  }

  return {
    state,
    fetchCourseLevels,
    fetchDeletedCourseLevels,
    createCourseLevel,
    updateCourseLevel,
    deleteCourseLevel,
    restoreCourseLevel
  }
})
