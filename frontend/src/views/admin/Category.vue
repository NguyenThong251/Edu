<script setup lang="ts">
import HeaderNavbar from '@/components/admin/Headernavbar/HeaderNavbar.vue'
import { useSidebarStore } from '@/store/sidebar';
import CardCategory from '@/components/admin/Card/CardCategory.vue';
import DialogArea from '@/components/admin/Dialog/DialogArea.vue'
import { ref } from 'vue'
import type { TListCategories } from '@/interfaces';
import { FolderPlusIcon } from '@heroicons/vue/24/solid';
import ButtonPrimary from '@/components/admin/Button/ButtonPrimary.vue';
import InputGroup from '@/components/admin/Dialog/InputGroup.vue';
import IconGroup from '@/components/admin/Dialog/IconGroup.vue';
import DescriptionGroup from '@/components/admin/Dialog/DescriptionGroup.vue';
import UploadGroup from '@/components/admin/Dialog/UploadGroup.vue';

const sidebarStore = useSidebarStore();
const dialogFormVisible = ref(false);

const listCategories = ref<TListCategories[]>([]);

// Hàm để thêm danh mục mới
// const addCategory = (newCategory: TListCategories) => {
//   console.log('Đang tạo mới danh mục:', newCategory);
//   listCategories.value.push(newCategory); 
//   dialogFormVisible.value = false; 
// };

const formAddNewData = ref<TListCategories>({
  img: '',
  icon: '',
  title: '',
  keyword: '',
  description: '',
  children: []
});
// Hàm để thêm danh mục mới
const addCategory = () => {
  // props.onAddCategory({ ...formAddNewData.value });
  console.log('Dữ liệu hiện tại:', formAddNewData.value); 
  const newCategory = { ...formAddNewData.value }; 
  console.log('Kiểm tra dữ liệu from:', newCategory);
  formAddNewData.value = { title: '', icon: '', keyword: '', description: '', img: '', children: [] }; // Reset form
};

</script>
<template>
  <div class="p-4">
    <HeaderNavbar  :namePage="sidebarStore.page" >
      <ButtonPrimary
        :icon="FolderPlusIcon"
        link="#"
        title="Thêm danh mục"
        :confirm="addCategory"
        plain @click="dialogFormVisible = true"
        />
    </HeaderNavbar>
  </div>
  <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
    <CardCategory />
  </div>
  <!-- DIALOG (Model) -->
  <DialogArea
    v-model="dialogFormVisible" 
    class="rounded-[10px] dark:bg-dark-sidebar dark:text-white border p-6 z-30"
    title="Thêm danh mục mới"
  >
  <template #default>
      <InputGroup
        label="Tên danh mục"
        inputId="title"
        v-model="formAddNewData.title"
        />
        <IconGroup
        label="Icon danh mục"
        inputId="icon-picker"
        inputPlaceHoder="Nhập từ khoá"
        v-model="formAddNewData.icon"
        />
        <InputGroup
        label="Từ khoá (không bắt buộc)"
        inputId="keyword"
        v-model="formAddNewData.keyword"
        inputPlaceHoder="Nhập từ khoá"
        />
        <DescriptionGroup
        label="Từ khoá (không bắt buộc)"
        inputId="description"
        inputPlaceHoder="Nhập mô tả danh mục..."
        v-model="formAddNewData.description"
        />
        <UploadGroup
        label="Tải lên hình ảnh thumbnail"
        inputId="upload"
        v-model="formAddNewData.img"
        />
      </template>
  </DialogArea>
</template>
