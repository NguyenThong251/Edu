import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/axiosConfig'
import type { AuthState, TUserAuth } from '@/interfaces'
import Cookies from 'js-cookie'
export const useAuthStore = defineStore('auth', () => {
  // khai báo trạng thái
  const user = ref<TUserAuth | null>(null)
  const token = ref<string | null>(Cookies.get('token_user_edu') || null)
  const loading = ref(false)
  const error = ref<string | null>(null)

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
    loading.value = true
    error.value = null
    try {
      const response = await api.post('/auth/login', { email, password })
      token.value = response.data.access_token
      Cookies.set('token_user_edu', response.data.access_token, { expires: 7 }) // Lưu token vào localStorage
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Đăng nhập thật bại'
    } finally {
      loading.value = false
    }
  }
  // hàm đăng xuất
  const logout = () => {
    user.value = null
    token.value = null
    Cookies.remove('token_user_edu')
  }
  // hàm đăng ký
  const register = async (userData: any) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.post('/auth/register', userData)
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Registration failed'
    } finally {
      loading.value = false
    }
  }

  return {
    user,
    token,
    loading,
    error,
    login,
    logout,
    register
    // fetchUserData
  }
})
