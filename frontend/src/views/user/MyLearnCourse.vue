<template>
    <main class="px-10 bg-indigo-100 py-10">
        <div class="flex lg:flex-row  flex-col  gap-5">
            <div class="lg:w-4/6 w-full">
                <div v-if="currentContent && currentContent.title">
                    <h2 class="text-xl font-bold mb-3">{{ currentContent.title }}</h2>
                </div>
                <div class=" border-2 bg-white  p-5 rounded-2xl">
                    <!-- <VideoCourse :src="videoUrl" /> -->
                    <div v-if="currentContent.type === 'video'" class="relative">
                        <!-- <VideoCourse :src="currentContent.content_link" :lesson="currentContent"
                            :onUpdateLearned="updateLearned" @timeupdate="handleTimeUpdate" @ended="handleVideoEnd" /> -->

                        <!-- <VideoCourse :src="videoUrl" /> -->
                        <div class="rounded-2xl w-full overflow-hidden">
                            <vue-plyr>
                                <video controls preload="metadata" @timeupdate="handleTimeUpdate"
                                    @ended="handleVideoEnd" ref="videoElement">
                                    <source :src="currentContent.content_link" type="video/mp4" />
                                    Trình duyệt của bạn không hỗ trợ video.
                                </video>

                            </vue-plyr>
                        </div>
                    </div>
                    <!-- file -->
                    <div v-else-if="currentContent.type === 'file'" class="p-3 border rounded-lg">
                        <a :href="currentContent.content_link" target="_blank" class="text-blue-500 underline">
                            Tải xuống tài liệu: {{ currentContent.title }}
                        </a>
                        <!-- <PDFViewer :src="currentContent.content_link" style="height: 500px; width: 100%;" /> -->

                    </div>
                    <!-- Quizz -->
                    <div v-else-if="currentContent.current_content_type === 'quiz'"
                        class="min-h-[70vh] bg-white flex items-center justify-center">
                        <div class="bg-white p-4 rounded-lg shadow-md max-w-md w-full">
                            <h2 class="text-xl font-semibold mb-4 text-center">Bài tập: Biến Python</h2>

                            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                                <div class="bg-blue-500 h-2.5 rounded-full w-2/5"></div>
                            </div>

                            <p class="text-center mb-6">Cách chính xác để khai báo biến Python là gì?</p>

                            <form class="space-y-4">
                                <label class="flex items-center bg-blue-50 p-3 rounded-lg cursor-pointer">
                                    <input type="radio" name="question" class="form-radio h-4 w-4 text-blue-600" />
                                    <span class="ml-2 text-gray-700">var x = 5</span>
                                </label>

                                <label class="flex items-center bg-blue-50 p-3 rounded-lg cursor-pointer">
                                    <input type="radio" name="question" class="form-radio h-4 w-4 text-blue-600" />
                                    <span class="ml-2 text-gray-700">#x = 5</span>
                                </label>

                                <label class="flex items-center bg-blue-50 p-3 rounded-lg cursor-pointer">
                                    <input type="radio" name="question" class="form-radio h-4 w-4 text-blue-600" />
                                    <span class="ml-2 text-gray-700">$x = 5</span>
                                </label>

                                <label class="flex items-center bg-blue-50 p-3 rounded-lg cursor-pointer">
                                    <input type="radio" name="question" class="form-radio h-4 w-4 text-blue-600" />
                                    <span class="ml-2 text-gray-700">x = 5</span>
                                </label>

                                <button type="submit"
                                    class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg text-center mt-4">
                                    Gửi câu trả lời
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- <div class="bg-white rounded-lg my-5 p-2">
                    <el-tabs v-model="activeName" class="demo-tabs" @tab-click="handleClick">
                        <el-tab-pane label="Tìm kiếm" name="first">
                            <UserSearch />
                        </el-tab-pane>
                        <el-tab-pane label="Hỏi đáp" name="second">
                            <UserQuestion />
                        </el-tab-pane>
                        <el-tab-pane label="Ghi chú" name="third">

                            <UserNote />
                        </el-tab-pane>
                        <el-tab-pane label="Đánh giá" name="fourth">
                            <UserFeedback />
                        </el-tab-pane>
                    </el-tabs>
                </div> -->
            </div>
            <div class="lg:w-2/6 w-full">
                <div class="bg-white rounded-lg shadow-lg p-5">
                    <header class="p-2 flex items-center gap-3 overflow-hidden bg-gray-800 rounded-lg">
                        <img class="-ms-6 w-20"
                            src="https://ik.imagekit.io/laracasts/series/thumbnails/svg/livewire-basics.svg?tr=w-200"
                            alt="">
                        <div class="flex flex-col gap-3">
                            <h3 class="text-xl font-medium text-white">Danh sách chương học</h3>
                            <el-progress :percentage="progress" status="success" />
                        </div>

                    </header>

                    <el-collapse class="border-0" accordion>
                        <el-collapse-item v-for="(content, index) in allContent" :key="content.id" :name="index">
                            <template #title>
                                <div class="px-4 !text-gray-900 flex gap-5 items-center justify-between leading-5">
                                    <h3 class="text-lg">{{ content.title }}</h3>
                                    <div class="flex gap-1" v-if="content.content_course_type === 'section'">
                                        <span class="text-gray-500">{{ content.content_done }}/{{ content.content_count
                                            }}
                                            Hoàn thành</span> •
                                        <span class="text-pink-500">{{ content.duration_display }}</span>
                                    </div>
                                </div>
                            </template>

                            <div v-for="lesson in content.section_content" :key="lesson.id"
                                class="cursor-pointer flex justify-between items-start bg-gray-50  py-2">
                                <div class="flex items-center gap-3 w-full px-4" @click="handleChangeContent(lesson)"
                                    :class="{ 'bg-gray-200 rounded-lg': currentContent.id === lesson.id }">

                                    <CheckOuline :class="lesson.percent >= 100 ? 'text-green-500' : 'text-gray-500'"
                                        class="h-5 w-5" />
                                    <div class=" flex flex-col">
                                        <h3>{{ lesson.title }}</h3>
                                        <div>
                                            <div class="flex items-center gap-1" v-if="lesson.type === 'video'">
                                                <PlayCircleIcon class="h-4 w-4 text-gray-600" />
                                                <span class="text-pink-500">{{ lesson.duration_display }}</span>
                                            </div>
                                            <div class="flex items-center gap-1" v-else-if="lesson.type === 'file'">
                                                <DocumentIcon class="h-4 w-4 text-gray-600" />
                                                <span class="text-pink-500">{{ lesson.duration_display }}</span>

                                            </div>
                                            <!-- Nếu không, hiển thị biểu tượng câu hỏi -->
                                            <div class="flex items-center gap-1"
                                                v-else-if="lesson.content_section_type === 'quiz'">
                                                <QuestionMarkCircleIcon class="h-4 w-4 text-gray-600" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </el-collapse-item>
                    </el-collapse>
                </div>
            </div>
        </div>

    </main>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { PlayCircleIcon, CheckCircleIcon as CheckOuline, QuestionMarkCircleIcon, DocumentIcon } from "@heroicons/vue/24/outline";
