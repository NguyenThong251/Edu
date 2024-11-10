<script setup lang="ts">
import CategoryImgWeb from '@/assets/images/CategoryImgWeb.jpeg'
import { PlusCircleIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/solid';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import type { TListCategories } from '@/interfaces/category.interface';
import { computed, defineEmits, onMounted, ref } from 'vue';
import ItemsChild from './ItemsChild.vue';
import CardActionButton from './CardActionButton.vue';
import { useDarkModeStore } from '@/store/darkmode';
import * as solidIcons from '@fortawesome/free-solid-svg-icons';
import updateStatus from '@/composables/admin/category/useCardCategory';
const useUpdateStatus = updateStatus();

const darkModeStore = useDarkModeStore()

// Hàm tính toán để lấy icon từ tên
const mappedIcon = computed(() => {
  // Sử dụng props.category.icon để ánh xạ
  const iconName = props.category.icon;
  // Tìm icon trong solidIcons bằng cách lấy giá trị của iconName
  return solidIcons[iconName as keyof typeof solidIcons] || solidIcons.faQuestionCircle;
});

const props = defineProps<{
  category: TListCategories;
}>();


const isActive = computed(() => props.category.status === 'active')
// Hàm gọi updateStatus khi thay đổi trạng thái
const toggleStatus = (categoryId: number, currentStatus: string | undefined) => {
  const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
  useUpdateStatus.updateStatus(categoryId, newStatus);
};

//Sửa category
const emit = defineEmits(['editCategory']);
// Hàm phát sự kiện khi nhấn nút "Sửa"
const handleEditClick = () => {
  emit('editCategory', props.category.id); // Phát ra sự kiện với id của danh mục
  console.log(props.category.id);
};
</script>
<template>
  <div class="bg-white dark:bg-dark-sidebar rounded-lg dark:border group hover:duration-700">
    <div class="p-3 h-full flex flex-wrap justify-center">
      <div class="w-full relative">
        <img :src="props.category.image" class="w-full  h-[170px] object-cover rounded-t-md" alt="">
        <el-tooltip class="box-item" :effect="darkModeStore.darkMode ? 'light' : 'dark'"
          :content="props.category.status === 'active' ? 'Dừng kích hoạt' : 'Kích hoạt'" placement="top-start">
          <label class="inline-flex items-center cursor-pointer absolute p-3 top-0 right-0">
            <input type="checkbox" :checked="isActive" @change="toggleStatus(props.category.id, props.category.status)"
              class="sr-only peer">
            <div
              class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
            </div>
          </label>
        </el-tooltip>
        <div class="py-4">
          <div class="flex justify-between pb-4 border-b">
            <div class="flex gap-2 items-center">
              <FontAwesomeIcon :icon="mappedIcon" class="w-5" />
              <div class="font-bold">{{ props.category.name }}</div>
            </div>
            <div class="">({{ props.category.children?.length }})</div>
          </div>
          <ItemsChild v-for="(CategoryItemChild, index) in props.category.children" :key="index"
            :child="CategoryItemChild" />
        </div>
      </div>
      <div class="flex justify-center invisible gap-4 pb-5 group-hover:visible">
        <CardActionButton :link="props.category.id" :icon="PlusCircleIcon" text="Thêm" />
        <CardActionButton link="#" :icon="PencilSquareIcon" text="Sửa" @click="handleEditClick" />
        <CardActionButton link="#" :icon="TrashIcon" text="Xoá" />
      </div>
    </div>
  </div>
</template>
