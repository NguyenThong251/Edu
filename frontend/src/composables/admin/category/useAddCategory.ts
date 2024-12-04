import Cookies from 'js-cookie'
import { ref } from 'vue'

export default function useAddCategory() {
  const userToken = ref(Cookies.get('token_user_edu'))
  const imageUrl = ref<string | null>(null)

  const formData = ref({
    name: '',
    image: null,
    description: '',
    status: 'active',
    parent_id: null
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
      if (formData.value.image) {
        requestData.append('image', formData.value.image)
      }
      requestData.append('description', formData.value.description)
      requestData.append('status', formData.value.status)
      if (formData.value.parent_id !== null) {
        requestData.append('parent_id', String(formData.value.parent_id))
      }

      const response = await fetch('http://localhost:8000/api/auth/categories', {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${userToken.value}`
        },
        body: requestData
      })

      const result = await response.json()
      if (response.ok) {
        alert('Thêm thành công rồi bạn ei')
      } else {
        console.error(result)
        alert('Lỗi rồi bạn ei :(')
      }
    } catch (error) {
      console.error(error)
      alert('Lỗi rồi bạn ei')
    }
  }

  return {
    handlePreviewImg,
    submitForm,
    formData,
    imageUrl
  }
}
