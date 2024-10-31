<script setup lang="ts">
import HeaderNavbar from '@/components/admin/Headernavbar/HeaderNavbar.vue'
import { useSidebarStore } from '@/store/sidebar';
import CardCategory from '@/components/admin/Card/CardCategory.vue';
import DialogArea from '@/components/admin/Dialog/DialogArea.vue'
import { onMounted, ref } from 'vue'
import type { TListCategories } from '@/interfaces';
import { FolderPlusIcon } from '@heroicons/vue/24/solid';
import ButtonPrimary from '@/components/admin/Button/ButtonPrimary.vue';
import InputGroup from '@/components/admin/Dialog/InputGroup.vue';
import IconGroup from '@/components/admin/Dialog/IconGroup.vue';
import DescriptionGroup from '@/components/admin/Dialog/DescriptionGroup.vue';
import UploadGroup from '@/components/admin/Dialog/UploadGroup.vue';
import useAddCategory from '@/composables/admin/category/useAddCategory';
// const pageTitle = route.meta.title;
const sidebarStore = useSidebarStore();
const dialogFormVisible = ref(false);
const listCategories = ref<TListCategories[]>([]);
const AddCategory = useAddCategory();

import useFetchCategories from '@/composables/admin/category/useCardCategory';
const {categories, loading, error, fetchCategories} = useFetchCategories()
onMounted(async () => {
  await fetchCategories(); // Gọi hàm fetchCategories khi component được mount
  console.log(categories.value); // In dữ liệu đã lấy được
});


// Hàm để thêm danh mục mới
// const addCategory = () => {
//   // props.onAddCategory({ ...formAddNewData.value });
//   console.log('Dữ liệu hiện tại:', formAddNewData.value); 
//   const newCategory = { ...formAddNewData.value }; 
//   console.log('Kiểm tra dữ liệu from:', newCategory);
//   formAddNewData.value = { name: '', icon: '', keyword: '', description: '', image: '', parent_id: [] }; // Reset form
// };

</script>
<template>
  <div class="p-4">
    <HeaderNavbar  namePage="Danh mục" >
      <ButtonPrimary
        :icon="FolderPlusIcon"
        link="#"
        title="Thêm danh mục"
        :confirm="addCategory"
        plain @click="dialogFormVisible = true"
        />
    </HeaderNavbar>
  </div>
  <div v-if="loading">Loading...</div>
  <div v-if="error">{{ error }}</div>
  <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
    <CardCategory v-for="(category, index) in categories" :key="index" :category="category" />
  </div>
  <!-- DIALOG (Model) -->
  <!-- <DialogArea
    v-model="dialogFormVisible" 
    class="rounded-[10px] dark:bg-dark-sidebar dark:text-white border p-6 z-30"
    title="Thêm danh mục mới"
    :submitForm="formAddNewData.submitForm"
    >
    <template #default>
      <InputGroup
        label="Tên danh mục"
        inputId="title"
        v-model="formAddNewData.formData.name"
        />
        <IconGroup
        label="Icon danh mục"
        inputId="icon-picker"
        inputPlaceHoder="Nhập từ khoá"
        v-model="formAddNewData.formData.icon"
        />
        <InputGroup
        label="Từ khoá (không bắt buộc)"
        inputId="keyword"
        v-model="formAddNewData.formData.keyword"
        inputPlaceHoder="Nhập từ khoá"
        />
        <DescriptionGroup
        label="Từ khoá (không bắt buộc)"
        inputId="description"
        inputPlaceHoder="Nhập mô tả danh mục..."
        v-model="AddCategory.formData.description"
        />
        <UploadGroup
        label="Tải lên hình ảnh thumbnail"
        inputId="upload"
        :imageUrl = "AddCategory.imageUrl"
        :handlePreviewImg="AddCategory.handlePreviewImg"
        />
      </template>
  </DialogArea> -->
</template>
