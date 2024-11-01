<script setup lang="ts">
import { defineProps } from 'vue';
const props = defineProps<{
  link: any;
  icon: any; 
  text: string; 
}>();
</script>

<template>
  <div>
    <router-link :to="link">
      <div class="flex gap-1 items-center">
        <component :is="icon" class="w-4"/> <p class="text-sm">{{ text }}</p>
      </div>
    </router-link>
  </div>
</template>



