<template>
  <div class="p-4">
    <HeaderNavbar namePage="Danh mục">
      <ButtonPrimary :icon="FolderPlusIcon" link="#" title="Thêm danh mục" :confirm="addCategory" plain
        @click="dialogFormVisible = true" />
    </HeaderNavbar>
    <!-- Loading Overlay -->
    <Loading :active="loading" :is-full-page="true" />

    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
      <CardCategory v-for="(category, index) in categories" :key="index" :category="category" @edit="openEditDialog" />
    </div>

    <!-- Dialog thêm danh mục -->
    <DialogArea :dialogVisible="dialogFormVisible"
      class="rounded-[10px] dark:bg-dark-sidebar dark:text-white border p-6 z-30" title="Thêm danh mục mới"
      :submitForm="submitForm">
      <template #default>
        <InputGroup label="Tên danh mục" inputId="title" v-model="formData.name" />
        <IconGroup label="Icon danh mục" inputId="icon-picker" inputPlaceHoder="Nhập từ khoá" v-model="formData.icon" />
        <DescriptionGroup label="Mô tả" inputId="description" v-model="formData.description" />
        <UploadGroup label="Tải lên hình ảnh thumbnail" inputId="upload" :imageUrl="imageUrl"
          :handlePreviewImg="handlePreviewImg" />
      </template>
    </DialogArea>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import HeaderNavbar from '@/components/admin/Headernavbar/HeaderNavbar.vue'
import CardCategory from '@/components/admin/Card/CardCategory.vue'
import DialogArea from '@/components/admin/Dialog/DialogArea.vue'
import ButtonPrimary from '@/components/admin/Button/ButtonPrimary.vue'
import InputGroup from '@/components/admin/Dialog/InputGroup.vue'
import IconGroup from '@/components/admin/Dialog/IconGroup.vue'
import DescriptionGroup from '@/components/admin/Dialog/DescriptionGroup.vue'
import UploadGroup from '@/components/admin/Dialog/UploadGroup.vue'
import { useSidebarStore } from '@/store/sidebar'
import { useCategory } from '@/composables/admin/useCategory'
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/css/index.css'
import { storeToRefs } from 'pinia'

const sidebarStore = useSidebarStore()
const dialogFormVisible = ref(false)
const { categories, loading, fetchCategories, handlePreviewImg, addCategory, formData, imageUrl } = useCategory()
console.log(categories)
onMounted(async () => {
  await fetchCategories()
})
</script>
