// stores/courseStore.ts
import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/axiosConfig'

export const useCourseStore = defineStore('courseStore', () => {
  // State
  const course = ref<any>(null)
  const isLoading = ref<boolean>(false)
  const error = ref<string | null>(null)

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

  // Getter
  const getCourse = () => course.value

  return {
    course,
    isLoading,
    error,
    fetchCourseDetail,
    getCourse
  }
})
