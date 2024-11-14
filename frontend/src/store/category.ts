import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/axiosConfig'
import { ElNotification } from 'element-plus'
import type { TCategory } from '@/interfaces'

export const useCategoryStore = defineStore('category', () => {
  const state = ref({
    categories: [] as TCategory[], // Danh sách danh mục
    deletedCategories: [] as TCategory[], // Danh sách danh mục đã xóa mềm
    selectedCategory: null as TCategory | null, // Danh mục chi tiết
    error: null as string | null // Thông báo lỗi
  })

  // Lấy danh sách danh mục
  const fetchCategories = async () => {
    try {
      const response = await api.get('/categories')
      state.value.categories = response.data.data.data
    } catch (error) {
      state.value.error = 'Không thể tải danh sách danh mục'
    }
  }

  // Tạo danh mục mới
  const createCategory = async (categoryData: FormData) => {
    try {
      await api.post('/auth/categories', categoryData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })

      await fetchCategories()

      //   ElNotification({
      //     title: 'Thành công',
      //     message: 'Tạo danh mục mới thành công',
      //     type: 'success'
      //   })
    } catch (error) {
      state.value.error = 'Không thể tạo danh mục mới'
      //   ElNotification({
      //     title: 'Thất bại',
      //     message: 'Tạo danh mục mới thất bại',
      //     type: 'error'
      //   })
    }
  }

  // Cập nhật danh mục
  const updateCategory = async (categoryData: TCategory) => {
    try {
      await api.put(`/auth/categories/${categoryData.id}`, categoryData)
      await fetchCategories()
      ElNotification({
        title: 'Thành công',
        message: 'Cập nhật danh mục thành công',
        type: 'success'
      })
    } catch (error) {
      state.value.error = 'Không thể cập nhật danh mục'
      ElNotification({
        title: 'Thất bại',
        message: 'Cập nhật danh mục thất bại',
        type: 'error'
      })
    }
  }

  // Xóa mềm danh mục
  const deleteCategory = async (id: string | number) => {
    try {
      await api.delete(`/auth/categories/${id}`)
      await fetchCategories()
      ElNotification({
        title: 'Thành công',
        message: 'Xóa danh mục thành công',
        type: 'success'
      })
    } catch (error) {
      state.value.error = 'Không thể xóa danh mục'
      ElNotification({
        title: 'Thất bại',
        message: 'Xóa danh mục thất bại',
        type: 'error'
      })
    }
  }

  // Khôi phục danh mục đã xóa mềm
  const restoreCategory = async (id: string | number) => {
    try {
      await api.get(`/auth/categories/restore/${id}`)
      await fetchCategories()
      ElNotification({
        title: 'Thành công',
        message: 'Khôi phục danh mục thành công',
        type: 'success'
      })
    } catch (error) {
      state.value.error = 'Không thể khôi phục danh mục'
      ElNotification({
        title: 'Thất bại',
        message: 'Khôi phục danh mục thất bại',
        type: 'error'
      })
    }
  }

  // Lấy danh sách danh mục đã xóa mềm
  const fetchDeletedCategories = async () => {
    try {
      const response = await api.get('/auth/categories/deleted')
      state.value.deletedCategories = response.data.data
    } catch (error) {
      state.value.error = 'Không thể tải danh sách danh mục đã xóa'
    }
  }

  // Lấy thông tin chi tiết danh mục
  const fetchCategoryDetails = async (id: number) => {
    try {
      const response = await api.get(`/auth/categories/${id}`)
      state.value.selectedCategory = response.data.data
    } catch (error) {
      state.value.error = 'Không thể tải thông tin danh mục'
    }
  }

  return {
    state,
    fetchCategories,
    createCategory,
    updateCategory,
    deleteCategory,
    restoreCategory,
    fetchDeletedCategories,
    fetchCategoryDetails,
    categories: computed(() => state.value.categories),
    deletedCategories: computed(() => state.value.deletedCategories),
    selectedCategory: computed(() => state.value.selectedCategory)
  }
})
