import type { TListCategories } from '@/interfaces/category.interface'
import axios from 'axios'
import Cookies from 'js-cookie'
import { ref } from 'vue'
// import { myToken } from "@/interfaces/token";

export default function useAddCategory() {
  // const userToken = ref(Cookies.get('token_user_edu'))
  const imageUrl = ref<string | null>(null)
  // const userToken = ref(myToken);
  // console.log('Token:', userToken.value)

  const formData = ref({
    name: '',
    image: null,
    description: '',
    icon: '',
    keyword: '',
    status: 'active',
    children: null
  })

  function handlePreviewImg(event: any) {
    const file = event.target.files?.[0]
    if (file) {
      const reader = new FileReader()
      reader.onloadend = () => {
        imageUrl.value = reader.result as string
      }
      formData.value.image = file
      reader.readAsDataURL(file)
    }
  }

  const submitForm = async () => {
    try {
      const requestData = new FormData()
      requestData.append('name', formData.value.name)
      requestData.append('icon', formData.value.icon)
      if (formData.value.image) {
        requestData.append('image', formData.value.image)
      }
      requestData.append('description', formData.value.description || '')
      requestData.append('keyword', formData.value.keyword || '')
      requestData.append('status', formData.value.status || 'inactive')
      if (formData.value.children !== null) {
        requestData.append('children', JSON.stringify(formData.value.children))
      }

      const response = await axios.post('http://localhost:8000/api/auth/categories/', requestData, {
        // headers: {
        //   Authorization: `Bearer ${userToken.value}`,
        //   'Content-Type': 'multipart/form-data'
        // }
      })
      // Kiểm tra phản hồi có thành công hay không
      if (response.status === 200 || response.status === 201) {
        console.log('Response data:', response.data)
        2
        alert(response.data.message)
        alert('Thêm thành công rồi bạn ei')
      } else {
        // Nếu phản hồi không phải thành công, hiển thị thông báo lỗi
        console.error('Error response:', response.data)
        alert(response.data.message || 'Có lỗi xảy ra từ phía server.')
      }
    } catch (error: any) {
      // Xử lý lỗi khi có lỗi xảy ra trong quá trình gửi yêu cầu
      if (error.response) {
        // Lỗi từ phía server
        const { status, data } = error.response
        console.error(`Error ${status}: ${data.message}`)
        alert(data.message || 'Đã xảy ra lỗi từ phía server.')
      } else if (error.request) {
        // Không nhận được phản hồi từ server
        console.error('No response received from server.')
        alert('Không nhận được phản hồi từ server.')
      } else {
        // Lỗi trong khi thiết lập yêu cầu
        console.error('Error:', error.message)
        alert('Đã xảy ra lỗi: ' + error.message)
      }
    }
  }

  return {
    handlePreviewImg,
    submitForm,
    formData,
    imageUrl
  }
}
