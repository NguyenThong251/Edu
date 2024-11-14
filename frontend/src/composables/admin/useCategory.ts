import { ref, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { useCategoryStore } from '@/store/category'
import type { TCategory } from '@/interfaces/category.interface'

export function useCategory() {
  const categoryStore = useCategoryStore()
  const dialogVisible = ref(false) // Hiển thị dialog thêm danh mục
  const updateDialogVisible = ref(false) // Hiển thị dialog cập nhật danh mục
  const deletedCategoriesDialogVisible = ref(false) // Hiển thị danh mục đã xóa mềm
  const categoryForm = ref<TCategory>({
    name: '',
    icon: '',
    description: '',
    image: '',
    status: 'active'
  })
  const fileList = ref<any[]>([])
  onMounted(async () => {
    await categoryStore.fetchCategories()
  })

  const openDialog = () => {
    resetForm()
    dialogVisible.value = true
  }

  const openDeletedCategoriesDialog = async () => {
    await categoryStore.fetchDeletedCategories()
    deletedCategoriesDialogVisible.value = true
  }

  const handleSubmit = async () => {
    try {
      const formData = new FormData()

      // // Kiểm tra và thêm giá trị vào FormData
      if (categoryForm.value.name) formData.append('name', categoryForm.value.name)
      if (categoryForm.value.icon) formData.append('icon', categoryForm.value.icon)
      if (categoryForm.value.description)
        formData.append('description', categoryForm.value.description)
      if (categoryForm.value.image instanceof File) {
        formData.append('image', categoryForm.value.image)
      }
      if (categoryForm.value.status) formData.append('status', categoryForm.value.status)
      // console.log(categoryForm.value)
      await categoryStore.createCategory(formData)
      ElMessage({
        type: 'success',
        message: 'Tạo danh mục thành công!'
      })
      dialogVisible.value = false
      await categoryStore.fetchCategories()
    } catch (error) {
      ElMessage({
        type: 'error',
        message: 'Tạo danh mục không thành công!'
      })
    }
  }
  const handleUpdate = async () => {
    try {
      // const formData = new FormData()

      // // Kiểm tra và thêm giá trị vào FormData
      // if (categoryForm.value.name) formData.append('name', categoryForm.value.name)
      // if (categoryForm.value.icon) formData.append('icon', categoryForm.value.icon)
      // if (categoryForm.value.description)
      //   formData.append('description', categoryForm.value.description)
      // if (categoryForm.value.image instanceof File) {
      //   formData.append('image', categoryForm.value.image)
      // }
      // if (categoryForm.value.status) formData.append('status', categoryForm.value.status)

      await categoryStore.updateCategory(categoryForm.value)
      ElMessage({
        type: 'success',
        message: 'Cập nhật danh mục thành công!'
      })
      updateDialogVisible.value = false
      await categoryStore.fetchCategories()
    } catch (error) {
      ElMessage({
        type: 'error',
        message: 'Cập nhật danh mục không thành công!'
      })
    }
  }

  const editCategory = (category: TCategory) => {
    categoryForm.value = { ...category }
    updateDialogVisible.value = true
  }

  const handleDeleteCategory = async (id: number | string) => {
    try {
      await ElMessageBox.confirm('Bạn có chắc chắn muốn xóa danh mục này không?', 'Xác nhận xóa', {
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
        type: 'warning'
      })
      await categoryStore.deleteCategory(id)
      ElMessage({
        type: 'success',
        message: 'Xóa danh mục thành công!'
      })
      await categoryStore.fetchCategories()
    } catch {
      ElMessage({
        type: 'info',
        message: 'Hủy xóa danh mục'
      })
    }
  }
  const handlePictureCardPreview = (file: any) => {
    categoryForm.value.image = file.raw // Gán file đã chọn vào categoryForm
    console.log(file)
  }
  const handleRemoveImage = () => {
    fileList.value = [] // Xóa danh sách file
    categoryForm.value.image = '' // Xóa file khỏi form
  }
  const handleFileChange = (file: any, fileListParam: any[]) => {
    console.log(file)
    fileList.value = fileListParam // Cập nhật danh sách file
    if (file.raw) {
      categoryForm.value.image = file.raw // Gắn file thực tế vào form
    }
  }
  const restoreCategory = async (id: string | number) => {
    try {
      await ElMessageBox.confirm(
        'Bạn có chắc chắn muốn khôi phục danh mục này không?',
        'Xác nhận khôi phục',
        {
          confirmButtonText: 'Khôi phục',
          cancelButtonText: 'Hủy',
          type: 'info'
        }
      )
      await categoryStore.restoreCategory(id)
      ElMessage({
        type: 'success',
        message: 'Khôi phục danh mục thành công!'
      })
      await categoryStore.fetchDeletedCategories()
    } catch {
      ElMessage({
        type: 'info',
        message: 'Hủy khôi phục danh mục'
      })
    }
  }

  const resetForm = () => {
    categoryForm.value = {
      name: '',
      icon: '',
      description: '',
      image: ''
    }
  }

  return {
    categoryStore,
    dialogVisible,
    updateDialogVisible,
    deletedCategoriesDialogVisible,
    categoryForm,
    fileList,
    openDialog,
    openDeletedCategoriesDialog,
    handleSubmit,
    handleUpdate,
    editCategory,
    handleDeleteCategory,
    restoreCategory,
    handlePictureCardPreview,
    handleRemoveImage,
    handleFileChange
  }
}
