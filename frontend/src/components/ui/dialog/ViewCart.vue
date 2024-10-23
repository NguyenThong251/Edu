<template>
    <div class="p-3 flex flex-col justify-between h-full">
        <div class="">
            <div class="flex justify-end ">
                <button @click="clearCart" class="text-indigo-600 pb-3 ">Xóa tất cả </button>
            </div>
            <div v-if="loading" class="flex justify-center items-center">
                <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>
            <div class="flex flex-col gap-3">
                <CardCourseViewCart v-for="course in cart" :key="course.id" :id="course.id" :title="course.title"
                    :thumbnail="course.thumbnail || 'default_thumbnail.jpg'" :price="course.price"
                    :category_id="course.category_id" />

            </div>
        </div>
        <div class="">
            <div class="py-3 font-bold text-xl">
                <!-- Tổng cộng : {{ formattedTotalPrice }} VNĐ -->
            </div>
            <RouterLink to="/cart"> <Button variant="primary" class="w-full">Xem giỏ hàng</Button>
            </RouterLink>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, watch } from 'vue';
import CardCourseViewCart from '../card/CardCourseViewCart.vue';
import Button from '../button/Button.vue';
import { useCart } from '@/composables/user/useCart';

const { cart, loading, fetchCartCourses, clearCart } = useCart();

onMounted(async () => {
    await fetchCartCourses();
});

</script>
