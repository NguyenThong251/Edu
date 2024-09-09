import { defineStore } from 'pinia'
import { ref } from 'vue'
import axiosInstance from '@/services/axiosConfig'

import type { AuthState } from '@/interfaces'
// khai báo trạng thái
export const useUserStore = defineStore('auth', () => {
  const state = ref<AuthState>({
    user: null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null
  })

  // Hàm này sẽ được gọi khi ứng dụng khởi động lại
  const fetchUserData = async () => {
    if (state.value.token) {
      // Nếu có token, kiểm tra và xác thực token
      try {
        const response = await axiosInstance.get('/auth/me') // Gửi yêu cầu đến API để lấy thông tin người dùng
        state.value.user = response.data.user // Cập nhật thông tin người dùng
      } catch (err) {
        state.value.token = null // Nếu token không hợp lệ, xóa nó khỏi localStorage
        localStorage.removeItem('token')
      }
    }
  }

  // Gọi hàm này ngay khi store được tạo để khôi phục trạng thái
  fetchUserData()

  return {
    ...state.value,
    fetchUserData
  }
})
