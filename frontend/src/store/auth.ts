import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/axiosConfig'
import type { AuthState } from '@/interfaces'

export const useAuthStore = defineStore('auth', () => {
  // khai báo trạng thái
  const state = ref<AuthState>({
    user: null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null
  })

  // hàm đăng nhập

  const login = async (email: string, password: string) => {
    state.value.loading = true
    state.value.error = null
    try {
      const response = await api.post('/auth/login', { email, password })
      state.value.user = response.data.user
      state.value.token = response.data.token
      localStorage.setItem('token', response.data.token) // Lưu token vào localStorage
    } catch (err: any) {
      state.value.error = err.response?.data?.message || 'Login failed'
    } finally {
      state.value.loading = false
    }
  }
  // hàm đăng xuất
  const logout = () => {
    state.value.user = null
    state.value.token = null
    localStorage.removeItem('token') // Xóa token khỏi localStorage
  }
  // hàm đăng ký
  const register = async (userData: any) => {
    state.value.loading = true
    state.value.error = null
    try {
      await api.post('/auth/register', userData)
    } catch (err: any) {
      state.value.error = err.response?.data?.message || 'Registration failed'
    } finally {
      state.value.loading = false
    }
  }

  return {
    ...state.value,
    login,
    logout,
    register
  }
})
