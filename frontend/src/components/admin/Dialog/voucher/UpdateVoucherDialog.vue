<template>
    <el-dialog align-center title="Cập Nhật Voucher" class="z-20" v-model:visible="localVisible" width="60%"
        @close="onClose">
        <form @submit.prevent="handleSubmit">
            <div class="mb-4">
                <label class="block text-sm font-medium">Mã Voucher</label>
                <el-input v-model="voucherForm.code" placeholder="Nhập mã voucher" />
            </div>
            <!-- Các trường form khác như trong template của bạn -->
            <div class="flex justify-end">
                <el-button @click="onClose">Hủy</el-button>
                <el-button type="primary" native-type="submit">Lưu</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script setup lang="ts">
import { ref, defineProps, defineEmits, watch } from 'vue'
import { useVoucher } from '@/composables/admin/useVoucher'

// Define props và emits
const props = defineProps<{ visible: boolean, voucherData: any }>()
const emit = defineEmits(['update:visible'])

// State cục bộ để quản lý trạng thái hiển thị
const localVisible = ref(props.visible)
const { voucherForm, handleSubmitUpdate } = useVoucher()

// Đồng bộ hóa voucherForm khi dữ liệu voucherData thay đổi
watch(() => props.voucherData, (newData) => {
    voucherForm.value = { ...newData }
})

// Đồng bộ hóa visible giữa prop và localVisible
watch(() => props.visible, (newVal) => {
    localVisible.value = newVal
})

// Hàm xử lý đóng dialog và phát sự kiện để component cha cập nhật
const onClose = () => {
    emit('update:visible', false)
}

const handleSubmit = async () => {
    await handleSubmitUpdate()
    emit('update:visible', false) // Đóng dialog sau khi lưu
}
</script>
