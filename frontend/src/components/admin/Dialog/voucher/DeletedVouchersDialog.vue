<template>
    <el-dialog title="Danh sách Voucher Đã Xóa" v-model:visible="localVisible" width="50%" @close="onClose">
        <el-table :data="deletedVouchers" class="rounded-md">
            <el-table-column prop="code" label="Mã" />
            <el-table-column prop="description" label="Mô tả" />
            <el-table-column prop="discount_value" label="Giá trị giảm" />
            <el-table-column prop="status" label="Trạng thái" />
            <el-table-column label="Hành động">
                <template #default="{ row }">
                    <div class="flex justify-center gap-1">
                        <ArrowPathIcon class="h-5 w-5 text-green-500 cursor-pointer" @click="confirmRestore(row)" />
                    </div>
                </template>
            </el-table-column>
        </el-table>

        <!-- Dialog xác nhận khôi phục voucher -->
        <ConfirmationDialog v-model:visible="showRestoreConfirm" :message="restoreConfirmMessage"
            @confirm="handleRestoreConfirm" @cancel="handleRestoreCancel" />
    </el-dialog>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, defineProps, defineEmits } from 'vue'
import { useVoucher } from '@/composables/admin/useVoucher'
import ConfirmationDialog from '@/components/ConfirmationDialog.vue'
import type { TVoucher } from '@/interfaces/voucher';

const props = defineProps<{ visible: boolean }>()
const emit = defineEmits(['update:visible'])

const localVisible = ref(props.visible) // Biến cục bộ để quản lý trạng thái hiển thị của dialog
const deletedVouchers = ref<number | string[]>([]) // Danh sách voucher đã xóa mềm
const selectedVoucher = ref<TVoucher | null>(null)
const showRestoreConfirm = ref(false)
const restoreConfirmMessage = ref('')

// Đồng bộ localVisible với prop visible
watch(() => props.visible, (newVal) => {
    localVisible.value = newVal
})

// Gọi API để lấy danh sách voucher đã xóa mềm
onMounted(async () => {
    deletedVouchers.value = await fetchDeletedVouchers()
})

const { fetchDeletedVouchers, restoreVoucher } = useVoucher()

// Xác nhận khôi phục voucher
const confirmRestore = (voucher: TVoucher) => {
    selectedVoucher.value = voucher
    restoreConfirmMessage.value = `Bạn có chắc chắn muốn khôi phục voucher ${voucher.code}?`
    showRestoreConfirm.value = true
}

// Thực hiện khôi phục voucher khi xác nhận
const handleRestoreConfirm = async () => {
    if (selectedVoucher.value) {
        await restoreVoucher(selectedVoucher.value.code)
        deletedVouchers.value = await fetchDeletedVouchers()
    }
    showRestoreConfirm.value = false
}

// Đóng dialog xác nhận khi hủy
const handleRestoreCancel = () => {
    showRestoreConfirm.value = false
}

// Đóng dialog chính và phát sự kiện cập nhật cho component cha
const onClose = () => {
    emit('update:visible', false)
}
</script>
