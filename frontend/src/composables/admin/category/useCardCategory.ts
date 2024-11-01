
import { ref } from "vue";
import axios from "axios";
import Cookies from "js-cookie";
import type { TListCategories } from "@/interfaces/admin.interface";
import { myToken } from "@/interfaces/token";
export default function useFetchCategories() {
  const categories = ref<TListCategories[]>([]);
  const loading = ref<boolean>(true)
  const error = ref<string | null>(null);

  // Lấy token từ Cookies
  // const userToken = ref(Cookies.get('token_user_edu'));
  // console.log("Token:", userToken.value); 
  const userToken = ref(myToken); 
  console.log("Token:", userToken.value);

  // Hàm lấy danh sách danh mục
  const fetchCategories = async () => {
    try {
      const response = await axios.get("http://localhost:8000/api/categories");
      categories.value = response.data.data.data
      console.log(response.data.data.data);
    } catch (error) {
      error.value = "Kết nối đến category không thành công nha";
      console.error(error)
    } finally {
      loading.value=false
    }
  }

   // Hàm cập nhật status cho danh mục
   const updateStatus = async (categoryId: number, newStatus: string | undefined ) => {
     try {
      const response = await axios.patch(
        `http://localhost:8000/api/auth/categories/${categoryId}/status`,
        { status: newStatus },
        {
          headers: {
            Authorization: `Bearer ${userToken.value}`,
            "Content-Type": "application/json",
          },
        }
      );

      if (response.status === 200) {
        // Cập nhật trạng thái của danh mục trong mảng `categories`
        const category = categories.value.find(cat => cat.id === categoryId);
        if (category) {
          category.status = newStatus;
        }
        console.log("Status updated successfully:", response.data);
      }
    } catch (err) {
      console.error("Error updating status:", err);
      alert("Cập nhật trạng thái thất bại.");
    }
  };
  return { categories, loading, error, fetchCategories, updateStatus}
}