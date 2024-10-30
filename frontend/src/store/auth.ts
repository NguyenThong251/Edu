import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/axiosConfig'
import type { AuthState, TUserAuth } from '@/interfaces'
import Cookies from 'js-cookie'
import { useRouter } from 'vue-router'
import { ElNotification } from 'element-plus'
const router = useRouter()
export const useAuthStore = defineStore('auth', () => {
  const state = ref<AuthState>({
    user: null,
    token: Cookies.get('token_user_edu') || null,
    loading: false,
    error: null
  })
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
  const logout = () => {
    state.value.user = null
    state.value.token = null
    Cookies.remove('token_user_edu')
  }
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
  const userData = async () => {
    if (!state.value.token) return
    state.value.loading = true
    try {
      const response = await api.get('/auth/profile')
      const isToken = await response.data
      if (isToken.status === 'FAIL') {
        logout()
        router.push('/')
        ElNotification({
          title: 'Thất bại',
          message: isToken.message || 'Bạn không có quyền truy cập',
          type: 'error'
        })
        return null
      }
      state.value.user = await response.data.data
      // console.log(state.value.user)
      // return response.data.data
    } catch (err: any) {
      state.value.error = err.response?.data?.message || 'Registration failed'
    } finally {
      state.value.loading = false
    }
  }
  const forgotPass = async (email: string) => {
    state.value.loading = true
    state.value.error = null
    try {
      const response = await api.post('/auth/forgot-password', { email })
      return response.data
    } catch (err: any) {
      state.value.error = err.response?.data?.message || 'Yêu cầu quên mật khẩu thất bại'
    } finally {
      state.value.loading = false
    }
  }

  const resetPass = async (token: string, password: string) => {
    state.value.loading = true
    state.value.error = null
    try {
      const response = await api.post(`/auth/reset-password/${token}`, { password })
      return response.data
    } catch (err: any) {
      state.value.error = err.response?.data?.message || 'Đặt lại mật khẩu thất bại'
    } finally {
      state.value.loading = false
    }
  }
  const getGoogleSignInUrl = async (role: string) => {
    try {
      const response = await api.post('/auth/get-google-sign-in-url', { role })
      return response.data.data.url
    } catch (err: any) {
      state.value.error = err.response?.data?.message || 'Failed to get Google sign-in URL'
    }
  }

  // const handleGoogleCallback = async (code: string) => {
  //   state.value.loading = true
  //   state.value.error = null
  //   try {
  //     const response = await api.get(`/auth/google/call-back?code=${code}`)
  //     state.value.token = response.data.access_token
  //     state.value.user = response.data.user
  //     Cookies.set('token_user_edu', response.data.access_token, { expires: 7 }) // Save token
  //     return response.data
  //   } catch (err: any) {
  //     state.value.error = err.response?.data?.message || 'Google login failed'
  //   } finally {
  //     state.value.loading = false
  //   }
  // }
  const handleGoogleCallback = async (jwtToken: string) => {
    state.value.loading = true
    try {
      Cookies.set('token_user_edu', jwtToken, { expires: 7 })
      state.value.token = jwtToken
      return true
    } catch (error) {
      console.error('Error handling Google callback:', error)
      return false
    } finally {
      state.value.loading = false
    }
  }
  return {
    state,
    login,
    logout,
    register,
    userData,
    forgotPass,
    resetPass,
    getGoogleSignInUrl,
    handleGoogleCallback
    // fetchUserData
  }
})
