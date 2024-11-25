<template>
    <div class="p-6">
        <h1 class="text-xl font-bold mb-6">Quản lý người dùng</h1>

        <!-- Bộ lọc -->
        <!-- <div class="mb-6 flex items-center gap-4">
            <el-input class="w-72" v-model="filters.keyword" placeholder="Tìm kiếm theo email" clearable
                @input="fetchUsers" />
            <el-select class="w-48" v-model="filters.role" placeholder="Chọn vai trò" clearable @change="fetchUsers">
                <el-option label="Học viên" value="student" />
                <el-option label="Giảng viên" value="instructor" />
                <el-option label="Admin" value="admin" />
            </el-select>
        </div> -->

        <!-- Bảng người dùng -->
        <el-table :data="state.allUser" class="w-full">
            <el-table-column prop="avatar" label="Avatar" width="100">
                <template #default="{ row }">
                    <img :src="row.avatar" alt="avatar" class="h-12 w-12 rounded-full" />
                </template>
            </el-table-column>
            <el-table-column prop="email" label="Email" />
            <el-table-column prop="first_name" label="Họ" />
            <el-table-column prop="last_name" label="Tên" />
            <el-table-column prop="role" label="Vai trò" />
            <el-table-column prop="status" label="Trạng thái">
                <template #default="{ row }">
                    <el-tag :type="row.status === 'active' ? 'success' : 'info'">
                        {{ row.status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
                    </el-tag>
                </template>
            </el-table-column>
        </el-table>

        <!-- Phân trang -->
        <!-- <div class="mt-6 flex justify-center">
            <el-pagination v-model:current-page="pagination.currentPage" :page-size="pagination.perPage"
                :total="pagination.total" layout="prev, pager, next" @current-change="handlePageChange" />
        </div> -->
    </div>
</template>
<script setup lang="ts">
import { useAuthStore } from '@/store/auth';
import { storeToRefs } from 'pinia';
import { reactive, onMounted, watch } from 'vue';

const useStore = useAuthStore();
const { fetchAllUser } = useStore;
const { state } = storeToRefs(useStore);

const filters = reactive({
    keyword: '',
    role: '',
});

const pagination = reactive({
    currentPage: 1,
    perPage: 10,
    total: 0,
});

// Fetch users function
// const fetchUsers = async () => {
//     const params = {
//         page: pagination.currentPage,
//         per_page: pagination.perPage,
//         keyword: filters.keyword,
//         role: filters.role,
//     };
//     await fetchAllUser(params);
//     pagination.total = state.allUser?.length || 0; // Giả sử API trả tổng số bản ghi
// };

// Handle pagination changes
// const handlePageChange = (page: number) => {
//     pagination.currentPage = page;
//     fetchUsers();
// };

// Fetch users on mounted and when filters change
onMounted(() => {
    fetchAllUser();
});

// watch(filters, fetchUsers, { deep: true });
</script>
