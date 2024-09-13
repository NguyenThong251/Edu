<template>
    <!-- HEADER MAIN -->
    <div class="z-10 bg-white drop-shadow-md shadow-sm sticky top-0">
        <div class="container-user py-2 ">
            <div class="flex items-center justify-between">
                <!-- LEFT -->
                <div class="flex items-center gap-5">
                    <!-- LOGO -->
                    <img class="w-32" :src="logo" alt="">
                    <!-- NAVBAR -->
                    <nav class="">
                        <!-- <el-text text @click="drawer = true" class="cursor-pointer animation hover:text-indigo-600">Thể
                            loại</el-text> -->
                        <!-- <div class="cursor-pointer animation hover:text-indigo-600">Thể
                            loại</div> -->
                        <el-menu class="text" mode="horizontal" :ellipsis="false">
                            <!-- Workspace Menu -->
                            <el-sub-menu class="" index="2">
                                <!-- Just display the text 'Workspace' -->
                                <template #title>
                                    <p class="">Thể loại</p>
                                    <!-- <span
                                        class="hover:text-indigo-600 text-gray-700">Workspace</span> -->
                                </template>

                                <el-menu-item index="2-1">item
                                    one</el-menu-item>
                                <el-menu-item index="2-2">item two</el-menu-item>
                                <el-menu-item index="2-3">item three</el-menu-item>
                                <el-sub-menu index="2-4">
                                    <template #title>item four</template>
                                    <el-menu-item index="2-4-1">item one</el-menu-item>
                                    <el-menu-item index="2-4-2">item two</el-menu-item>
                                    <el-sub-menu index="2-4-4">
                                        <template #title>item four</template>
                                        <el-menu-item index="2-4-4-1">item one</el-menu-item>
                                        <el-menu-item index="2-4-4-2">item two</el-menu-item>
                                        <el-menu-item index="2-4-4-3">item three</el-menu-item>
                                    </el-sub-menu>
                                </el-sub-menu>
                            </el-sub-menu>
                        </el-menu>

                    </nav>
                    <!-- SEARCH -->
                    <form class="flex gap-5 py-2 px-4 border-[1px] rounded-3xl border-gray-900">

                        <MagnifyingGlassIcon class=" h-6 w-6 text-gray-900" />
                        <input class=" pe-36 focus-visible:outline-none border-none" type="text"
                            placeholder="Tìm  kiếm nội dung bất kì">
                    </form>
                </div>
                <!-- RIGHT -->
                <div class="flex items-center gap-5">
                    <ul class="flex gap-5">
                        <router-link class="animation hover:text-indigo-600" to="/">
                            Giảng viên trên Edunity
                        </router-link>
                        <!-- <router-link class="animation hover:text-indigo-600" to="/">Khóa học của tôi</router-link> -->


                    </ul>
                    <el-badge :value="3" type="primary" badge-style="text-lg">
                        <ShoppingCartIcon class="h-6 w-6 text-gray-900" />
                    </el-badge>
                    <!-- CART -->

                    <!-- NO USER -->
                    <div class="flex gap-5">
                        <Button variant="default">Đăng ký</Button>
                        <Button variant="primary">Đăng nhập</Button>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- HEADER CATEGORY  -->
    <!-- <div class="z-20 bg-green-600 drop-shadow-md shadow-sm sticky top-[76px]">
        <div class="container-user py-2 ">
            <div class="px-20">
                <ul>
                    <li>
                        Phát triển
                    </li>
                </ul>
            </div>
        </div>
    </div> -->

    <!-- <el-drawer direction="ltr" v-model="drawer" title="I am the title" :with-header="false" size="15%">
        <span>Thể loại</span>
    </el-drawer> -->
</template>

<script setup lang="ts">
import logo from '../../assets/images/logo1.svg'
import { MagnifyingGlassIcon, ShoppingCartIcon } from "@heroicons/vue/24/outline";
import Button from '@/components/ui/button/Button.vue';
// import { ref } from 'vue'
// const drawer = ref(false)
import { ref } from 'vue'

const activeIndex = ref('2')
const handleSelect = (key: string, keyPath: string[]) => {
    console.log(key, keyPath)
}
</script>
<style scoped>
.el-menu--horizontal.el-menu {
    border: none;
}
</style>