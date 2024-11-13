import { ref } from 'vue'
import api from '@/services/axiosConfig'
import type { TListCategories } from '@/interfaces/category.interface'

export function useCategory() {
  const categories = ref<TListCategories[]>([])
  const loading = ref(false)
  const formData = ref({
    name: '',
    icon: '',
    image: null,
    description: '',
    status: 'active'
  })
  const formDataEdit = ref<TListCategories>({
    name: '',
    icon: '',
    image: null,
    description: '',
    status: 'active'
  })
  const imageUrl = ref<string | null>(null)

  const fetchCategories = async () => {
    loading.value = true
    try {
      const response = await api.get('/categories')
      categories.value = response.data.data
      console.log(categories.value)
    } catch (error) {
      console.error('Lỗi khi tải danh mục:', error)
    } finally {
      loading.value = false
    }
  }

  const handlePreviewImg = (event: Event, isEdit = false) => {
    const file = (event.target as HTMLInputElement).files?.[0]
    if (file) {
      const reader = new FileReader()
      reader.onloadend = () => {
        if (isEdit) {
          imageUrl.value = reader.result as string
          formDataEdit.value.image = file
        } else {
          imageUrl.value = reader.result as string
          formData.value.image = file
        }
      }
      reader.readAsDataURL(file)
    }
  }

  const addCategory = async () => {
    loading.value = true
    try {
      const requestData = new FormData()
      requestData.append('name', formData.value.name)
      requestData.append('icon', formData.value.icon)
      if (formData.value.image) {
        requestData.append('image', formData.value.image)
      }
      const response = await api.post('/categories', requestData)
      console.log('Danh mục được thêm:', response.data)
    } catch (error) {
      console.error('Lỗi khi thêm danh mục:', error)
    } finally {
      loading.value = false
    }
  }

  const editCategory = async () => {
    loading.value = true
    try {
      const requestData = new FormData()
      requestData.append('name', formDataEdit.value.name)
      requestData.append('icon', formDataEdit.value.icon)
      if (formDataEdit.value.image) {
        requestData.append('image', formDataEdit.value.image)
      }
      requestData.append('description', formDataEdit.value.description || '')
      requestData.append('status', formDataEdit.value.status)

      const response = await api.put(`/auth/categories/${formDataEdit.value.id}`, requestData)
      console.log('Danh mục được chỉnh sửa:', response.data)

      // Cập nhật danh sách danh mục sau khi chỉnh sửa
      await fetchCategories()
    } catch (error) {
      console.error('Lỗi khi chỉnh sửa danh mục:', error)
    } finally {
      loading.value = false
    }
  }
  fetchCategories()
  return {
    categories,
    loading,
    formData,
    formDataEdit,
    imageUrl,
    fetchCategories,
    handlePreviewImg,
    addCategory,
    editCategory
  }
}
