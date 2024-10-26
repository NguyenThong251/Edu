import type { TCourseFilters } from '@/interfaces/course.interface'
import api from '@/services/axiosConfig'
import { ref } from 'vue'

export function useFilter() {
  // States

  const coursesFilter = ref<any[]>([])
  const totalCourses = ref(0)
  const loading = ref(false)
  const noProduct = ref(false)
  const currentPage = ref(1)
  // Fetch courses based on filters
  const fetchCourseFilter = async (
    page = 1,
    limit = 12,
    perPage = 12,
    filters: TCourseFilters = {}
  ) => {
    loading.value = true
    noProduct.value = false

    try {
      // Extract filter parameters from passed object
      const { duration_range, max_rating, category_ids, level_id, sort_by, sort_order, keyword } =
        filters

      // Build query params
      const categoryIds = category_ids ? category_ids.join(',') : ''
      const level = level_id ? level_id.join(',') : ''

      const response = await api.get('/courses', {
        params: {
          limit,
          per_page: perPage,
          page,
          category_ids: categoryIds,
          duration_range,
          min_rating: 0,
          max_rating,
          level_id: level,
          sort_by,
          sort_order,
          keyword
        }
      })

      if (response.data.data.data.length > 0) {
        coursesFilter.value = response.data.data.data
        totalCourses.value = response.data.data.data.length
      } else {
        noProduct.value = true
        coursesFilter.value = []
      }
    } catch (error) {
      console.error('Error fetching courses:', error)
      noProduct.value = true
    } finally {
      loading.value = false
    }
  }

  return {
    fetchCourseFilter,
    coursesFilter,
    totalCourses,
    loading,
    noProduct,
    currentPage
  }
}
