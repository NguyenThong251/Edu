
<template>
 <router-view /> 
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useAuthStore } from '@/store/auth';

const authStore = useAuthStore();

const initData = async () => {
  await authStore.fetchCurrentUser();
};

// Khởi tạo dữ liệu khi ứng dụng được mounted
onMounted(() => {
  if (authStore.state.token) {
    initData();
  } else {
    // Nếu không có token, có thể redirect đến trang login nếu cần
    // window.location.href = '/login';
  }
});
</script>

<style>
/* Các style của bạn */
</style>