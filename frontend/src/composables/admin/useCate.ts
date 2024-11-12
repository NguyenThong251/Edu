import api from "@/services/axiosConfig";
import { ElNotification } from "element-plus";
import { ref } from "vue";
import { apisStore } from '@/store/apis';
import useAddCategory from "./category/useAddCategory";
const apiStore = apisStore()

export function useCate() {
  const imageUrl = ref<string | null>(null)
  const loading = ref(false)
  const fetchCategory = () => {
    apiStore.fetchCate();
  }
  const AddNewCate = () => {
    useAddCategory
  }

  return {
    submitForm,
    handlePreviewImg,
    fetchCategory
  }
}