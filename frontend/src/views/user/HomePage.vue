<template>

    <main>
        <UserHero />
        <UserCompany />
        <!-- CATEGORY -->
        <section class="container-user mt-16">
            <div class="text-center">
                <span class="text-lg font-medium text-indigo-600">Thể loại</span>
                <h3 class="text-3xl font-medium">Khám phá các khóa học hàng đầu</h3>
            </div>
            <div class="grid grid-cols-3 gap-5 mt-10">
                <CardCategory
                    image="https://demo.creativeitem.com/academy-laravel/public/uploads/category-logo/web-development-logo-1718273508.png"
                    name="Phát triển Web" :countCourse="10" />
                <CardCategory
                    image="https://demo.creativeitem.com/academy-laravel/public/uploads/category-logo/graphic-design-logo-1718524053.png"
                    name="Thiết kế đồ họa" :countCourse="10" />
                <CardCategory
                    image="https://demo.creativeitem.com/academy-laravel/public/uploads/category-logo/user-experience-logo-1718524065.png"
                    name="UI/UX" :countCourse="10" />
                <CardCategory
                    image="https://demo.creativeitem.com/academy-laravel/public/uploads/category-logo/3d-and-animation-logo-1718524102.png"
                    name="3D và hoạt hình" :countCourse="10" />
                <CardCategory
                    image="https://demo.creativeitem.com/academy-laravel/public/uploads/category-logo/music-logo-1718524117.png"
                    name="Âm nhạc" :countCourse="10" />
                <CardCategory
                    image="https://demo.creativeitem.com/academy-laravel/public/uploads/category-logo/web-development-logo-1718273508.png"
                    name="Phát triển Web" :countCourse="10" />

            </div>
        </section>

        <!-- PRODUCT -->
        <section class="container-user mt-16">
            <div class="flex flex-col gap-3">
                <h1 class="text-4xl font-semibold">Tuyển tập khóa học rộng lớn</h1>
                <span class="text-lg w-1/2 text-gray-500">Lựa chọn trong số hơn 250000 khóa học video online với nhiều
                    nội dung bổ
                    sung mới
                    được
                    xuất bản
                    hàng tháng</span>
            </div>


            <div class="mt-5 flex ">
                <CardProduct />
            </div>
        </section>

    </main>
    <div class=" mt-16">

    </div>

</template>

<script lang="ts" setup>
import { computed } from 'vue';
import UserHero from '@/components/user/UserHero.vue';
import UserCompany from '@/components/user/UserCompany.vue';
import CardCategory from '@/components/ui/card/CardCategory.vue';
import CardProduct from '@/components/ui/card/CardProduct.vue';


</script>