<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import useAddCategory from '@/composables/admin/category/useAddCategory';
import { PlusIcon } from '@heroicons/vue/20/solid';

const {handlePreviewImg,
submitForm,formData,imageUrl} = useAddCategory();


</script>


<template>
    <div class="mt-4 ml-4">
      <form class="space-y-4" @submit.prevent="submitForm">
        <div>
          <label class="w-[120px] inline-block"  for="name">Tên danh mục:</label>
          <input v-model="formData.name" type="text" id="name" required />
        </div>

        <div class="flex">
          <label class="w-[120px] inline-block" for="description">Mô tả:</label>
          <textarea rows="1" v-model="formData.description" id="description"></textarea>
        </div>
        <div>
          <label class="w-[120px] inline-block" for="status">Trạng thái:</label>
          <select v-model="formData.status" id="status" required>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>

        <div>
          <label class="w-[120px] inline-block" for="parent_id">Parent Category:</label>
          <input v-model="formData.parent_id" type="number" id="parent_id" />
        </div>
        <div>
         <div class="relative size-[200px] border border-stone-500 rounded-lg group flex items-center justify-center">
            <label class=" size-24 group-hover:inline-block transition-all duration-300 translate-y-2 group-hover:translate-y-0 opacity-0 group-hover:opacity-100" for="image">
           <PlusIcon class="text-white"/>
            </label>
            <img v-if="imageUrl" :src="imageUrl" class="w-full h-full object-cover absolute -z-10" />
            <input @change="handlePreviewImg" class="hidden" type="file" id="image">
        </div>
        </div>
        <Button type="submit">Thêm danh mục</Button>
      </form>
    </div>
  </template>
  


  