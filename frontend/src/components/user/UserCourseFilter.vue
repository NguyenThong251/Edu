<template>
    <div>
        <div class="flex flex-col gap-5">
            <!-- RATE -->
            <div>
                <h3 class="font-medium text-lg">Đánh giá</h3>
                <el-radio-group class="flex flex-col items-start" v-model="selectedRate" @change="applyFilters">
                    <el-radio class="" :label="3">
                        <div class="flex gap-1">
                            <StarIcon class="h-4 w-4 text-yellow-300" />
                            <StarIcon class="h-4 w-4 text-yellow-300" />
                            <StarIcon class="h-4 w-4 text-yellow-300" />
                        </div>
                    </el-radio>
                    <el-radio :label="4">
                        <div class="flex gap-1">
                            <StarIcon class="h-4 w-4 text-yellow-300" />
                            <StarIcon class="h-4 w-4 text-yellow-300" />
                            <StarIcon class="h-4 w-4 text-yellow-300" />
                            <StarIcon class="h-4 w-4 text-yellow-300" />
                        </div>
                    </el-radio>
                    <el-radio :label="5">
                        <div class="flex gap-1">
                            <StarIcon class="h-4 w-4 text-yellow-300" />
                            <StarIcon class="h-4 w-4 text-yellow-300" />
                            <StarIcon class="h-4 w-4 text-yellow-300" />
                            <StarIcon class="h-4 w-4 text-yellow-300" />
                            <StarIcon class="h-4 w-4 text-yellow-300" />
                        </div>
                    </el-radio>
                </el-radio-group>
            </div>

            <!-- Category Filter (Checkboxes) -->
            <div class="font-medium text-lg">
                <h3 class="font-semibold text-lg mb-2">Danh mục</h3>
                <el-checkbox-group v-model="selectedCategories" class="flex flex-col items-start"
                    @change="applyFilters">
                    <el-checkbox v-for="category in categories" v-bind:key="category.id" :label="category.id">{{
                        category.name }}</el-checkbox>

                    <!-- <el-checkbox :label="'design'">Thiết kế đồ họa</el-checkbox>
                    <el-checkbox :label="'web-development'">Phát triển web</el-checkbox>
                    <el-checkbox :label="'javascript'">Javascript</el-checkbox>
                    <el-checkbox :label="'computer-science'">Khoa học máy tính</el-checkbox> -->
                </el-checkbox-group>
            </div>
            <!-- Video Duration (Checkboxes) -->
            <div class="font-medium text-lg">
                <h3 class="font-semibold text-lg mb-2">Thời gian khóa học</h3>
                <el-checkbox-group v-model="selectedDurations" class="flex flex-col items-start" @change="applyFilters">
                    <el-checkbox :label="'0-4'">0-48 giờ</el-checkbox>
                    <el-checkbox :label="'48-128'">48-128 giờ</el-checkbox>
                    <el-checkbox :label="'128+'">128+ giờ</el-checkbox>
                </el-checkbox-group>
            </div>
            <!-- Level (Checkboxes) -->
            <div class="font-medium text-lg">
                <h3 class="font-semibold text-lg mb-2">Trình độ</h3>
                <el-checkbox-group v-model="selectedLevels" class="flex flex-col items-start" @change="applyFilters">
                    <el-checkbox :label="'beginner'">Mới bắt đầu</el-checkbox>
                    <el-checkbox :label="'intermediate'">Trung cấp</el-checkbox>
                    <el-checkbox :label="'advanced'">Nâng cao</el-checkbox>
                </el-checkbox-group>
            </div>
            <!-- Language (Checkboxes) -->
            <div class="font-medium text-lg">
                <h3 class="font-semibold text-lg mb-2">Ngôn ngữ</h3>
                <el-checkbox-group class="flex flex-col items-start ">
                    <el-checkbox class="!text-gray-900" :label="'#'">Tiếng việt</el-checkbox>
                    <el-checkbox class="!text-gray-900" :label="'#'">Tiếng anh</el-checkbox>
                    <el-checkbox class="!text-gray-900" :label="'#'">Tiếng pháp</el-checkbox>
                </el-checkbox-group>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { StarIcon } from "@heroicons/vue/20/solid";
import { useHome } from '@/composables/user/useHome';

const { categories, fetchCate } = useHome()
const emit = defineEmits(['updateFilters']);
const selectedRate = ref<number | null>(null);
const selectedCategories = ref<string[]>([]);
const selectedDurations = ref<string[]>([]);
const selectedLevels = ref<string[]>([]);
const applyFilters = () => {

    emit('updateFilters', {
        max_rating: selectedRate.value,
        category_ids: selectedCategories.value,
        duration_range: selectedDurations.value,
        levels: selectedLevels.value,
    });
};
onMounted(() => {
    fetchCate()
})
</script>
