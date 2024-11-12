
import type { TListCategories } from '@/interfaces/category.interface';
import api from '@/services/axiosConfig';
import { Loading } from '@element-plus/icons-vue';
import axios from 'axios'
import { ElNotification } from 'element-plus';
import Cookies from 'js-cookie'
import { ref } from 'vue'

export default function useAddCategory() {
  const userToken = ref(Cookies.get('token_user_edu'))
  const imageUrl = ref<string | null>(null)
  console.log("Token:", userToken.value);

  const formData = ref({
    name: '',
    image: null,
    description: '',
    icon: '',
    keyword: '',
    status: 'active',
    children: null,
  });

  function handlePreviewImg(event: any) {
    const file = event.target.files?.[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        imageUrl.value = reader.result as string;
      };
      formData.value.image = file;
      reader.readAsDataURL(file);
    }
  }

  const submitForm = async () => {
    Loading.value = true
    try {
      const requestData = new FormData();
      requestData.append('name', formData.value.name);
      requestData.append('icon', formData.value.icon);
      if (formData.value.image) {
        requestData.append('image', formData.value.image);
      }
      requestData.append('description', formData.value.description || '');
      requestData.append('keyword', formData.value.keyword || '');
      requestData.append('status', formData.value.status || 'inactive');
      if (formData.value.children !== null) {
        requestData.append('children', JSON.stringify(formData.value.children));
      }

      const response = await api.post('/auth/categories/', requestData);
      ElNotification({
        title: 'Thành công',
        message: 'Đã thêm danh mục khoá học.',
        type: 'success'
      })
    } catch (error: any) {
      ElNotification({
        title: 'Thông báo',
        message: 'Thêm danh mục thất bại.',
        type: 'warning'
      })
    }
  }

  return {
    handlePreviewImg,
    submitForm,
    formData,
    imageUrl,
  };
}