<template>
    <div class="mb-20">
        <main>
            <EmptyData v-if="myCourses.length === 0" />
            <div v-else class="flex justify-end my-5">
                <form action="" class="border-2 border-gray-900 rounded-full items-center flex gap-2 py-1 px-2">
                    <button type="submit" class="flex items-center justify-center">
                        <MagnifyingGlassIcon class="h-6 w-6 text-gray-900" />
                    </button>
                    <input class="focus-visible:outline-none" type="text" name="" id="" placeholder="Tìm kiếm khóa học">
                </form>
            </div>
            <div class="grid sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <CardMyCourse v-for="course in myCourses || []" :key="course.id" :id="course.id"
                    :thumbnail="course.thumbnail" :title="course.title" :creator="course.creator"
                    :total_lectures="course.total_lectures" :completed_lectures="course.completed_lectures"
                    :progress_percent="course.progress_percent" />
            </div>
        </main>
    </div>
</template>

<script setup lang="ts">
import { MagnifyingGlassIcon } from "@heroicons/vue/20/solid";

import CardMyCourse from '@/components/ui/card/CardMyCourse.vue';
import EmptyData from '@/components/ui/empty/EmptyData.vue';
import { useCourseStore } from "@/store/course";
import { onMounted } from "vue";
import { storeToRefs } from "pinia";

const courseStore = useCourseStore()
const { myCourses } = storeToRefs(courseStore);
const { fetchMyCourse } = courseStore;
onMounted(async () => {
    await fetchMyCourse()
})
</script>
