<template>
    <div class="p-3 flex flex-col justify-between h-full">
        <Loading :active="loading" :is-full-page="true" />
        <div class="">
            <div class="flex justify-end ">
                <button @click="clearCart" class="text-indigo-600 pb-3 ">Xóa tất cả </button>
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
import { computed, ref, onMounted, watch } from 'vue';
import CardCourseViewCart from '../card/CardCourseViewCart.vue';
import { useCartStore } from '@/store/cart';
import Button from '../button/Button.vue';

const cartStore = useCartStore();
const { cart, loading, fetchCartCourses, clearCart } = cartStore;

interface TCartCourseViewItem {
    id: number;
    thumbnail: string;
    title: string;
    price: number;
    category_id: number;
}

// const dataCart = ref<TCartCourseViewItem[]>([]);


// const cartData = computed(() => cartStore.cart


// );



// onMounted(async () => {
//     const courses = await fetchCartCourses();
//     if (courses) {
//         dataCart.value = courses;
//         console.log('DataCart:', dataCart.value);
//     }
// });

// const formattedTotalPrice = computed(() => {
//     return dataCart.value.reduce((total, course) => total + course.price, 0).toLocaleString();
// });
onMounted(async () => {
    await fetchCartCourses(); // Ensure that fetchCartCourses is called once the component mounts
    // console.log('Current cart data:', cart); // This will now show updated data in cart after fetching
});

</script>
