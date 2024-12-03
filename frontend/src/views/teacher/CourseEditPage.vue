<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-lg p-6 space-y-6">
            <!-- Header -->
            <header class="flex justify-between items-center">
                <h2 class="text-lg font-bold">Quản lý bài học</h2>
                <!-- <el-button type="primary" @click="openAddLectureModal">Thêm bài học</el-button> -->
            </header>

            <!-- Danh sách bài học -->
            <section>
                <h3 class="text-md font-semibold mb-4">Nội dung chương học</h3>
                <!-- <draggable v-model="listContentOfSection" class="space-y-4" handle=".drag-handle" @end="handleSort">
                    <div v-for="lecture in listContentOfSection" :key="lecture.id"
                        class="flex justify-between items-center bg-gray-100 p-4 rounded-md shadow-sm">
                        <h2>{{ lecture }}</h2>
                        <div class="flex items-center gap-4">
                            <div class="drag-handle cursor-move text-gray-500">
                                <i class="el-icon-rank"></i>
                            </div>
                            <div>
                                <p class="font-semibold">{{ lecture.title }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ lecture.type.toUpperCase() }} • {{ lecture.duration }} phút
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <el-tag v-if="lecture.status === 'active'" type="success">Hoạt động</el-tag>
                            <el-tag v-else type="warning">Không hoạt động</el-tag>
                            <el-button type="text" icon="el-icon-edit" @click="editLecture()"></el-button>
                            <el-button type="text" icon="el-icon-delete" @click="deleteLecture()"></el-button>
                        </div>
                    </div>
                </draggable> -->
                <Draggable class="dragArea list-group w-full" :list="list" @change="log">
                    <div class="list-group-item bg-gray-300 m-1 p-3 rounded-md text-center" v-for="element in list"
                        :key="element.id">
                        {{ element.name }}
                    </div>
                </Draggable>
                <!-- <div v-for="lecture in listContentOfSection" :key="lecture.id">
                    <h3>{{ lecture.content_link }}</h3>
                </div> -->
            </section>
        </div>

        <!-- Modal Thêm/Sửa bài học -->
        <!-- <el-dialog v-model="isLectureModalVisible" title="Thêm/Sửa Bài Học" width="500px">
            <el-form :model="lectureForm">
                <el-form-item label="Tiêu đề">
                    <el-input v-model="lectureForm.title" placeholder="Nhập tiêu đề bài học"></el-input>
                </el-form-item>
                <el-form-item label="Loại">
                    <el-select v-model="lectureForm.type" placeholder="Chọn loại bài học">
                        <el-option label="Video" value="video"></el-option>
                        <el-option label="File" value="file"></el-option>
                        <el-option label="Quiz" value="quiz"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Thời lượng">
                    <el-input-number v-model="lectureForm.duration" placeholder="Thời lượng (phút)" />
                </el-form-item>
                <el-form-item label="Trạng thái">
                    <el-switch v-model="lectureForm.status" active-text="Hoạt động" inactive-text="Không hoạt động" />
                </el-form-item>
            </el-form>
            <template #footer>
                <el-button @click="isLectureModalVisible = false">Hủy</el-button>
                <el-button type="primary" @click="saveLecture">Lưu</el-button>
            </template>
</el-dialog> -->
    </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from "vue";
// import draggable from "vuedraggable";
import { ElMessage, ElMessageBox } from "element-plus";
import { useCourseStore } from "@/store/course";
import { useRoute } from "vue-router";
import type { TLecture } from "@/interfaces";

import { VueDraggableNext as Draggable } from "vue-draggable-next";


const list = ref([
    { name: "John", id: 1 },
    { name: "Joao", id: 2 },
    { name: "Jean", id: 3 },
    { name: "Gerard", id: 4 },
]);


const log = (event: any) => {
    console.log("Dragged Event:", event);
};
// const isLectureModalVisible = ref(false); // Modal hiển thị thêm/sửa bài học
// const lectureForm = ref({
//     id: null,
//     title: "",
//     type: "video",
//     duration: 0,
//     status: "active",
// }); // Form dữ liệu cho thêm/sửa bài học
// const route = useRoute();
// const id_course = route.params.id ? Number(route.params.id) : null;
// const useCourse = useCourseStore()
// const { showContentOfSection,
//     fetchListLecturesAdmin,
//     lectureEditFrom,
//     createLecture,
//     updateLecture,
//     deleteLecture,
//     updateSectionLecture,
//     updateStatusLecture,
//     sortContentOfSection } = useCourse
// const { listContentOfSection,
//     listLecturesAdmin,
//     dataForm } = useCourse



// // Xử lý kéo thả
// const handleSort = async () => {
//     try {
//         const sortedData = listContentOfSection.value.map((item, index) => ({
//             id: item.id,
//             order: index + 1,
//         }));
//         await api.put("/auth/sort-content-of-section", { sorted_content: sortedData });
//         ElMessage.success("Cập nhật thứ tự bài học thành công");
//     } catch (error) {
//         console.error("Lỗi khi sắp xếp bài học:", error);
//         ElMessage.error("Không thể cập nhật thứ tự bài học");
//     }
// };
// // Khởi tạo
// onMounted(async () => {
//     await showContentOfSection(id_course || 0); // ID chương cần hiển thị
// });
// console.log(listContentOfSection)
// const openAddLectureModal = () => {
//     lectureForm.value = {
//         section_id: undefined,
//         type: "video", // Giá trị mặc định
//         title: "",
//         content: "",
//         duration: 0,
//         preview: null,
//         status: "active",
//         order: undefined,
//     };
//     isLectureModalVisible.value = true;
// };

// const saveLecture = async () => {
//     try {
//         if (!id_course) {
//             ElMessage.error("ID khóa học không hợp lệ");
//             return;
//         }
//         if (lectureForm.value.id) {
//             // Sửa bài học
//             await updateLecture(id_course, lectureForm.value.id, lectureForm.value)
//         } else {
//             await createLecture(id_course, lectureForm.value)
//         }
//         isLectureModalVisible.value = false;
//         await showContentOfSection(id_course); // ID chương cần hiển thị
//     } catch (error) {
//         console.error("Lỗi khi lưu bài học:", error);
//         ElMessage.error("Không thể lưu bài học");
//     }

// };

// const editLecture = (lecture: TLecture) => {
//     lectureForm.value = { ...lecture };
//     isLectureModalVisible.value = true;
// };
</script>
