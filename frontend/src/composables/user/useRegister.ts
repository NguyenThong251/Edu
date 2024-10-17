import { ref, watch } from 'vue'
import { useAuthStore } from '@/store/auth'
import { ElNotification } from 'element-plus'
import type { TUserAuth } from '@/interfaces'
import { useRouter } from 'vue-router'
import api from '@/services/axiosConfig'
export function useRegister() {
  const lname = ref<string>('')
  const fname = ref<string>('')
  const email = ref<string>('')
  const password = ref<string>('')

  const fnameError = ref<string>('')
  const lnameError = ref<string>('')
  const emailError = ref<string | null>(null)
  const passwordError = ref<string | null>(null)
  const authStore = useAuthStore()
  const router = useRouter()
  // Hàm validate form đăng ký
  const validateForm = (): boolean => {
    let isValid = true
    if (!fname.value) {
      fnameError.value = 'Nhập tên của bạn'
      isValid = false
    } else {
      fnameError.value = ''
    }
    if (!lname.value) {
      lnameError.value = 'Nhập họ của bạn'
      isValid = false
    } else {
      lnameError.value = ''
    }

    if (!email.value) {
      emailError.value = 'Nhập email của bạn'

      isValid = false
    } else {
      emailError.value = ''
    }

    if (!password.value) {
      passwordError.value = 'Nhập mật khẩu của bạn'

      isValid = false
    } else if (password.value.length < 8) {
      passwordError.value = 'Mật khẩu phải có ít nhất 8 ký tự'
      isValid = false
    } else {
      passwordError.value = ''
    }

    return isValid
  }
  watch(fname, (newVal) => {
    if (newVal) fnameError.value = ''
  })

  watch(lname, (newVal) => {
    if (newVal) lnameError.value = ''
  })

  watch(email, (newVal) => {
    if (newVal) emailError.value = ''
  })

  watch(password, (newVal) => {
    if (newVal && newVal.length >= 8) passwordError.value = ''
  })

  const handleSubmit = async () => {
    if (!validateForm()) return

    const userData: TUserAuth = {
      first_name: fname.value,
      last_name: lname.value,
      email: email.value,
      password: password.value,
      role: 'student'
    }
    try {
      const res = await authStore.register(userData)
      console.log(res)
      if (res.status === 'FAIL') {
        if (res.error.email) {
          emailError.value = res.error.email[0]
        }
        if (res.error.password) {
          passwordError.value = res.error.password[0]
        }
      } else {
        ElNotification({
          title: 'Thành công',
          message: 'Kiểm tra email để xác thực',
          type: 'success'
        })
        router.push('/login')
        await api.post(`/auth/verify-email/${res.access_token}`)
        // await verifyEmail(res.data.verificationToken)
      }
    } catch (error) {
      console.error(error)
    }
  }
  // Hàm xác thực email
  // const verifyEmail = async (verificationToken: string) => {
  //   try {
  //     const response = await api.post(`/api/auth/verify-email/${verificationToken}`)
  //     console.log(response)
  //     if (response.data.status === 'OK') {
  //       ElNotification({
  //         title: 'Thành công',
  //         message: 'Email đã được xác thực thành công, hãy đăng nhập.',
  //         type: 'success'
  //       })

  //       // Redirect to login page
  //       router.push('/login')
  //     } else {
  //       ElNotification({
  //         title: 'Xác thực thất bại',
  //         message: response.data.message || 'Có lỗi xảy ra trong quá trình xác thực email.',
  //         type: 'error'
  //       })
  //     }
  //   } catch (error) {
  //     ElNotification({
  //       title: 'Lỗi kết nối',
  //       message: 'Không thể kết nối đến máy chủ.',
  //       type: 'error'
  //     })
  //     console.error('Email verification error:', error)
  //   }
  // }
  return {
    fname,
    lname,
    email,
    password,
    fnameError,
    lnameError,
    emailError,
    passwordError,
    handleSubmit,
    authStore
  }
}
