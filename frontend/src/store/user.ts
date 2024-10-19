import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/axiosConfig'
import Cookies from 'js-cookie'
import type { AuthState } from '@/interfaces'
// khai báo trạng thái
export const useUserStore = defineStore('auth', () => {
  const state = ref<AuthState>({
    user: null,
    token: Cookies.get('token_user_edu') || null,
    loading: false,
    error: null
  })

  const fetchUserData = async () => {
    if (state.value.token) {
      try {
        const response = await api.get('/auth/profile')
        state.value.user = response.data.user
      } catch (error) {
        console.error('Lỗi khi lấy dữ liệu người dùng:', error)
      }
    }
  }

  return {
    state,
    fetchUserData
  }
})
