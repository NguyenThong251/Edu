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
import SelectGroup from '@/components/admin/Dialog/SelectGroup.vue';
import Loading from 'vue-loading-overlay';
import { useCate } from '@/composables/admin/useCate';
const sidebarStore = useSidebarStore();
const dialogFormVisible = ref(false);

// const { handlePreviewImg, submitForm, formData, imageUrl } = useAddCategory();

// const { categories, loading, error, fetchCategories } = useFetchCategories()
// onMounted(async () => {
//   await fetchCategories(); // Gọi hàm fetchCategories khi component được mount
//   console.log(categories.value); // In dữ liệu đã lấy được
// });

onMounted(() => {
  apiStore.fetchCategory();
});



const dialogEditVisible = ref(false);
const categoryIdToEdit = ref<number | null>(null); // Biến để lưu id danh mục cần chỉnh sửa

// Hàm mở Dialog chỉnh sửa
const openEditDialog = (id: number) => {
  console.log('Mở dialog chỉnh sửa cho id:', id);
  categoryIdToEdit.value = id;
  // Khởi tạo dữ liệu form với id của danh mục
  const category = categories.value.find((cat: TListCategories) => cat.id === id);
  if (category) {
    initializeForm(category);
  }
  dialogEditVisible.value = true;
  console.log('dialogEditVisible:', dialogEditVisible.value);
};

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
  <!-- <div v-if="loading">Loading...</div> -->
  <Loading :active="loading" :is-full-page="true" />
  <div v-if="error">{{ error }}</div>
  <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
    <CardCategory 
    v-for="(category, index) in apiStore.categories" 
    :key="index" 
    :category="category" 
    @edit="openEditDialog"
    />
  </div>
  <!-- DIALOG (Model) -->
  <DialogArea
    :dialogVisible="dialogFormVisible" 
    class="rounded-[10px] dark:bg-dark-sidebar dark:text-white border p-6 z-30"
    title="Thêm danh mục mới"
    :submitForm="submitForm"
    >
    <template #default>
        <InputGroup
        label="Tên danh mục"
        inputId="title"
        v-model="formData.name"
        @input="console.log('Tên danh mục:', formData.name)"
        />
        <IconGroup
        label="Icon danh mục"
        inputId="icon-picker"
        inputPlaceHoder="Nhập từ khoá"
        v-model="formData.icon"
        @input="console.log('Icon danh mục:', formData.icon)"
        />
        <InputGroup
        label="Từ khoá (không bắt buộc)"
        inputId="keyword"
        v-model="formData.keyword"
        inputPlaceHoder="Nhập từ khoá"
        @input="console.log('Từ khoá (không bắt buộc):', formData.keyword)"
        />
        <DescriptionGroup
        label="Mô tả (không bắt buộc)"
        inputId="description"
        inputPlaceHoder="Nhập mô tả danh mục..."
        v-model="formData.description"
        @input="console.log('mô tả:', formData.description)"
        />
        <UploadGroup
        label="Tải lên hình ảnh thumbnail"
        inputId="upload"
        :imageUrl = "imageUrl"
        :handlePreviewImg="handlePreviewImg"
        />
      </template>
  </DialogArea>
  <!-- DIALOG (Edit) -->
  <DialogArea
    :dialogVisible="dialogEditVisible" 
    class="rounded-[10px] dark:bg-dark-sidebar dark:text-white border p-6 z-30"
    title="Chỉnh sửa danh mục"
    :submitForm="submitForm"
    >
    <template #default>
      <SelectGroup
          inputPlaceHoder="Danh mục gốc"
          required="*"
          label="Danh mục"
          />
      <InputGroup
        label="Tên danh mục"
        inputId="title"
        v-model="formDataEdit.name"
        @input="console.log('Tên danh mục:', formDataEdit.name)"
        />
        <IconGroup
        label="Icon danh mục"
        inputId="icon-picker"
        inputPlaceHoder="Nhập từ khoá"
        v-model="formDataEdit.icon"
        @input="console.log('Icon danh mục:', formDataEdit.icon)"
        />
        <InputGroup
        label="Từ khoá (không bắt buộc)"
        inputId="keyword"
        v-model="formDataEdit.keyword"
        inputPlaceHoder="Nhập từ khoá"
        @input="console.log('Từ khoá (không bắt buộc):', formDataEdit.keyword)"
        />
        <DescriptionGroup
        label="Mô tả (không bắt buộc)"
        inputId="description"
        inputPlaceHoder="Nhập mô tả danh mục..."
        v-model="formDataEdit.description"
        @input="console.log('mô tả:', formDataEdit.description)"
        />
        <UploadGroup
        label="Tải lên hình ảnh thumbnail"
        inputId="upload"
        :imageUrl = "imageUrl"
        :handlePreviewImg="handlePreviewImg"
        />
      </template>
  </DialogArea>
</template>