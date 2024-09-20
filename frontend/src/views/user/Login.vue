<template>
    <main class="container-user my-16">
        <div class="flex items-center lg:flex-row flex-col justify-between gap-5">
            <div class="lg:w-1/2 ">
                <div class="md:p-10">
                    <img class="w-full h-[420px] lg:h-[580px]" :src="Banner" alt="">
                </div>
            </div>
            <div class="lg:w-1/2 ">
                <div class="py-5 lg:px-24  flex flex-col gap-5">
                    <h3 class="text-3xl font-bold">Đăng nhập</h3>
                    <span class="text-lg text-gray-600">Chúng tôi rất vui mừng khi thấy bạn quay lại với chúng
                        tôi.</span>
                    <LoginForm />
                    <div class="flex gap-3 items-center justify-center">
                        <div class="h-0.5 w-28 bg-slate-300"></div>
                        <span class="text-center">Các tùy chọn đăng nhập khác</span>
                        <div class="h-0.5 w-28 bg-slate-300"></div>
                    </div>
                    <div class="flex justify-center items-center gap-5">
                        <div class="border rounded-md border-gray-900">
                            <button class="p-2">
                                <img class="w-10 h-1w-10" :src="Google" alt="">
                            </button>
                        </div>
                        <div class="border rounded-md border-gray-900">
                            <button class="p-2">
                                <img class="w-10 h-1w-10" :src="Facebook" alt="">
                            </button>
                        </div>
                    </div>
                    <div class="text-lg text-center">
                        Bạn chưa có tài khoản ? <RouterLink class="text-indigo-600 font-medium" to="/register"> Đăng ký
                        </RouterLink>
                    </div>
                </div>
            </div>
        </div>

    </main>
</template>

<script lang="ts" setup>
import LoginForm from '@/components/auth/LoginForm.vue';
const Banner = 'https://demo.creativeitem.com/academy-laravel/public/assets/frontend/default/image/login.gif'
const Google = 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Google_%22G%22_logo.svg/768px-Google_%22G%22_logo.svg.png'
const Facebook = 'https://upload.wikimedia.org/wikipedia/commons/b/b9/2023_Facebook_icon.svg'
</script>
