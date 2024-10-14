import { ref } from 'vue'
import { useAuthStore } from '@/store/auth'
import { ElNotification } from 'element-plus'
import type { User } from '@/interfaces'

export function useRegister() {
  const name = ref<string>('')
  const nameError = ref<string | null>(null)
  const email = ref<string>('')
  const emailError = ref<string | null>(null)
  const password = ref<string>('')
  const passwordError = ref<string | null>(null)
  const authStore = useAuthStore()
  // Hàm validate form đăng ký
  const validateForm = (): boolean => {
    if (!name.value) {
      nameError.value = 'Tên không được để trống'
      ElNotification({
        title: 'Lỗi',
        message: 'Tên không được để trống',
        type: 'error'
      })
      return false
    }

    if (!email.value) {
      emailError.value = 'Email không được để trống'
      ElNotification({
        title: 'Lỗi',
        message: 'Email không được để trống',
        type: 'error'
      })
      return false
    }

    if (!password.value) {
      passwordError.value = 'Mật khẩu không được để trống'
      ElNotification({
        title: 'Lỗi',
        message: 'Mật khẩu không được để trống',
        type: 'error'
      })
      return false
    } else if (password.value.length < 8) {
      passwordError.value = 'Mật khẩu phải có ít nhất 8 ký tự'
      ElNotification({
        title: 'Lỗi',
        message: 'Mật khẩu phải có ít nhất 8 ký tự',
        type: 'error'
      })
      return false
    }

    return true
  }
  const handleSubmit = async () => {
    console.log(name.value, email.value, password.value)
    if (!validateForm()) return
    const userData: User = {
      id: '',
      username: name.value,
      email: email.value,
      passwrod: password.value,
      role: 'student',
      image: '',
      createdAt: new Date()
    }
    const res = await authStore.register(userData)
  }
  return {
    name,
    email,
    password,
    handleSubmit,
    authStore
  }
}
