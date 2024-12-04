<template>

    <div class="p-6 bg-gray-50 min-h-screen">
        <el-button type="info" plain>
            <ChevronLeftIcon class="h-4 w-4 text-gray-500" />

            Quay lại
        </el-button>
        <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-lg p-6 space-y-6">
            <!-- Header -->
            <header class="flex justify-between items-center">
                <h2 class="text-lg font-bold">Quản lý bài học</h2>
            </header>

            <!-- Danh sách bài học -->
            <section>
                <div class="flex items-center justify-between mb-10">
                    <h3 class="text-md font-semibold mb-4">Nội dung bài học</h3>
                    <div class="flex gap-2">
                        <el-button type="primary" @click="openAddLectureDialog">+ Thêm video/file bài học</el-button>
                        <el-button type="primary">+ Thêm quiz</el-button>
                        <el-button type="success">
                            <ArrowPathIcon class="mr-1 h-4 w-4 text-white" />
                            Khôi phục
                        </el-button>
                    </div>
                </div>
                <TransitionGroup tag="div" class="space-y-2" name="list" mode="out-in">
                    <Draggable v-model="listContentOfSection" class="space-y-2" @change="handleSort" :animation="200">
                        <div v-for="item in listContentOfSection" :key="item.id" v-motion
                            :initial="{ opacity: 0, y: 30 }" :enter="{ opacity: 1, y: 0 }"
                            :leave="{ opacity: 0, y: -30 }"
                            :transition="{ type: 'spring', stiffness: 150, damping: 20 }"
                            :class="['flex items-center justify-between p-4 rounded-lg shadow-md', item.status === 'active' ? 'bg-indigo-400' : 'bg-indigo-200']">
                            <div class="flex gap-3 items-center">
                                <div class="" v-if="item.type === 'video'">
                                    <VideoCameraIcon class="h-4 w-4 text-white" />
                                </div>
                                <div class="" v-else-if="item.type === 'file'">
                                    <DocumentIcon class="h-4 w-4 text-white" />
                                </div>
                                <div class="" v-else>
                                    <QuestionMarkCircleIcon class="h-4 w-4 text-white" />
                                </div>
                                <div class="text-white items-center">
                                    {{ item.duration }}
                                    <span v-if="item.type === 'video'">Giây</span>
                                    <span v-if="item.type === 'file'">Trang</span>
                                    <span v-else></span>
                                </div>
                                <span class="text-white" v-if="item.preview === 'can'">• Xem trước</span>
                            </div>
                            <!-- <div class="drag-handle cursor-move text-white">
                                {{ item.total_contents || 0 }} bài học
                            </div> -->
                            <p class="text-white">{{ item.title }}</p>
                            <div class="flex items-center gap-2">
                                <PencilSquareIcon class="h-5 w-5 cursor-pointer text-white" />
                                <TrashIcon class="h-5 w-5 cursor-pointer text-white" />
                                <EyeIcon class="h-5 w-5 cursor-pointer text-white" />
                            </div>
                        </div>
                    </Draggable>
                </TransitionGroup>
            </section>
        </div>
    </div>
    <!-- Dialog Thêm Bài Học -->
    <el-dialog v-model="isDialogVisible" title="Thêm Bài Học" width="600px">
        <el-form :model="newLecture" :rules="rules" ref="lectureForm" class="space-y-6">
            <!-- Tiêu đề bài học -->
            <el-form-item label="Tiêu đề bài học" prop="title" class="block">
                <el-input v-model="newLecture.title" placeholder="Nhập tiêu đề bài học" class="w-full" />
            </el-form-item>

            <!-- Loại bài học -->
            <el-form-item label="Loại bài học" prop="type" class="block">
                <el-radio-group v-model="newLecture.type" class="space-x-4">
                    <el-radio label="video">Video</el-radio>
                    <el-radio label="file">File</el-radio>
                </el-radio-group>
            </el-form-item>



            <el-form-item v-if="newLecture.type === 'video'" class="block">
                <el-upload class="upload-demo" drag :action="null" :auto-upload="false"
                    :before-upload="handleVideoUpload" :on-change="handleVideoUpload" multiple>
                    <el-icon class="el-icon--upload">
                        <UploadFilled />
                    </el-icon>
                    <div class="el-upload__text">
                        Thả video vào đây hoặc <em>click để tải lên</em>
                    </div>
                    <template #tip>
                        <div class="el-upload__tip">
                            Định dạng mp4
                        </div>
                    </template>
                </el-upload>
            </el-form-item>

            <el-form-item v-if="newLecture.type === 'file'" class="block">
                <el-upload class="upload-demo" drag :action="null" :auto-upload="false"
                    :before-upload="handleFileSelect" :on-change="handleFileSelect" multiple>
                    <el-icon class="el-icon--upload">
                        <UploadFilled />
                    </el-icon>
                    <div class="el-upload__text">
                        Thả file PDF vào đây hoặc <em>click để tải lên</em>
                    </div>
                    <template #tip>
                        <div class="el-upload__tip">
                            Định dạng PDF
                        </div>
                    </template>
                </el-upload>
            </el-form-item>

            <!-- new -->
            <!-- <el-upload ref="uploadRef" class="upload-demo" :action="null" :auto-upload="false"
                :before-upload="handleBeforeUpload" :on-change="handleFileSelect">
                <template #trigger>
                    <el-button type="primary">Chọn tệp</el-button>
                </template>
                <el-button class="ml-3" type="success" @click="addLecture">
                    Tải lên
                </el-button>
            </el-upload> -->
            <!-- Preview -->
            <el-form-item label="Thời lượng/Trang" prop="duration" class="block">
                <el-input v-model="newLecture.duration" :disabled="true" placeholder="Tự động tính toán"
                    class="w-full" />
            </el-form-item>

            <!-- Trạng thái bài học -->
            <el-form-item label="Trạng thái bài học" prop="status" class="block">
                <el-select v-model="newLecture.status" placeholder="Chọn trạng thái" class="w-full">
                    <el-option label="Hoạt động" value="active"></el-option>
                    <el-option label="Không hoạt động" value="inactive"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="Cho phép xem trước" prop="preview" class="block">
                <el-select v-model="newLecture.preview" placeholder="Chọn quyền xem trước" class="w-full">
                    <el-option label="Không cho phép" value="cant"></el-option>
                    <el-option label="Cho phép" value="can"></el-option>
                </el-select>
            </el-form-item>
        </el-form>
        <template #footer>
            <el-button @click="isDialogVisible = false">Hủy</el-button>
            <el-button type="primary" @click="addLecture">Thêm</el-button>
        </template>
    </el-dialog>
    <Loading :active="loading" :is-full-page="true" />
