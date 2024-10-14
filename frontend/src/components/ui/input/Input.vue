<template>
    <div class="input-group">
        <label :for="label">{{ label }}</label>
        <input :type="type" class="input" :placeholder="placeholder" v-model="modelValue"
            @input="$emit('update:modelValue', modelValue)" />
    </div>
</template>

<script setup lang="ts">
import type { Input } from '@/interfaces/ui.interface';
import { ref } from 'vue';

const props = defineProps<Input>();

const modelValue = ref(props.modelValue);
</script>