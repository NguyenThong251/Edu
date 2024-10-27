<script setup lang="ts">
import ButtonPrimary from '@/components/admin/Button/ButtonPrimary.vue';
import CkeditorGroup from '@/components/admin/Dialog/CkeditorGroup.vue';
import DescriptionGroup from '@/components/admin/Dialog/DescriptionGroup.vue';
import InputGroup from '@/components/admin/Dialog/InputGroup.vue';
import RadioGroup from '@/components/admin/Dialog/RadioGroup.vue';
import SelectGroup from '@/components/admin/Dialog/SelectGroup.vue';
import UploadGroup from '@/components/admin/Dialog/UploadGroup.vue';
import HeaderNavbar from '@/components/admin/Headernavbar/HeaderNavbar.vue';
import { useSidebarStore } from '@/store/sidebar';
const sidebarStore = useSidebarStore()

import { ref } from 'vue'

const paid = ref('1')
const radio2 = ref('1')

</script>
<template>
  <div class="p-4">
    <HeaderNavbar
    :namePage="sidebarStore.page"
    />
  </div>
  <div class="p-4 py-2">
    <div class="background-table ">
      <div class="grid grid-cols-1 md:grid-cols-2 px-10 py-3 gap-10 ">
        <div class="">
          <InputGroup
          label="Tiêu đề"
          required="*"
          inputPlaceHoder="Nhập tiêu đề khoá học"
          />
          <DescriptionGroup
          label="Mô tả ngắn"
          inputPlaceHoder="Nhập mô tả..."
          />
          <CkeditorGroup
          label="Nội dung"
          />
          <RadioGroup
          label="Chọn"
          required="*"
          >
          <div class="mb-2 ml-4">
            <el-radio-group class="grid" v-model="radio2">
              <el-radio value ="1" >Kích hoạt</el-radio>
              <el-radio value ="2" >Riêng tư</el-radio>
              <el-radio value ="3" >Sắp tới</el-radio>
              <el-radio value ="4" >Đang sử lý</el-radio>
              <el-radio value ="5" >Lưu bản nháp</el-radio>
            </el-radio-group>
          </div>
          </RadioGroup>
        </div>
        <div class="">
          <SelectGroup
          inputPlaceHoder="Chọn danh mục cho khoá học"
          required="*"
          label="Danh mục"
          />
          <SelectGroup
          inputPlaceHoder="Chọn cấp độ cho khoá học"
          required="*"
          label="Cấp độ khoá học"
          />
          <SelectGroup
          inputPlaceHoder="Chọn ngôn ngữ cho khoá học"
          required="*"
          label="Ngôn ngữ"
          />
          <RadioGroup
          label="Loại giá"
          >
          <el-radio-group class="grid" v-model="paid">
              <el-radio value ="1">Có phí</el-radio>
              <el-radio value ="0">Miễn phí</el-radio>
          </el-radio-group>
          </RadioGroup>
          <template
           :class="{'block': paid == 1, 'hidden': paid == 0}"
           class="transition-opacity translate-y-2  duration-500 ease-in-out"
          >
            <InputGroup
          label="Giá (đơn vị vnđ)"
          required="*"
          inputPlaceHoder="Nhập giá của khoá học"
          />
          <div class="mt-1">
            <el-checkbox v-model="checked2" label="Click nếu khoá học này có sử dụng mã giảm giá" size="large" />
          </div>
          <InputGroup
          label="Giá khoá học đã giảm (đơn vị vnđ)"
          required="*"
          inputPlaceHoder="Nhập giá của khoá học đã giảm"
          />
        </template>
        <UploadGroup
          label="Tải lên hình ảnh thumbnail"
          inputId="upload"
          />
        </div>
      </div>
      <div class="flex justify-end p-10">
        <ButtonPrimary
        title="Xác nhận"
        />
      </div>
    </div>
  </div>
</template>

