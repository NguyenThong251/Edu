import { ref } from 'vue';
import axios from 'axios';
// import Cookies from 'js-cookie';
import type { TListCategories } from '@/interfaces/category.interface';
import { myToken } from '@/interfaces/token';

export default function useEditCategory() {
  // Token lấy từ Cookies (hoặc có thể thay thế bằng phương pháp khác)
  const userToken = ref(myToken);

  // Biến để lưu thông tin danh mục đang chỉnh sửa
  const formDataEdit = ref<TListCategories>({
    id: 0,
    name: '',
    image: undefined,
    icon: '',
    keyword: '',
    description: '',
    status: 'active',
    children: [],
  });

  // Hàm xử lý khi hình ảnh được chọn (Preview)
  const handlePreviewImgEdit = (event: any) => {
    const file = event.target.files?.[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        formDataEdit.value.image = reader.result as string;
      };
      reader.readAsDataURL(file);
    }
  };

  // Hàm gửi dữ liệu chỉnh sửa
  const submitFormEdit = async () => {
    try {
      const requestData = new FormData();
      requestData.append('name', formDataEdit.value.name);
      requestData.append('icon', formDataEdit.value.icon);
      if (formDataEdit.value.image) {
        requestData.append('image', formDataEdit.value.image);
      }
      requestData.append('description', formDataEdit.value.description || '');
      requestData.append('keyword', formDataEdit.value.keyword || '');
      requestData.append('status', formDataEdit.value.status || 'inactive');

      // Gửi yêu cầu PUT để cập nhật danh mục
      const response = await axios.put(
        `http://localhost:8000/api/auth/categories/${formDataEdit.value.id}`,
        requestData,
        {
          headers: {
            Authorization: `Bearer ${userToken.value}`,
            'Content-Type': 'multipart/form-data',
          },
        }
      );

      if (response.status === 200) {
        alert('Cập nhật danh mục thành công');
      } else {
        alert('Có lỗi xảy ra khi cập nhật danh mục');
      }
    } catch (error: any) {
      if (error.response) {
        // Lỗi từ phía server
        alert(error.response.data.message || 'Lỗi từ phía server');
      } else {
        alert('Đã xảy ra lỗi: ' + error.message);
      }
    }
  };

  // Hàm khởi tạo dữ liệu form với thông tin danh mục hiện tại
  const initializeForm = (category: TListCategories) => {
    formDataEdit.value = { ...category };
  };

  return {
    formDataEdit,
    handlePreviewImgEdit,
    submitFormEdit,
    initializeForm,
  };
}
