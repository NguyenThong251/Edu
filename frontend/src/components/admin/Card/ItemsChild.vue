<script setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/solid';
import { TListCategoriesChildren } from '@/interfaces/category.interface';
import { defineProps, computed} from 'vue';

import * as solidIcons from '@fortawesome/free-solid-svg-icons';
import * as regularIcons from '@fortawesome/free-regular-svg-icons';
import * as brandsIcons from '@fortawesome/free-brands-svg-icons';

// Hàm tính toán để lấy icon từ tên
const mappedIconChild = computed(() => {
  // Sử dụng props.category.icon để ánh xạ
  const iconName = props.child.icon;
  // Tìm icon trong solidIcons bằng cách lấy giá trị của iconName
  return solidIcons[iconName as keyof typeof solidIcons] || regularIcons[iconName as keyof typeof regularIcons] || brandsIcons[iconName as keyof typeof brandsIcons];
});
const props = defineProps<{
  child: TListCategoriesChildren;
}>();

</script>
<template>
    <div class="hoverItem hover:bg-bg-primary dark:hover:text-black rounded-sm p-1 flex justify-between items-center hover:duration-300">
      <div class="flex gap-2 items-center">
        <FontAwesomeIcon :icon="mappedIconChild" class="w-4" />
        <div class="text-sm">{{child.name}}</div>
      </div>
      <div class="flex gap-2">
        <router-link class="hidden overItemChild" to="#" >
          <PencilSquareIcon class="w-4"/>
        </router-link>
        <router-link class="hidden overItemChild"  to="#" >
          <TrashIcon class="w-4"/>
        </router-link>
      </div>
    </div>
</template>

