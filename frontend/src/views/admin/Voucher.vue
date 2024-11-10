<template>
    <div class="p-4">
        <h1 class="text-xl font-bold mb-4">Quản lý Voucher</h1>
        <!-- Nút thêm voucher mới -->
        <el-button type="primary" icon="heroicon-o-plus" @click="openDrawer">
            Thêm Voucher
        </el-button>

        <!-- Bảng hiển thị danh sách voucher -->
        <el-table :data="voucherStore.state.vouchers" class="mt-4">
            <el-table-column prop="code" label="Mã" width="180" />
            <el-table-column prop="description" label="Mô tả" />
            <el-table-column prop="discount_value" label="Giá trị giảm" width="120" />
            <el-table-column prop="expires_at" label="Ngày hết hạn" width="180" />
            <el-table-column label="Hành động" width="200">
                <template #default="{ row }">
                    <el-button type="primary" icon="heroicon-o-pencil-alt" @click="editVoucher(row)">
                        Sửa
                    </el-button>
                    <el-button type="danger" icon="heroicon-o-trash" @click="deleteVoucher(row.code)">
                        Xóa
                    </el-button>
                    <el-button type="warning" icon="heroicon-o-refresh" @click="restoreVoucher(row.code)">
                        Khôi phục
                    </el-button>
                </template>
            </el-table-column>
        </el-table>

        <!-- Drawer tạo và chỉnh sửa voucher -->
        <el-drawer title="Tạo Voucher" v-model:visible="drawerVisible" size="30%">
            <form @submit.prevent="handleSubmit">
                <div class="mb-4">
                    <label class="block text-sm font-medium">Mã Voucher</label>
                    <el-input v-model="voucherForm.code" placeholder="Nhập mã voucher" />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Mô tả</label>
                    <el-input v-model="voucherForm.description" placeholder="Nhập mô tả" />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Loại giảm giá</label>
                    <el-select v-model="voucherForm.discount_type" placeholder="Chọn loại giảm giá">
                        <el-option label="Phần trăm" value="percent" />
                        <el-option label="Giá trị cố định" value="fixed" />
                    </el-select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Giá trị giảm</label>
                    <el-input-number v-model="voucherForm.discount_value" placeholder="Nhập giá trị giảm" />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Giới hạn sử dụng</label>
                    <el-input-number v-model="voucherForm.usage_limit" placeholder="Nhập giới hạn sử dụng" />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Ngày hết hạn</label>
                    <el-date-picker v-model="voucherForm.expires_at" placeholder="Chọn ngày hết hạn" type="date" />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Giá trị đơn hàng tối thiểu</label>
                    <el-input-number v-model="voucherForm.min_order_value"
                        placeholder="Nhập giá trị đơn hàng tối thiểu" />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Giá trị giảm tối đa</label>
                    <el-input-number v-model="voucherForm.max_discount_value" placeholder="Nhập giá trị giảm tối đa" />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Trạng thái</label>
                    <el-select v-model="voucherForm.status" placeholder="Chọn trạng thái">
                        <el-option label="Hoạt động" value="active" />
                        <el-option label="Không hoạt động" value="inactive" />
                    </el-select>
                </div>
                <div class="flex justify-end">
                    <el-button @click="drawerVisible = false">Hủy</el-button>
                    <el-button type="primary" native-type="submit">Lưu</el-button>
                </div>
            </form>
        </el-drawer>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useVoucherStore } from '@/store/voucher'
import type { TVoucher } from '@/interfaces/voucher';

const drawerVisible = ref(false)
const voucherForm = ref<TVoucher>({
    code: '',
    description: '',
    discount_type: 'percent', // Hoặc giá trị mặc định khác tùy nhu cầu
    discount_value: 0,
    usage_limit: 0,
    expires_at: '',
    min_order_value: 0,
    max_discount_value: 0,
    status: 'active'
})

const voucherStore = useVoucherStore()

// Lấy danh sách voucher khi component mount
onMounted(async () => {
    await voucherStore.fetchVouchers()
})

const openDrawer = () => {
    drawerVisible.value = true
}

const handleSubmit = async () => {
    await voucherStore.createVoucher(voucherForm.value)
    drawerVisible.value = false
    await voucherStore.fetchVouchers() // Tải lại danh sách voucher sau khi thêm mới
}

const editVoucher = (voucher: number | string) => {
    Object.assign(voucherForm.value, voucher)
    drawerVisible.value = true
}

const deleteVoucher = async (code: number | string) => {
    await voucherStore.deleteVoucher(code)
    await voucherStore.fetchVouchers() // Tải lại danh sách voucher sau khi xóa
}

const restoreVoucher = async (code: number | string) => {
    await voucherStore.restoreVoucher(code)
    // await voucherStore.fetchDeletedVouchers() // Tải lại danh sách voucher đã xóa
}
</script>
