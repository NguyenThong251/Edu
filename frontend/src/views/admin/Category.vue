<template>
  <div class="p-4">
    <HeaderNavbar namePage="Danh mục">
      <el-button type="primary" class="flex items-center gap-1" @click="openDialog">
        <PlusIcon class="h-5 w-5 text-white cursor-pointer" />
        Thêm Danh Mục
      </el-button>
    </HeaderNavbar>

    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
      <CardCategory v-for="(category, index) in categoryStore.state.categories" :key="index" :category="category" />
    </div>

    <!-- Dialog Thêm Danh Mục -->
    <el-dialog v-model="dialogVisible" title="Thêm Danh Mục" width="50%">
      <form @submit.prevent="handleSubmit">
        <div class="mb-4">
          <label for="name" class="block text-sm font-medium">
            Tên danh mục
          </label>
          <el-input id="name" v-model="categoryForm.name" type="text" placeholder="Nhập tên danh mục" class="w-full" />
        </div>
        <div class="mb-4">
          <label for="icon" class="block text-sm font-medium">
            Icon danh mục
          </label>
          <el-input id="icon" v-model="categoryForm.icon" type="text" placeholder="Nhập icon" class="w-full" />
        </div>
        <div class="mb-4">
          <label for="status" class="block text-sm font-medium">Trạng thái</label>
          <el-select v-model="categoryForm.status" id="status" placeholder="Chọn trạng thái">
            <el-option label="Hoạt động" value="active" />
            <el-option label="Không hoạt động" value="inactive" />
          </el-select>
        </div>
        <div class="mb-4">
          <label for="description" class="block text-sm font-medium">
            Mô tả
          </label>
          <el-input type="textarea" id="description" v-model="categoryForm.description"
            placeholder="Nhập mô tả danh mục" class="w-full" :rows="4" />
        </div>
        <div class="mb-4">
          <label for="image" class="block text-sm font-medium">
            Tải lên hình ảnh
          </label>
          <el-upload list-type="picture-card" :auto-upload="false" :file-list="fileList" :on-change="handleFileChange"
            :on-remove="handleRemoveImage" accept="image/*" :limit="1">
            <el-icon>
              <Plus />
            </el-icon>
          </el-upload>
        </div>
        <div class="flex justify-end mt-4">
          <el-button @click="dialogVisible = false" class="mr-2">Hủy</el-button>
          <el-button type="primary" native-type="submit">Lưu</el-button>
        </div>
      </form>
    </el-dialog>
  </div>
</template>
<script setup lang="ts">
import { onMounted, ref } from 'vue'
import HeaderNavbar from '@/components/admin/Headernavbar/HeaderNavbar.vue'
import CardCategory from '@/components/admin/Card/CardCategory.vue'
import { PlusIcon } from '@heroicons/vue/20/solid'
import { useCategory } from '@/composables/admin/useCategory'
import { Plus } from '@element-plus/icons-vue'
import { storeToRefs } from 'pinia'
import { apisStore } from '@/store/apis'
const fetchData = apisStore()
const { fetchCate } = fetchData
const { categories } = storeToRefs(fetchData)
onMounted(async () => {
  await fetchCate()
})
const handlePictureCardPreview = (file: any) => {
  // console.log(file)
  categoryForm.value.image = file.raw; // Gán file đã chọn vào form
};
// Sử dụng composable
const {
  categoryStore,
  dialogVisible,
  categoryForm,
  openDialog,
  handleSubmit,
  handleDeleteCategory,
  handleRemoveImage,
  handleFileChange,
  // handlePictureCardPreview,
  fileList
} = useCategory()
</script>
