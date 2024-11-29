<template>
    <div class="p-5 bg-gray-50 min-h-screen">
        <div class="flex justify-between items-center flex-wrap gap-3">
            <h1 class="text-2xl font-bold">Quản lý yêu cầu rút tiền</h1>
        </div>

        <!-- Bộ lọc và bảng -->
        <div class="mt-8 bg-white p-5 rounded-lg shadow">
            <!-- Bộ lọc -->
            <div class="flex flex-wrap justify-between gap-3 items-center">
                <el-date-picker v-model="filterDate" type="daterange" placeholder="Chọn khoảng thời gian"
                    class="w-full sm:w-1/2 md:w-1/3" />
                <el-select v-model="filterStatus" placeholder="Chọn trạng thái" class="w-full sm:w-1/4 md:w-1/5">
                    <el-option label="Tất cả" value="" />
                    <el-option label="Đang xử lý" value="processing" />
                    <el-option label="Đã thanh toán" value="paid" />
                </el-select>
                <el-button type="primary" class="!py-2 !px-4" @click="handleFilter">
                    Lọc
                </el-button>
            </div>

            <!-- Bảng -->
            <el-table :data="tableData" stripe class="mt-5" style="width: 100%">
                <el-table-column prop="id" label="#" width="50" />
                <el-table-column prop="teacher" label="Tên giáo viên" />
                <el-table-column prop="amount" label="Số tiền rút" />
                <el-table-column prop="requestDate" label="Ngày yêu cầu" />
                <el-table-column prop="status" label="Trạng thái">
                    <template #default="scope">
                        <span v-if="scope.row.status === 'paid'"
                            class="bg-green-100 text-green-600 px-3 py-1 rounded-md">
                            Đã thanh toán
                        </span>
                        <span v-else class="bg-yellow-100 text-yellow-600 px-3 py-1 rounded-md">
                            Đang xử lý
                        </span>
                    </template>
                </el-table-column>
                <el-table-column label="Hành động" width="200">
                    <template #default="scope">
                        <div class="flex ">
                            <el-button type="success" size="small" v-if="scope.row.status !== 'paid'"
                                @click="handleApprove(scope.row)">
                                Xác nhận
                            </el-button>
                            <el-button type="danger" size="small" @click="handleReject(scope.row)"
                                v-if="scope.row.status !== 'paid'">
                                Từ chối
                            </el-button>
                        </div>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <!-- Modal Xác nhận -->
        <el-dialog title="Xác nhận thanh toán" v-model="isApproveModalVisible" width="500px">
            <p>Bạn có chắc chắn muốn thanh toán yêu cầu rút tiền này?</p>
            <div class="mt-4">
                <strong>Số tiền:</strong> {{ selectedRequest?.amount || '' }}
            </div>
            <div class="mt-4">
                <strong>Giáo viên:</strong> {{ selectedRequest?.teacher || '' }}
            </div>
            <template #footer>
                <el-button @click="isApproveModalVisible = false">Hủy</el-button>
                <el-button type="primary" @click="confirmApprove">Xác nhận</el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

// Bộ lọc và bảng
const filterDate = ref<[Date, Date] | null>(null);
const filterStatus = ref('');
const tableData = ref([
    {
        id: 1,
        teacher: 'Nguyễn Văn A',
        amount: '234,000,000 VND',
        requestDate: '12/09/2024 - 11:00 pm',
        status: 'processing',
    },
    {
        id: 2,
        teacher: 'Trần Thị B',
        amount: '100,000,000 VND',
        requestDate: '13/09/2024 - 10:00 am',
        status: 'paid',
    },
]);

// Modal Xác nhận
const isApproveModalVisible = ref(false);
const selectedRequest = ref<any>(null);

// Xử lý lọc
const handleFilter = () => {
    console.log('Lọc theo:', filterDate.value, filterStatus.value);
    // Gọi API hoặc lọc dữ liệu tại đây
};

// Xử lý thanh toán
const handleApprove = (row: any) => {
    selectedRequest.value = row;
    isApproveModalVisible.value = true;
};

const confirmApprove = () => {
    console.log('Thanh toán cho:', selectedRequest.value);
    // Gọi API cập nhật trạng thái
    selectedRequest.value.status = 'paid';
    isApproveModalVisible.value = false;
};

// Xử lý từ chối yêu cầu
const handleReject = (row: any) => {
    console.log('Từ chối yêu cầu của:', row);
    // Gọi API hoặc xử lý tại đây
};
</script>

<style scoped></style>
