<template>
    <button :class="buttonClasses" :type="type" :disabled="disabled">
        <slot></slot>
    </button>
</template>

<script setup lang="ts">
import { computed, defineProps } from 'vue';

const props = defineProps<{
    variant?: 'primary' | 'default';
    type?: 'button' | 'submit' | 'reset';
    disabled?: boolean;
}>()
const buttonClasses = computed(() => {
    switch (props.variant) {
        case 'primary':
            return 'border rounded-lg font-medium py-2 px-8 text-white transition ease-in-out bg-blue-500   hover:bg-blue-600 duration-300';
        case 'default':
        default:
            return 'border border-blue-600 rounded-lg font-medium py-2 px-8 text-blue-600 transition ease-in-out bg-white   hover:bg-blue-100 duration-300';
    }
});
</script>
