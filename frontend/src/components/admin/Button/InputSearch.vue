
<script setup lang="ts">
import { defineProps } from 'vue';
import ButtonPrimary from './ButtonPrimary.vue';
import InputGroup from '../Dialog/InputGroup.vue';
const props = defineProps<{
  label: string;
  inputId: string;
  inputPlaceHoder?: string;
  modelValue: string; 
  title: string
}>();
</script>
<template>
    <input 
    type="text" 
    :name="inputId" 
    :id="inputId"
    :placeholder="inputPlaceHoder"
    :v-model="modelValue"
    class="input-style w-auto md:w-full" 
    />
    <ButtonPrimary
    :title="title"
    class="md:w-[170px] sm:w-full justify-center"
    />
</template>


