<template>
    <form class="flex flex-col gap-5 " @submit.prevent="handleSubmit">
        <Input v-model="name" label="Họ và tên" type="text" placeholder="Nhập họ và tên" />
        <Input v-model="email" label="Email" type="email" placeholder="Nhập email" />
        <Input v-model="password" label="Mật khẩu" type="password" placeholder="Nhập mật khẩu" />
        <Button class="w-full" variant="primary" :disabled="authStore.loading">Đăng ký</Button>
    </form>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import Button from '../ui/button/Button.vue';
import Input from '../ui/input/Input.vue';
import { useRegister } from '@/composables/user/useRegister';
const { name, email, password, handleSubmit, authStore } = useRegister();


</script>

<style scoped></style>