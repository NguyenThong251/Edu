<script setup lang="ts">
import type { InputGroupProps } from '@/interfaces';
import { defineProps } from 'vue';
const props = defineProps<InputGroupProps>();
</script>
<template>
    <div class="mt-3"
      :class="customsClass"
    >
        <label 
        :for="inputId" 
        class="label-input flex gap-2"
        :class="customsClassChild"
          >{{ label }} 
            <p class="text-red-600 dark:text-red-500">{{ required }}</p></label>
        <div 
        class="relative mt-1 rounded-md shadow-sm"
        :class="customsClassChild2"
        >
          <input 
          type="text" 
          :name="inputId" 
          :id="inputId"
          :placeholder="inputPlaceHoder"
          :v-model="modelValue"
          class="input-style" 
        
          />
        </div>
      </div>
</template>

