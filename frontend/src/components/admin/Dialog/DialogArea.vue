
<script setup lang="ts">
import ButtonSecondary from '@/components/admin/Button/ButtonSecondary.vue';
import SubmitButtonPrimary from '@/components/admin/Button/SubmitButtonPrimary.vue';
import { ref, defineProps, toRefs } from 'vue';
import { ElDialog } from 'element-plus'

const props = defineProps<{
  dialogVisible: boolean;
  title: string;
  submitForm: (data: opject) => Promise<void>;
}>();

//Gửi dữ liệu đến sever
// const submitForm = () => {
//   console.log('Form submitted with data:', formAddNewData.value); // Kiểm tra dữ liệu

//   // Bạn có thể gửi yêu cầu POST tới API ở đây, ví dụ sử dụng fetch:
//   fetch('/api/your-endpoint', {
//     method: 'POST',
//     headers: {
//       'Content-Type': 'application/json',
//     }, 
//     body: JSON.stringify(formAddNewData.value),
//   })
//     .then(response => response.json())
//     .then(data => {
//       console.log('Success:', data);
//       dialogVisible.value = false; // Đóng dialog sau khi thành công
//       // Reset form nếu cần
//       formAddNewData.value = { img: '', icon: '', title: '', keyword: '', description: '', children: [] };
//     })
//     .catch((error) => {
//       console.error('Error:', error);
//     });
// };

</script>
<template>
  <el-dialog 
  v-model="props.dialogVisible"  
  width="500"
  >
    <template #title >
      <span class="custom-dialog-title text-lg font-bold">{{ props.title }}</span>
    </template>
    <form @submit.prevent="submitForm">
      <div class="border-t-2 border-primary-sidebar dark:border-bg-primary">
        <slot>
        </slot>
      </div>
        <div class="flex mt-5">
          <SubmitButtonPrimary
          title="Xác nhận"
          />
        </div>
    </form>
    <!-- <template #footer>
      <div class="dialog-footer pt-5 border-t-2 border-primary-sidebar dark:border-bg-primary flex justify-end">
        <ButtonSecondary
          link="#"
          title="Đóng"
        />
      </div>
    </template> -->
  </el-dialog>

</template>

