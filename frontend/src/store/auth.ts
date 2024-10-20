import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/axiosConfig'
import type { AuthState, TUserAuth } from '@/interfaces'
import Cookies from 'js-cookie'
export const useAuthStore = defineStore('auth', () => {
  // khai báo trạng thái
  const state = ref<AuthState>({
    user: null,
    token: Cookies.get('token_user_edu') || null,
    loading: false,
    error: null
  })

  // fetch data user
  // const fetchUserData = async () => {
  //   if (token.value) {
  //     try {
  //       const res = await api.get('/auth/profile')
  //       user.value = res.data.data
  //     } catch (err) {
  //       console.error('Lỗi khi lấy dữ liệu người dùng:', err)
  //     }
  //   }
  // }
  // fetchUserData()
  // hàm đăng nhập

  const login = async (email: string, password: string) => {
    state.value.loading = true
    state.value.error = null
    try {
      const response = await api.post('/auth/login', { email, password })
      state.value.token = response.data.access_token
      Cookies.set('token_user_edu', response.data.access_token, { expires: 7 }) // Lưu token vào localStorage
      return response.data
    } catch (err: any) {
      state.value.error = err.response?.data?.message || 'Đăng nhập thật bại'
    } finally {
      state.value.loading = false
    }
  }
  // hàm đăng xuất
  const logout = () => {
    state.value.user = null
    state.value.token = null
    Cookies.remove('token_user_edu')
  }
  // hàm đăng ký
  const register = async (userData: TUserAuth) => {
    state.value.loading = true
    state.value.error = null
    try {
      const response = await api.post('/auth/register', userData)
      return response.data
    } catch (err: any) {
      state.value.error = err.response?.data?.message || 'Registration failed'
    } finally {
      state.value.loading = false
    }
  }

  return {
    state,
    login,
    logout,
    register
    // fetchUserData
  }
})
