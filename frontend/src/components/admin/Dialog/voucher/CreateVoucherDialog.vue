<template>
    <el-dialog align-center title="Tạo Voucher" class="z-20" v-model:visible="localVisible" width="60%"
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

const props = defineProps<{ visible: boolean }>()
const emit = defineEmits(['update:visible'])

const localVisible = ref(props.visible)
const { voucherForm, handleSubmitCreate } = useVoucher()

watch(() => props.visible, (newVal) => {
    localVisible.value = newVal
})

const onClose = () => {
    emit('update:visible', false)
}

const handleSubmit = async () => {
    await handleSubmitCreate()
    emit('update:visible', false) // Đóng dialog sau khi lưu
}
</script>
