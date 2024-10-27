<script setup lang="ts">
import type { DescriptionGroupProps } from '@/interfaces';
import { defineProps } from 'vue';

const props = defineProps<DescriptionGroupProps>();

</script>

<template>
  <div class="mt-3"
  :class="customsClass"
  >
    <label :for="inputId" class="label-input" :class="customsClassChild">
      {{ label }}
    </label>
    <textarea 
    :id="inputId" 
    :name="inputId"
    :placeholder="inputPlaceHoder"
    :value="value"
    :v-model="modelValue"
    rows="4" 
    class="block p-2.5 input-style" 
    :class="customsClassChild2"
    >
    </textarea>
  </div>
</template>

