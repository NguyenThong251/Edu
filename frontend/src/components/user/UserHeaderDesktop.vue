<template>
    <div class="z-10 bg-white drop-shadow-md shadow-sm sticky top-0">
        <div class="container-user py-2 ">
            <div class="flex items-center justify-between">
                <div class="lg:hidden" @click="toggleMenu">
                    <Bars3Icon class="h-8 w-8 text-gray-900" />
                </div>
                <div class="flex items-center gap-5">
                    <RouterLink to="/">
                        <img class="w-32" :src="logo" alt="">
                    </RouterLink>
                    <nav class="hidden lg:block">
                        <MenuDesktop />
                    </nav>
                    <div class="hidden lg:block">
                        <form class="flex gap-5 py-2 px-4 border-[1px] rounded-3xl border-gray-900">
                            <MagnifyingGlassIcon class=" h-6 w-6 text-gray-900" />
                            <input class=" pe-24 focus-visible:outline-none border-none" type="text"
                                placeholder="Tìm  kiếm nội dung bất kì">
                        </form>
                    </div>
                </div>
                <!-- RIGHT -->
                <div class="hidden xl:block">
                    <div class=" flex items-center gap-5">
                        <ul class="flex gap-5">
                            <router-link class="animation hover:text-indigo-600" to="/">
                                Giảng viên trên Edunity
                            </router-link>
                        </ul>

                        <el-badge @click="toggleCart" :value="3" type="primary" badge-style="text-lg">
                            <ShoppingCartIcon class="h-6 w-6 text-gray-900" />
                        </el-badge>
                        <!-- CART -->

                        <!-- NO USER -->
                        <div class="">
                            <div class="" v-if="state.token">
                                <UserProfile :dataUser="state.user" />
                            </div>
                            <div v-else class="flex gap-5">
                                <Button variant="default">
                                    <RouterLink to="/register">Đăng ký</RouterLink>
                                </Button>
                                <Button variant="primary">

                                    <RouterLink to="/login">Đăng nhập</RouterLink>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="xl:hidden block">
                    <div class="" v-if="state.token">
                        <UserProfile :dataUser="state.user" />
                    </div>
                    <div v-else class="flex items-center gap-3">

                        <RouterLink class="text-indigo-600" to="/register">Đăng ký</RouterLink>
                        <RouterLink class="text-indigo-600" to="/login">Đăng Nhập</RouterLink>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <el-drawer size="50%" v-model="isOpenNav" title="I am the title" :direction="direction">
        <span>Hi, there!</span>
    </el-drawer>

    <!-- Cart view -->
    <el-drawer v-model="isOpenCart" @update:modelValue="isOpenCart = false" title="Giỏ hàng">
        <ViewCart />
    </el-drawer>
</template>

<script setup lang="ts">
import logo from '../../assets/images/logo1.svg'
import { XMarkIcon, MagnifyingGlassIcon, ShoppingCartIcon } from "@heroicons/vue/24/outline";
import Button from '@/components/ui/button/Button.vue';
import { onMounted, ref } from 'vue'
import { ElNotification, type DrawerProps } from 'element-plus';
import { Bars3Icon } from "@heroicons/vue/24/outline";
import UserProfile from './UserProfile.vue';

import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '@/store/auth';
import MenuDesktop from '../ui/menu/MenuDesktop.vue';
import ViewCart from '../ui/dialog/ViewCart.vue';
const direction = ref<DrawerProps['direction']>('ltr')
const isOpenNav = ref(false)
const isOpenCart = ref(false)
const toggleMenu = () => {
    isOpenNav.value = !isOpenNav.value
}
// const toggleCart = ref(false)
const toggleCart = () => {
    isOpenCart.value = !isOpenCart.value
}
const router = useRouter()
const authStore = useAuthStore()
const { state, userData } = authStore
const profileData = ref(null)
const currentPath = router.currentRoute.value.fullPath
localStorage.setItem('redirectAfterLogin', currentPath);
onMounted(async () => {
    if (!state.user && state.token) {
        const res = await userData()
        if (res?.status === 'FAIL') {
            authStore.logout()
            router.push('/')
            ElNotification({
                title: 'Thất bại',
                message: res.message || 'Bạn không có quyền truy cập',
                type: 'error'
            })
        } else {
            profileData.value = res
        }
    }
})


</script>