</template>

<script setup lang="ts">
import { useCourseStore } from '@/store/course';
import { ArrowPathIcon, ChevronLeftIcon, EyeIcon, PencilSquareIcon, TrashIcon, } from '@heroicons/vue/24/outline';
import { VideoCameraIcon, QuestionMarkCircleIcon, DocumentIcon } from '@heroicons/vue/20/solid';
import { storeToRefs } from 'pinia';
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { VueDraggableNext as Draggable } from "vue-draggable-next";
import { UploadFilled } from '@element-plus/icons-vue';
import type { TLecture } from '@/interfaces';
import { getDocument } from 'pdfjs-dist';
import { ElMessage, type UploadFile } from 'element-plus';
import Loading from 'vue-loading-overlay';
import { GlobalWorkerOptions } from 'pdfjs-dist';
GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.worker.min.js';
const route = useRoute();
const id_section = route.params.id ? Number(route.params.id) : null;
const useCourse = useCourseStore()
const { showContentOfSection, createLecture } = useCourse
const { listContentOfSection, loading } = storeToRefs(useCourse)
onMounted(async () => {
    await showContentOfSection(id_section || 0)
})

const isDialogVisible = ref(false);
const newLecture = ref<TLecture>({
    title: '',
    type: 'video',
    content: undefined,
    duration: 0,
    preview: 'cant',
    status: 'active',
    section_id: id_section || 0,
});
const resetForm = () => {
    newLecture.value = {
        title: '',
        type: 'video',
        content: undefined,
        duration: 0,
        preview: 'cant',
        status: 'active',
        section_id: id_section || 0,
    };
};
const lectureForm = ref();
const rules = {
    title: [{ required: true, message: 'Tiêu đề bài học là bắt buộc', trigger: 'blur' }],
    type: [{ required: true, message: 'Loại bài học là bắt buộc', trigger: 'change' }],
    status: [{ required: true, message: 'Trạng thái là bắt buộc', trigger: 'change' }],
};
const openAddLectureDialog = () => {
    isDialogVisible.value = true;
};

