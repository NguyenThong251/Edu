<template>
    <div class="flex  items-center justify-between">
        <div class="flex gap-5">

            <img class="w-28 h-20 rounded-lg" :src="thumbnail" alt="Course Thumbnail">
            <div class="">
                <h3 class="text-lg  font-semibold">{{ title }}</h3>
                <span class="text-gray-600">{{ category_id }}</span>
                <div class="flex gap-3 items-end">
                    <span class="text-md font-bold">{{ price }} vnđ</span>
                </div>
            </div>
        </div>
        <button @click="removeCourseFromCart">
            <XMarkIcon class="h-6 w-6 text-red-500" />
        </button>
    </div>
</template>

<script setup lang="ts">
import { useCartStore } from '@/store/cart';
import { XMarkIcon } from '@heroicons/vue/20/solid';
const cartStore = useCartStore();
interface TCartCourseViewItem {
    id: number;
    thumbnail: string;
    title: string;
    price: number;
    category_id: number;
}
const props = defineProps<TCartCourseViewItem>();
const removeCourseFromCart = () => {
    if (props.id) {
        cartStore.removeCourseFromCart(props.id);
    }
};
</script>
