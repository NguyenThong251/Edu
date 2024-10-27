<template>
    <main class="pb-16">
        <div class="container-user mt-16 flex flex-col gap-5">
            <h3 class="text-4xl font-bold">Giỏ hàng</h3>
            <span class="text-lg"> <span class="font-semibold"> 2 </span>khóa học trong giỏ hàng</span>
            <div class="flex md:flex-row flex-col gap-5">
                <!-- COURSE ITEM -->
                <div class="flex flex-col md:w-4/6 w-full gap-5">
                    <CardCourseCart v-for="course in cart" :key="course.id" :id="course.id" :title="course.title"
                        :thumbnail="course.thumbnail || 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'"
                        :old_price="course.old_price" :current_price="course.current_price"
                        :category="course.category_name || course.category.name" :level="course.level"
                        :average_rating="course.average_rating" :reviews_count="course.reviews_count"
                        :lectures_count="course.lectures_count" :creator="course?.creator" />
                    <!-- <div v-for="course in dataCart" :key="course.id">
                        <div>{{ course.title }} - {{ course.price }} VNĐ</div>
                        <button @click="removeCourseFromCart(course.id)">Xóa khóa học</button>
                    </div> -->

                    <!-- <button @click="clearCart">xoóa item</button> -->
                </div>
                <!-- TOTAL -->
                <div class="md:w-2/6 w-full">
                    <div class="p-5 sticky top-0 shadow-lg rounded-lg">
                        <div class="flex flex-col gap-5">
                            <div class="flex flex-col">
                                <span class="text-2xl font-medium">Tổng</span>
                                <div class="text-4xl font-bold">788.000 vnđ</div>
                            </div>
                            <Button class="w-full" variant="primary">Thanh toán</Button>
                            <div class="flex flex-col gap-3">
                                <h3 class="text-lg">Áp dụng mã giảm giá</h3>
                                <div class="border-2  border-gray-900 flex justify-between">
                                    <input type="text" class="px-5 w-[12rem] py-2 focus-visible:outline-none"
                                        placeholder="Nhập phiếu giảm giá">
                                    <button class="h-full bg-gray-900 px-5 py-2 text-white">Áp dụng</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import CardCourseCart from '@/components/ui/card/CardCourseCart.vue';
import { useCart } from '@/composables/user/useCart';
import { useCartStore } from '@/store/cart';
import { computed, onMounted, ref } from 'vue';
const cartStore = useCartStore()
const { cart, loading, fetchCartCourses, clearCart, formattedTotalPrice } = useCart();
onMounted(async () => {
    await fetchCartCourses();
});

</script>
