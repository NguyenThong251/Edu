
import { ref } from "vue";
import axios from "axios";
import type { TListCategories } from "@/interfaces/admin.interface";

export default function useFetchCategories() {
  const categories = ref<TListCategories[]>([]);
  const loading = ref<boolean>(true)
  const error = ref<string | null>(null);

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
  return { categories, loading, error, fetchCategories}
}