<script setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/solid';
import { ListCategoriesChildren } from '@/interfaces/admin.interface';
import { defineProps } from 'vue';

const props = defineProps<{
  child: ListCategoriesChildren;
}>();

</script>
<template>
    <div class="group hover:bg-bg-primary dark:hover:text-black rounded-sm p-1 flex justify-between items-center hover:duration-300">
      <div class="flex gap-2 items-center">
        <FontAwesomeIcon :icon="child.icon" class="w-4" />
        <div class="text-sm">{{child.title}}</div>
      </div>
      <div class="flex gap-2">
        <router-link class="hidden duration-300 group-hover:block" to="#" >
          <PencilSquareIcon class="w-4"/>
        </router-link>
        <router-link class="hidden mduration-300 group-hover:block"  to="#" >
          <TrashIcon class="w-4"/>
        </router-link>
      </div>
    </div>
</template>

