<script setup lang="ts">
import {ref, defineProps } from 'vue';
import { Ckeditor } from '@ckeditor/ckeditor5-vue';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import type { CkeditorGroupProps } from '@/interfaces';
const props = defineProps<CkeditorGroupProps>();

const editor = ClassicEditor;
const editorData = ref(props.value || ''); // Dữ liệu khởi tạo của CKEditor

const onChange = (event: any) => {
  console.log(event);
};

// Cấu hình editor với plugin
const editorConfig = {
  toolbar: [
    'undo', 'redo', '|',
    'heading', '|',
    'bold', 'italic', 'link', '|',
    'imageUpload', // Thêm plugin Image Upload
    'bulletedList', 'numberedList', 'blockQuote'
  ],
  // Thêm các tùy chọn khác nếu cần
};

</script>

<template>
    

  <div class="mt-3 " :class="customsClass">
    <label 
    :for="inputId" 
    class="label-input"
    :class="customsClassChild"
    >
      {{ label }}
    </label>
    <div id="editor"></div>
    <ckeditor
      :editor="editor"
      @change="onChange"
      :v-model="modelValue"
      class="w-full !h-[400px]"
      :config="editorConfig"
      rows="4"
      :class="customsClassChild2"
    >
  </ckeditor>
  </div>
</template>

<style>
.ck-editor__editable {
  min-height: 150px; 
  width: 100%;
  background-color: #f4f4f4; /* Màu nền cho khung chỉnh sửa */
  border: 1px solid #ccc; /* Viền cho khung chỉnh sửa */
  border-radius: 0rem 0rem 0.375rem 0.375rem !important;
}

.ck-toolbar {
  background-color: #7c7b7b; /* Màu nền cho thanh công cụ */
  border-bottom: 1px solid #ccc; /* Viền dưới thanh công cụ */
}
.ck-editor {
  flex-basis: 83.333333%; 
}

</style>