// Hàm xử lý trước khi chọn file


// const handleFileSelect = async (file: UploadFile) => {
//     console.log('File được chọn:', file);

//     // Nếu là file PDF
//     if (file.raw?.type === 'application/pdf') {
//         const reader = new FileReader();
//         reader.onload = async (e) => {
//             const typedarray = new Uint8Array(e.target?.result as ArrayBuffer);
//             const loadingTask = getDocument(typedarray);

//             try {
//                 const pdf = await loadingTask.promise;
//                 newLecture.value.duration = pdf.numPages; // Số trang PDF
//                 console.log('Số trang PDF:', pdf.numPages);
//             } catch (error) {
//                 console.error('Lỗi khi đọc file PDF:', error);
//             }
//         };
//         reader.readAsArrayBuffer(file.raw as File);
//     }

//     // Gán file vào `newLecture.value.content`
//     newLecture.value.content = file.raw; // `file.raw` chứa `File` gốc
//     console.log('File đã gán:', newLecture.value.content);

//     return false; // Ngăn upload tự động
// };


const handleFileSelect = async (file: UploadFile) => {
    console.log('File được chọn:', file);

    if (file.raw?.type === 'application/pdf') {
        const reader = new FileReader();
        reader.onload = async (e) => {
            const typedarray = new Uint8Array(e.target?.result as ArrayBuffer);
            const loadingTask = getDocument({ data: typedarray });

            try {
                const pdf = await loadingTask.promise;
                newLecture.value.duration = pdf.numPages; // Số trang PDF
                console.log('Số trang PDF:', pdf.numPages);
            } catch (error: any) {
                console.error('Lỗi khi đọc file PDF:', error.message);
                ElMessage.error('Không thể đọc số trang của file PDF.');
            }
        };
        reader.readAsArrayBuffer(file.raw as File);
    } else {
        console.error('Không phải file PDF:', file.raw?.type);
        ElMessage.error('Vui lòng tải lên file PDF hợp lệ.');
    }

    newLecture.value.content = file.raw; // `file.raw` chứa file gốc
    return false; // Ngăn upload tự động
};

const handleVideoUpload = (file: UploadFile) => {
    // console.log('Video được chọn:', file);

    const video = document.createElement('video');
    video.preload = 'metadata';
    video.src = URL.createObjectURL(file.raw as File);

    video.onloadedmetadata = () => {
        newLecture.value.duration = Math.round(video.duration); // Tính thời lượng video
        // console.log('Thời lượng video:', video.duration);
        URL.revokeObjectURL(video.src);
    };

    // Gán file video vào `newLecture.value.content`
    newLecture.value.content = file.raw; // `file.raw` chứa `File` gốc
    // console.log('Video đã gán:', newLecture.value.content);

    return false; // Ngăn upload tự động
};
const addLecture = async () => {
    await lectureForm.value.validate();
    const formData = new FormData();
    // newLecture.value.content = selectedFile.value
    formData.append('title', newLecture.value.title || '');
    formData.append('type', newLecture.value.type || '');
    formData.append('status', newLecture.value.status || '');
    formData.append('preview', newLecture.value.preview || '');
    formData.append('section_id', String(newLecture.value.section_id || 0));
    formData.append('duration', String(newLecture.value.duration || 0));
    if (newLecture.value.content instanceof File) {
        formData.append('content', newLecture.value.content); // Sửa thành `content` thay vì `content.raw`
        // console.log('File hợp lệ được gán:', newLecture.value.content);
    } else {
        // console.error('Lỗi: Không phải file hợp lệ.', newLecture.value.content);
        ElMessage.error('File không hợp lệ.');
        return;
    }
    await createLecture(id_section || 0, formData);
    isDialogVisible.value = false;
    resetForm();

};
const handleSort = async () => {

}


</script>

<style scoped>
.el-upload {
    border: 2px dashed #d3d3d3;
    padding: 16px;
    background-color: #f9f9f9;
    border-radius: 8px;
}

.el-upload:hover {
    background-color: #f0f8ff;
}
</style>