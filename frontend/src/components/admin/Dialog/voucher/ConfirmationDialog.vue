<template>
    <el-dialog title="Xác nhận" v-model:visible="localVisible" width="30%" @close="onClose">
        <div>{{ message }}</div>
        <div class="flex justify-end mt-4">
            <el-button @click="onCancel">Hủy</el-button>
            <el-button type="primary" @click="onConfirm">Xác nhận</el-button>
        </div>
    </el-dialog>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, watch, ref } from 'vue'

const props = defineProps<{ visible: boolean, message: string }>()
const emit = defineEmits(['confirm', 'cancel', 'update:visible'])

const localVisible = ref(props.visible)

watch(() => props.visible, (newVal) => {
    localVisible.value = newVal
})

const onClose = () => {
    emit('update:visible', false)
}

const onConfirm = () => {
    emit('confirm')
    emit('update:visible', false) // Đóng dialog sau khi xác nhận
}

const onCancel = () => {
    emit('cancel')
    emit('update:visible', false) // Đóng dialog khi hủy
}
</script>
