<template>
    <div
        class="w-1/5 rounded-lg p-[8px] bg-white shadow-md hover:shadow-lg hover:transition-all transition-all hover:duration-300 duration-300 group ">
        <div class="relative overflow-hidden">
            <!-- IMAGE  -->
            <div class="rounded-lg overflow-hidden">
                <img class="group-hover:scale-105  scale-100 hover:transition-all transition-all hover:duration-300 duration-300 cursor-pointer object-cover w-full h-36"
                    src="https://img-c.udemycdn.com/course/240x135/4993276_3452.jpg" alt="Product Image" />
            </div>
            <!-- BODY  -->
            <div class="mt-2 flex flex-col gap-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm">Nguyễn Hoàng Thông</span>
                    <button class="text-sm bg-pink-400 border rounded-md px-2 py-0.5 text-white">Bán chạy nhất</button>
                </div>
                <h3 class="text-[16px] font-medium leading-6">Learn Figma - UI/UX Design Essential Training</h3>
                <ul class="flex justify-between gap-1">
                    <li class="flex items-center gap-1">
                        <ClockIcon class="h-4 w-4 text-gray-500" />
                        <span class="text-[12px]">120 Giờ</span>
                    </li>
                    <li class="flex items-center gap-1">
                        <BookOpenIcon class="h-4 w-4 text-gray-500" />
                        <span class="text-[12px]">12 Chương học</span>
                    </li>
                    <li class="flex items-center gap-1">
                        <RocketLaunchIcon class="h-4 w-4 text-gray-500" />
                        <span class="text-[12px]">Mới bắt đầu</span>
                    </li>
                </ul>

                <div class="flex items-end gap-3">
                    <span class="text-lg text-gray-800 font-bold">299.000đ</span>
                    <del class="text-gray-500">299.000đ</del>
                </div>
            </div>
            <div
                class="absolute top-2 right-2 opacity-0 translate-x-11 group-hover:translate-x-0  hover:transition-all duration-300 group-hover:opacity-100  ">
                <div class="flex flex-col gap-2">
                    <button
                        class="bg-indigo-500 hover:bg-indigo-600 hover:transition-all transition-all hover:duration-300 duration-300 p-2 rounded-full">
                        <ShoppingCartIcon class="h-5 w-5  text-white" />
                    </button>
                    <button
                        class="bg-indigo-500 hover:bg-indigo-600 hover:transition-all transition-all hover:duration-300 duration-300 p-2 rounded-full">
                        <HeartIcon class="h-5 w-5  text-white" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';
import { ClockIcon, BookOpenIcon, RocketLaunchIcon, HeartIcon, ShoppingCartIcon } from "@heroicons/vue/24/outline";
import { Button } from '@/components/ui';

// const props = defineProps<{
//     imageSrc: string;
//     title: string;
//     description: string;
// }>();
</script>