// stores/wishlistStore.ts
import { defineStore } from 'pinia'
import { ref, watchEffect } from 'vue'
import api from '@/services/axiosConfig'
import Cookies from 'js-cookie'
import { ElNotification } from 'element-plus'
import type { TCardCourse } from '@/interfaces'

export const useWishlistStore = defineStore('wishlist', () => {
  const wishlist = ref<TCardCourse[]>([])
  const error = ref<string | null>(null)
  const isAuthenticated = ref<boolean>(!!Cookies.get('token_user_edu'))

  watchEffect(() => {
    isAuthenticated.value = !!Cookies.get('token_user_edu')
  })

  // Kiểm tra user

  // Thêm khóa học vào wishlist
  const addToWishlist = async (courseId: number) => {
    if (isAuthenticated.value) {
      try {
        const response = await api.post('/auth/wishlist', { course_id: courseId })
        if (response.data.status === 'OK') {
          await fetchWishlist() // Tải lại danh sách wishlist
        } else {
          error.value = response.data.message || 'Đã xảy ra lỗi'
        }
      } catch (err) {
        error.value = 'Không thể thêm khóa học vào danh sách yêu thích'
      }
    } else {
      ElNotification({
        title: 'Thông báo',
        message: 'Bạn cần đăng nhập để thêm vào yêu thích',
        type: 'warning',
        duration: 1000
      })
    }
  }

  // Lấy danh sách wishlist
  const fetchWishlist = async () => {
    try {
      const response = await api.get('/auth/wishlist')
      wishlist.value = response.data.data || []
    } catch (err) {
      error.value = 'Không thể tải danh sách yêu thích'
    }
  }

  // Xóa khóa học khỏi wishlist
  const removeFromWishlist = async (courseId: number) => {
    if (isAuthenticated.value) {
      try {
        const response = await api.post('/auth/delete-wishlist', { course_id: courseId })
        if (response.data.status === 'OK') {
          await fetchWishlist() // Tải lại danh sách wishlist
        } else {
          error.value = response.data.message || 'Đã xảy ra lỗi'
        }
      } catch (err) {
        error.value = 'Không thể xóa khóa học khỏi danh sách yêu thích'
      }
    } else {
      ElNotification({
        title: 'Thông báo',
        message: 'Bạn cần đăng nhập để thêm vào yêu thích',
        type: 'warning',
        duration: 1000
      })
    }
  }

  // Khởi tạo store và tải danh sách wishlist
  fetchWishlist()

  return {
    wishlist,
    error,
    addToWishlist,
    fetchWishlist,
    removeFromWishlist
  }
})