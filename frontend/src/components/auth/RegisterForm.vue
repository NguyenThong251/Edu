<template>
    <form class="flex flex-col gap-5 " @submit.prevent="handleSubmit">
        <Input v-model="name" label="Email" type="text" :error="nameError ?? ''" placeholder="Nhập họ và tên" />
        <Input v-model="email" label="Email" type="email" :error="emailError ?? ''" placeholder="Nhập email " />
        <Input v-model="password" label="Mật khẩu" type="password" :error="passwordError ?? ''"
            placeholder="Nhập password" />
        <Button class="w-full" variant="primary">Đăng ký</Button>
    </form>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import Button from '../ui/button/Button.vue';
import Input from '../ui/input/Input.vue';
const name = ref('')
const nameError = ref<string | null>(null);
const email = ref('')
const emailError = ref<string | null>(null);
const password = ref('')
const passwordError = ref<string | null>(null);

const handleSubmit = () => {
    // Kiểm tra validation
    if (!name.value) {
        nameError.value = 'Bạn cần nhập tên';
    }
    if (!email.value) {
        emailError.value = 'Bạn cần nhập email';
    }
    if (!password.value) {
        passwordError.value = 'Bạn cần nhập password';
    }

    // Thực hiện logic  đăng ký ở đây
};

</script>

<style scoped></style>