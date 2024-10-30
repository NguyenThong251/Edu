<script setup lang="ts">
import type { InputGroupProps } from '@/interfaces';
import {ref, defineProps } from 'vue';

const props = defineProps<InputGroupProps>();



</script>

<template>
  <div class="mt-3"
  :class="customsClass"
  >
  <label :for="inputId"
    :class="customsClassChild" class="label-input flex gap-2">{{ label }} 
    <p class="text-red-600 dark:text-red-500">{{ required }}</p>
  </label>
    <div class="relative mt-1 rounded-md shadow-sm"
    :class="customsClassChild2"
    >
      <input 
        type="file" 
        :name="inputId" 
        :id="inputId"
        class="input-style dark:bg-white "
      />
    </div>
  </div>
</template>

