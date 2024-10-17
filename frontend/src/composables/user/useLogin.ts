// src/composables/useLogin.ts
import { ref } from 'vue'
import { useAuthStore } from '@/store/auth'
import { ElNotification } from 'element-plus'
export function useLogin() {
  const authStore = useAuthStore()
  const email = ref<string>('')
  const password = ref<string>('')
  const emailError = ref<string | null>(null)
  const passwordError = ref<string | null>(null)
  const loginError = ref<string | null>(null)

  const validateForm = (): boolean => {
    let isValid = true
    if (!email.value) {
      ElNotification({
        title: 'Thất bại',
        message: 'Email không được để trống',
        type: 'error'
      })
      isValid = false
    }
    if (!password.value) {
      ElNotification({
        title: 'Thất bại',
        message: 'Mật khẩu không được để trống',
        type: 'error'
      })
      isValid = false
    } else if (password.value.length < 8) {
      ElNotification({
        title: 'Thất bại',
        message: 'Mật khẩu phải có ít nhất 8 ký tự',
        type: 'error'
      })
      isValid = false
    }

    return isValid
  }
  const handleSubmit = async () => {
    if (!validateForm()) return
    const res = await authStore.login(email.value, password.value)
    console.log(res)
    // Nếu có lỗi từ authStore thì không hiển thị thông báo thành công
    if (res.status === 'FAIL') {
      ElNotification({
        title: 'Thất bại',
        message: res.message,
        type: 'error'
      })
    }
    if (res.status === 'OK') {
      ElNotification({
        title: 'Thành công',
        message: 'Đăng nhập thành công!',
        type: 'success'
      })
    }
    // Chuyển hướng đến trang khác (ví dụ: dashboard)
  }
  return {
    email,
    password,
    emailError,
    passwordError,
    handleSubmit,
    authStore
  }
}
