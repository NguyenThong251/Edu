// composables/useCourseDetail.js
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/axiosConfig' // Đường dẫn đến file cấu hình axios

export function useCourseDetail() {
  const route = useRoute()
  const id = route.params.id ? String(route.params.id) : null // Chuyển đổi `id` thành kiểu số
  // State
  const course = ref<any>(null)
  const isLoading = ref<boolean>(false)
  const error = ref<string | null>(null)

  // Hàm để lấy dữ liệu chi tiết khóa học
  const fetchCourseDetail = async () => {
    isLoading.value = true

    try {
      const response = await api.get(`/courses/${id}`)
      course.value = response.data.data
      console.log(response)
      error.value = null
    } catch (err) {
      // error.value = 'Đã có lỗi xảy ra khi lấy thông tin khóa học'
    } finally {
      isLoading.value = false
    }
  }

  // Gọi hàm khi component được gắn vào
  onMounted(() => {
    fetchCourseDetail()
  })

  return {
    course,
    isLoading,
    error
  }
}
