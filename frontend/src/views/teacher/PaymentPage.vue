<template>
    <div class="p-5 bg-gray-50 min-h-screen">
        <div class="flex justify-between items-center flex-wrap gap-3">
            <h1 class="text-2xl font-bold">Lịch sử rút tiền</h1>
            <el-button type="primary" class="!py-2 !px-4 rounded-lg" @click="handleWithdrawClick">
                + Yêu cầu rút tiền
            </el-button>
        </div>

        <!-- Tổng quan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-5">
            <div class="bg-white p-5 shadow rounded-lg flex flex-col items-center justify-center">
                <h2 class="text-2xl font-bold text-indigo-500">96</h2>
                <p class="text-gray-600">Tiền đã nhận</p>
            </div>
            <div class="bg-white p-5 shadow rounded-lg flex flex-col items-center justify-center">
                <h2 class="text-2xl font-bold text-indigo-500">96</h2>
                <p class="text-gray-600">Tổng thu nhập</p>
            </div>
            <div class="bg-white p-5 shadow rounded-lg flex flex-col items-center justify-center">
                <h2 class="text-2xl font-bold text-indigo-500">96</h2>
                <p class="text-gray-600">Tiền đang yêu cầu</p>
            </div>
        </div>

        <!-- Bộ lọc và bảng -->
        <div class="mt-8 bg-white p-5 rounded-lg shadow">
            <!-- Bộ lọc -->
            <div class="flex flex-wrap justify-between gap-3 items-center">
                <el-date-picker v-model="filterDate" type="daterange" placeholder="Chọn khoảng thời gian"
                    class="w-full sm:w-1/2 md:w-1/3" />
                <el-button type="primary" class="!py-2 !px-4" @click="handleFilter">
                    Lọc
                </el-button>
            </div>

            <!-- Bảng -->
            <el-table :data="tableData" stripe class="mt-5" style="width: 100%">
                <el-table-column prop="id" label="#" width="50" />
                <el-table-column prop="amount" label="Số tiền rút" />
                <el-table-column prop="requestDate" label="Ngày yêu cầu" />
                <el-table-column prop="paymentDate" label="Ngày thanh toán" />
                <el-table-column prop="status" label="Trạng thái">
                    <template #default="scope">
                        <span v-if="scope.row.status === 'Đã thanh toán'"
                            class="bg-green-100 text-green-600 px-3 py-1 rounded-md">
                            {{ scope.row.status }}
                        </span>
                        <span v-else class="bg-red-100 text-red-600 px-3 py-1 rounded-md">
                            {{ scope.row.status }}
                        </span>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <!-- Modal Rút Tiền -->
        <el-dialog title="Yêu cầu rút tiền" v-model="isWithdrawModalVisible" width="500px">
            <div class="space-y-4">
                <el-form ref="withdrawForm" :model="withdrawForm" :rules="rules" label-width="120px">
                    <el-form-item label="Số tiền rút" prop="amount">
                        <el-input v-model="withdrawForm.amount" type="number" placeholder="Nhập số tiền muốn rút" />
                    </el-form-item>
                    <el-form-item label="Số tài khoản Stripe" prop="stripeAccount">
                        <el-input v-model="withdrawForm.stripeAccount" placeholder="Nhập số tài khoản Stripe" />
                    </el-form-item>
                </el-form>
            </div>
            <template #footer>
                <el-button @click="isWithdrawModalVisible = false">Hủy</el-button>
                <el-button type="primary" @click="submitWithdrawForm">Xác nhận</el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref } from "vue";

// Modal và Form
const isWithdrawModalVisible = ref(false);
const withdrawForm = ref({
    amount: '',
    stripeAccount: ''
});
const rules = {
    amount: [{ required: true, message: 'Vui lòng nhập số tiền muốn rút', trigger: 'blur' }],
    stripeAccount: [{ required: true, message: 'Vui lòng nhập số tài khoản Stripe', trigger: 'blur' }]
};

// Dữ liệu bộ lọc và bảng
const filterDate = ref<[Date, Date] | null>(null);
const tableData = ref([
    {
        id: 1,
        amount: "234,000,000 vnd",
        requestDate: "12/09/2024 - 11:00 pm",
        paymentDate: "12/09/2024 - 11:00 pm",
        status: "Đã thanh toán",
    },
    {
        id: 2,
        amount: "234,000,000 vnd",
        requestDate: "12/09/2024 - 11:00 pm",
        paymentDate: "12/09/2024 - 11:00 pm",
        status: "Đang chờ xử lý",
    },
]);

// Xử lý lọc
const handleFilter = () => {
    console.log("Lọc dữ liệu theo ngày:", filterDate.value);
};

// Hiển thị Modal Rút Tiền
const handleWithdrawClick = () => {
    isWithdrawModalVisible.value = true;
};

// Xử lý Gửi Form Rút Tiền
const submitWithdrawForm = () => {
    const form = ref(null);
    form.value?.validate((valid: boolean) => {
        if (valid) {
            console.log("Yêu cầu rút tiền thành công:", withdrawForm.value);
            isWithdrawModalVisible.value = false;
        } else {
            console.error("Lỗi trong form.");
        }
    });
};
</script>

<style scoped></style>
