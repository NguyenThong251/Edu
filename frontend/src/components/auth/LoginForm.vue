<template>
    <form class="flex flex-col gap-5" @submit.prevent="handleSubmit">
        <Input v-model="email" label="Email" type="email" placeholder="Nhập email" />
        <Input v-model="password" label="Mật khẩu" type="password" placeholder="Nhập mật khẩu" />
        <RouterLink class="text-indigo-600 text-end" to="/">Quên mật khẩu</RouterLink>
        <Button class="w-full" variant="primary" :disabled="authStore.loading">Đăng nhập</Button>
    </form>
</template>

<script setup lang="ts">
import { useLogin } from '@/composables/user/useLogin';
import Button from '../ui/button/Button.vue';
import Input from '../ui/input/Input.vue';

const { email, password, handleSubmit, authStore } = useLogin();
</script>