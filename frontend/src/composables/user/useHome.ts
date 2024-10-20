import { ref } from 'vue'
import api from '@/services/axiosConfig'

export function useHome() {
  const categories = ref([])

  const fetchCate = async () => {
    try {
      const res = await api.get('/categories')
      console.log(res)
      categories.value = res.data // Assuming 'data' contains the array of categories
    } catch (error) {
      console.error('Error fetching categories:', error)
    }
  }

  return {
    categories,
    fetchCate
  }
}
