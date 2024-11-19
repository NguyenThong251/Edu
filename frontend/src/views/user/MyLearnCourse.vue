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
                                <video controls preload="metadata" @timeupdate="handleTimeUpdate" @pause="handlePause"
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
                            <h2 class="text-xl font-semibold mb-4 text-center">Bài tập: {{ currentContent.title }}</h2>

                            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                                <div class="bg-blue-500 h-2.5 rounded-full" :style="{ width: progressQuizz + '%' }">
                                </div>
                            </div>

                            <p class="text-center mb-6">{{ currentQuestion.question }}</p>

                            <form class="space-y-4" @submit.prevent="checkAnswer">
                                <!-- <label class="flex items-center bg-blue-50 p-3 rounded-lg cursor-pointer">
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
                                </button> -->
                                <label v-for="(option, index) in currentQuestion.options" :key="index"
                                    class="flex items-center bg-blue-50 p-3 rounded-lg cursor-pointer">
                                    <input type="radio" name="question" class="form-radio h-4 w-4 text-blue-600"
                                        :value="option" v-model="selectedAnswer" />
                                    <span class="ml-2 text-gray-700">{{ option }}</span>
                                </label>

                                <button type="submit"
                                    class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg text-center mt-4">
                                    Gửi câu trả lời
                                </button>
                            </form>
                            <p v-if="feedbackMessage" class="text-center mt-4 text-red-500">{{ feedbackMessage }}</p>
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
import { computed, onMounted, ref } from 'vue';
import { PlayCircleIcon, CheckCircleIcon as CheckOuline, QuestionMarkCircleIcon, DocumentIcon } from "@heroicons/vue/24/outline";
import { useRoute } from 'vue-router';
import { useCourseStore } from '@/store/course';
import { storeToRefs } from 'pinia';
import type { TLesson } from '@/interfaces';
import UserSearch from '@/components/user/mycourse/UserSearch.vue';
import UserQuestion from '@/components/user/mycourse/UserQuestion.vue';
import UserNote from '@/components/user/mycourse/UserNote.vue';
import UserFeedback from '@/components/user/mycourse/UserFeedback.vue';
import { useQuizStore } from '@/store/quiz';
const route = useRoute();
const idCourse = Number(route.params.id);
const courseStore = useCourseStore();
const quizStore = useQuizStore();
const { studyCourse, currentContent, allContent, progress } = storeToRefs(courseStore);
// const { currentQuestionIndex, progressQuizz } = storeToRefs(quizStore);
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
    await changeContent(data);

};
const updateLearned = async ({ id, learned }: { id: number; learned: number }) => {
    if (!currentContent.value) return;
    // console.log(learned, id)
    const data = {
        course_id: idCourse,
        content_type: currentContent.value.current_content_type,
        content_id: id,
        learned,
        content_old_type: currentContent.value?.current_content_type,
    }
    await changeContent(data);

};
const handleTimeUpdate = () => {
    // if (videoElement.value && currentContent.value) {
    //     console.log(videoElement.value.currentTime)
    //     updateLearned({ id: currentContent.value.id, learned: videoElement.value.currentTime });
    // }
};
const handlePause = async () => {
    if (videoElement.value && currentContent.value) {
        // console.log(currentContent.value.learned)
        if (videoElement.value.currentTime > currentContent.value.learned) {

            // console.log('hi')
            updateLearned({ id: currentContent.value.id, learned: videoElement.value.currentTime });
        }
        // console.log(videoElement.value.currentTime)
        // Gọi API để cập nhật thời gian đã xem
        // await updateLearned({
        //     id: currentContent.value.id,
        //     learned: Math.floor(videoElement.value.currentTime), // Làm tròn giá trị thời gian
        // });
    }
};


const handleVideoEnd = () => {
    if (videoElement.value && currentContent.value) {
        updateLearned({ id: currentContent.value.id, learned: videoElement.value.currentTime });
    }
    handleNextLesson();
};

const handleNextLesson = async () => {
    const currentSection = allContent.value.find(section =>
        section.section_content.some((lesson: any) => lesson.id === currentContent.value.id)
    );
    const sectionLessons = currentSection.section_content;
    const currentIndex = sectionLessons.findIndex((lesson: TLesson) => lesson.id === currentContent.value?.id);
    const nextLesson = sectionLessons[currentIndex + 1];
    await handleChangeContent(nextLesson);

};
// Quizz

const selectedAnswer = ref('');
const feedbackMessage = ref('');
const currentQuestionIndex = ref(0);
const progressQuizz = ref(0);
// Lấy câu hỏi hiện tại
const currentQuestion = computed(() => currentContent.value?.questions[currentQuestionIndex.value] || {});

