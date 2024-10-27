<script setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/solid';
import { TListCategoriesChildren } from '@/interfaces/category.interface';
import { defineProps } from 'vue';

const props = defineProps<{
  child: TListCategoriesChildren;
}>();

</script>
<template>
    <div class="hoverItem hover:bg-bg-primary dark:hover:text-black rounded-sm p-1 flex justify-between items-center hover:duration-300">
      <div class="flex gap-2 items-center">
        <FontAwesomeIcon :icon="child.icon" class="w-4" />
        <div class="text-sm">{{child.title}}</div>
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

