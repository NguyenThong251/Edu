<template>
    <div>
        <!-- <Loading :active="loading" :is-full-page="true" /> -->
        <form class="flex flex-col gap-5" @submit.prevent="handleSubmit">
            <Input v-model="email" label="Email" type="email" placeholder="Nhập email" :errorMessages="emailError" />
            <Input v-model="password" label="Mật khẩu" type="password" placeholder="Nhập mật khẩu"
                :errorMessages="passwordError" />
            <RouterLink class="text-indigo-600 text-end" to="/">Quên mật khẩu</RouterLink>
            <Button class="w-full" variant="primary">Đăng nhập</Button>
        </form>
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { ElNotification } from 'element-plus';
import { useRouter } from 'vue-router';
import Button from '../ui/button/Button.vue';
import Input from '../ui/input/Input.vue';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';
import { useAuthStore } from '@/store/auth';

// State management and logic
const authStore = useAuthStore();
const email = ref<string>('');
const password = ref<string>('');
const emailError = ref<string | null>(null);
const passwordError = ref<string | null>(null);
const loading = ref(false);
const router = useRouter();

const validateForm = (): boolean => {
    let isValid = true;

    if (!email.value) {
        emailError.value = 'Email không được để trống';
        isValid = false;
    } else {
        emailError.value = null;
    }

    if (!password.value) {
        passwordError.value = 'Mật khẩu không được để trống';
        isValid = false;
    } else if (password.value.length < 8) {
        passwordError.value = 'Mật khẩu phải có ít nhất 8 ký tự';
        isValid = false;
    } else {
        passwordError.value = null;
    }

    return isValid;
};

// watch(email, (newVal) => {
//     if (newVal) emailError.value = '';
// });

// watch(password, (newVal) => {
//     if (newVal && newVal.length >= 8) passwordError.value = '';
// });

const handleSubmit = async () => {
    // if (!validateForm()) return;
    // loading.value = true;
    try {
        const res = await authStore.login(email.value, password.value);
        if (res.status === 'FAIL') {
            ElNotification({
                title: 'Thất bại',
                message: res.message || 'Đăng nhập không thành công',
                type: 'error',
            });
        } else if (res.status === 'OK') {
            ElNotification({
                title: 'Thành công',
                message: res.message || 'Đăng nhập thành công',
                type: 'success',
            });
            router.push('/');
        }
    } catch (error) {
        console.error(error);
        ElNotification({
            title: 'Lỗi',
            message: 'Có lỗi xảy ra trong quá trình đăng nhập',
            type: 'error',
        });
    } finally {
        // loading.value = false;
    }
};
</script>