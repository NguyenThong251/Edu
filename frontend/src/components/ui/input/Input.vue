<template>
    <div class="flex flex-col gap-1">
        <label class="text-lg font-medium" for="">{{ label }}</label>
        <input :v-model="modelValue" class="w-full border-2 rounded-md p-2 " :type="type" :placeholder="placeholder">
        <p v-if="error" class="input-error-message text-red-500">{{ error }}</p>
    </div>
</template>

<script setup lang="ts">

import type { Input } from '@/interfaces/ui.interface';
import { defineProps } from 'vue';
defineProps<Input>()
</script>
