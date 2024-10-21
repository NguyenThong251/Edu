import { computed, ref } from 'vue'
import api from '@/services/axiosConfig'
import type { TCardCourse, TCategory } from '@/interfaces'

export function useHome() {
  const categories = ref<TCategory[]>([])
  const courses = ref<TCardCourse[]>([])
  const fetchCate = async () => {
    try {
      const res = await api.get('/categories')
      categories.value = res.data.data.data
    } catch (error) {
      console.error('Error fetching categories:', error)
    }
  }

  const fetchCourse = async () => {
    try {
      const res = await api.get('/course/index')
      courses.value = res.data.data
    } catch (error) {
      console.error('Error fetching categories:', error)
    }
  }

  const activeCourses = computed(() => {
    return courses.value.filter((course) => course.status === 'active')
  })
  return {
    courses,
    activeCourses,
    categories,
    fetchCate,
    fetchCourse
  }
}
