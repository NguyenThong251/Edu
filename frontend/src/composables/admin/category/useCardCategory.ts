import { ref } from 'vue'
import type { TListCategories } from '@/interfaces/category.interface'
import api from '@/services/axiosConfig'
// import { myToken } from "@/interfaces/token";
export default function useFetchCategories() {
  const categories = ref([])
  const loading = ref<boolean>(true)
  const error = ref<string | null>(null)

  // Lấy token từ Cookies
  // const userToken = ref(Cookies.get('token_user_edu'));
  // console.log("Token:", userToken.value);
  // const userToken = ref(myToken)
  // console.log('Token:', userToken.value)

  // Hàm lấy danh sách danh mục
  const fetchCategories = async () => {
    try {
      const response = await api.get('/categories')
      categories.value = response.data.data.data
      console.log('đây là ds danh mục:',response.data.data.data);
    } catch (error) {
      // error.value = 'Kết nối đến category không thành công nha'
      console.error(error)
    } finally {
      loading.value = false
    }
  }

  // Hàm cập nhật status cho danh mục
  const updateStatus = async (categoryId: number, newStatus: string | undefined) => {
    try {
      const response = await api.patch(
        `/auth/categories/${categoryId}/status`,
        { status: newStatus }
      )

      if (response.status === 200) {
        // Cập nhật trạng thái của danh mục trong mảng `categories`
        const category = categories.value.find((cat: any) => cat.id === categoryId)
        if (category) {
          category.status = newStatus
        }
        console.log('Status updated successfully:', response.data)
      }
    } catch (err) {
      console.error('Error updating status:', err)
      alert('Cập nhật trạng thái thất bại.')
    }
  }
  return { categories, loading, error, fetchCategories, updateStatus }
}
