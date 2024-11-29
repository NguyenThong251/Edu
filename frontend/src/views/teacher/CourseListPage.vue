<template>
    <div class="mt-5 gap-5 grid xl:grid-cols-4 lg:grid-cols-3 md:grid-cols-2">
        <CardCourseAdmin v-for="course in listCourseTeacher" :key="course.id" :id="course.id" :title="course.title"
            :thumbnail="course.thumbnail || 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'"
            :creator="course.creator" :tag="course.tag" :lectures_count="course.lectures_count" :level="course.level"
            :current_price="course.current_price" :old_price="course.old_price" />
    </div>
</template>

<script setup lang="ts">
import CardCourseAdmin from '@/components/ui/card/CardCourseAdmin.vue';
import { useCourseStore } from '@/store/course';
import { storeToRefs } from 'pinia';
import { onMounted } from 'vue';



const courseStore = useCourseStore()
const { listCourseTeacher } = storeToRefs(courseStore)
const { fetchTeacherCourse } = courseStore

onMounted(async () => {
    await fetchTeacherCourse()
})
</script>

<style scoped></style>