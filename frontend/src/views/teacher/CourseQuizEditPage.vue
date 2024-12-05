<template>
    <div>
        <h2>{{ listQuestion }}</h2>
    </div>
</template>

<script setup lang="ts">
import { useCourseStore } from '@/store/course';
import { storeToRefs } from 'pinia';
import { onMounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const id_quiz = route.params.id ? Number(route.params.id) : null;

const useCourse = useCourseStore()
const { showQuestionsOfQuiz } = useCourse
const { listQuestion } = storeToRefs(useCourse)


onMounted(() => {
    showQuestionsOfQuiz(id_quiz || 0)
})
</script>