// Tiến trình (progress bar)
// const progressQuizz = computed(() =>
//     ((currentQuestionIndex.value + 1) / currentContent.value?.questions.length) * 100
// );
const updateProgress = () => {
    progressQuizz.value = ((currentQuestionIndex.value) / currentContent.value.questions.length) * 100;
};
// Hàm kiểm tra câu trả lời
const checkAnswer = async () => {
    if (selectedAnswer.value === currentQuestion.value.answer) {
        const payload = {
            course_id: idCourse,
            content_type: 'quiz',
            content_id: currentContent.value.id,
            content_old_type: currentContent.value.current_content_type,
            content_old_id: currentContent.value.id,
            questions_done: currentQuestionIndex.value + 1,
            question_id: currentQuestion.value.id,
            answer_user: selectedAnswer.value,
            total_questions: currentContent.value.questions.length
        };
        console.log(payload)
        await quizStore.handleAnswer(payload);
        feedbackMessage.value = ''; // Xóa thông báo lỗi
        nextQuestion(); // Chuyển sang câu hỏi tiếp theo hoặc hoàn thành quiz
    } else {
        feedbackMessage.value = 'Câu trả lời không chính xác. Hãy thử lại!';
    }
};

// Hàm xử lý chuyển câu hỏi tiếp theo
const nextQuestion = () => {
    if (currentQuestionIndex.value < currentContent.value.questions.length - 1) {
        currentQuestionIndex.value++; // Tăng chỉ số câu hỏi
        updateProgress(); // Cập nhật tiến trình
        selectedAnswer.value = ''; // Reset câu trả lời
        feedbackMessage.value = ''; // Xóa thông báo cũ
        // const questionId = currentQuestion.value.id; // ID của câu hỏi hiện tại
        // const userAnswer = selectedAnswer.value; // Câu trả lời của người dùng
        // const data = {
        //     course_id: idCourse, // ID của khóa học
        //     content_type: 'quiz', // Loại nội dung
        //     content_id: currentContent.value?.id, // ID của quiz hiện tại
        //     content_old_type: currentContent.value?.current_content_type,
        //     content_old_id: currentContent.value?.id,
        //     questions_done: currentQuestionIndex.value + 1, // Số câu đã hoàn thành
        //     question_id: questionId, // ID của câu hỏi hiện tại
        //     answer_user: userAnswer, // Câu trả lời của người dùng
        // }
    } else {
        handleQuizCompletion(); // Hoàn thành quiz
    }
};
// // Xử lý khi hoàn thành quiz
const handleQuizCompletion = () => {
    progressQuizz.value = 100; // Đảm bảo tiến trình đạt 100% khi hoàn thành
    feedbackMessage.value = 'Bạn đã hoàn thành bài tập!'; // Hiển thị thông báo
    console.log('Quiz completed!'); // Log hoàn thành
    // Thêm logic cập nhật trạng thái bài học (nếu cần)
    // Ví dụ: Gọi API để đánh dấu quiz đã hoàn thành
};


// const checkAnswer = async () => {
//     const payload = {
//         course_id: idCourse,
//         content_type: 'quiz',
//         content_id: currentContent.value.id,
//         content_old_type: currentContent.value.current_content_type,
//         content_old_id: currentContent.value.id,
//         questions_done: currentQuestionIndex.value + 1,
//         question_id: currentQuestion.value.id,
//         answer_user: selectedAnswer.value,
//         total_questions: currentContent.value.questions.length
//     };
//     console.log(payload)
//     await quizStore.handleAnswer(payload);

//     if (!quizStore.error) {
//         feedbackMessage.value = '';
//         nextQuestion();
//     } else {
//         feedbackMessage.value = quizStore.error;
//     }
// };
// const nextQuestion = async () => {
//     // Kiểm tra xem đã hoàn thành quiz chưa
//     if (currentQuestionIndex.value < currentContent.value.questions.length - 1) {
//         const payload = {
//             course_id: idCourse,
//             content_type: 'quiz',
//             content_id: currentContent.value.id,
//             content_old_type: currentContent.value.current_content_type,
//             content_old_id: currentContent.value.id,
//             questions_done: currentQuestionIndex.value + 1,
//             question_id: currentQuestion.value.id,
//             answer_user: selectedAnswer.value,
//             total_questions: currentContent.value.questions.length
//         };

//         // Gửi câu trả lời qua API
//         await quizStore.handleAnswer(payload);

//         // Nếu câu trả lời đúng, chuyển sang câu hỏi tiếp theo
//         if (!quizStore.error) {
//             currentQuestionIndex.value++; // Cập nhật câu hỏi hiện tại
//             feedbackMessage.value = ''; // Xóa thông báo lỗi
//             selectedAnswer.value = ''; // Reset câu trả lời
//         } else {
//             feedbackMessage.value = quizStore.error; // Hiển thị lỗi nếu có
//         }
//     } else {
//         handleQuizCompletion(); // Hoàn thành quiz
//     }
// };
</script>