import { useRoute } from 'vue-router';
import { useCourseStore } from '@/store/course';
import { storeToRefs } from 'pinia';
import type { TLesson } from '@/interfaces';
const route = useRoute();
const idCourse = Number(route.params.id);
const courseStore = useCourseStore();
const { studyCourse, currentContent, allContent, progress } = storeToRefs(courseStore);
const { fetchStudyCourse, changeContent } = courseStore;




const videoElement = ref<HTMLVideoElement | null>(null);

onMounted(async () => {
    await fetchStudyCourse(idCourse);
});


const handleChangeContent = async (lesson: any) => {
    // if (!lesson || !lesson.id || !lesson.type) {
    //     console.error('Invalid lesson data:', lesson);
    //     return;
    // }
    const data = {
        course_id: idCourse,
        content_type: lesson.content_section_type,
        content_id: lesson.id,
        learned: lesson.learned,
        content_old_type: currentContent.value?.type || '',
        content_old_id: currentContent.value?.id || 0
    }
    console.log(lesson)
    await changeContent(data);
};
const updateLearned = async ({ id, learned }: { id: number; learned: number }) => {
    if (!currentContent.value) return;
    await changeContent({
        course_id: idCourse,
        content_type: currentContent.value.type,
        content_id: id,
        learned,
        // content_old_type: currentContent.value.type,
        // content_old_id: currentContent.value.id
    });
};
const handleTimeUpdate = () => {
    if (videoElement.value && currentContent.value) {
        console.log(videoElement.value.currentTime)
        // updateLearned({ id: currentContent.value.id, learned: videoElement.value.currentTime });
    }
};
const handlePause = async () => {
    if (videoElement.value && currentContent.value) {
        console.log('Video paused at:', videoElement.value.currentTime);
        // Gọi API để cập nhật thời gian đã xem
        // await updateLearned({
        //     id: currentContent.value.id,
        //     learned: Math.floor(videoElement.value.currentTime), // Làm tròn giá trị thời gian
        // });
    }
};
// const handleTimeUpdate = () => {
//     if (videoElement.value && videoElement.value.paused) {
//         // const percentWatched = (videoElement.value.currentTime / videoElement.value.duration) * 100;
//         // // console.log(id)
//         // updateLearned({
//         //     id: id,
//         //     learned: Math.min(Math.round(percentWatched), 100),
//         // });
//         // console.log(id)
//         updateLearned({
//             id: id,
//             learned: videoElement.value.currentTime,
//         });
//     }
// };

const handleVideoEnd = () => {
    // updateLearned({ id: currentContent.value.id, learned: 100 });
    handleNextLesson();
};

const handleNextLesson = async () => {

    // const currentIndex = allContent.value.findIndex(
    //     (lesson) => lesson.id === currentContent.value.id
    // );
    // const nextLesson = allContent.value[currentIndex + 1];


    const currentSection = allContent.value.find(section =>
        section.section_content.some((lesson: any) => lesson.id === currentContent.value.id)
    );
    const sectionLessons = currentSection.section_content;
    const currentIndex = sectionLessons.findIndex((lesson: TLesson) => lesson.id === currentContent.value?.id);
    const nextLesson = sectionLessons[currentIndex + 1];
    // console.log(nextLesson)
    await handleChangeContent(nextLesson);
    // if (nextLesson) {
    //     changeContent(nextLesson.course_id, nextLesson.section_id, nextLesson);
    // }
};
</script>