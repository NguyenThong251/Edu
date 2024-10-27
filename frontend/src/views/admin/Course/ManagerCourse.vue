<script setup lang="ts">
import ButtonPrimary from '@/components/admin/Button/ButtonPrimary.vue';
import ButtonSecondary from '@/components/admin/Button/ButtonSecondary.vue';
import InputSearch from '@/components/admin/Button/InputSearch.vue';
import DialogView from '@/components/admin/Dialog/DialogView.vue';
import HeaderNavbar from '@/components/admin/Headernavbar/HeaderNavbar.vue';
import { useSidebarStore } from '@/store/sidebar';
import { ArrowUpOnSquareIcon, FunnelIcon } from '@heroicons/vue/24/outline';
import { DocumentPlusIcon } from '@heroicons/vue/24/solid';
const sidebarStore = useSidebarStore()

import { ref } from 'vue'
//dialog
const dialogVisible = ref(false);

const openDialog = () => {
  dialogVisible.value = true;
};
//end dialog

const tableData: User[] = [
  {
    date: '2016-05-03',
    name: 'Tom',
    address: 'No. 189, Grove St, Los Angeles',
    tag: 'Home',
  },
  {
    date: '2016-05-02',
    name: 'Tom',
    address: 'No. 189, Grove St, Los Angeles',
    tag: 'Office',
  },
  {
    date: '2016-05-04',
    name: 'Tom',
    address: 'No. 189, Grove St, Los Angeles',
    tag: 'Home',
  },
  {
    date: '2016-05-01',
    name: 'Tom',
    address: 'No. 189, Grove St, Los Angeles',
    tag: 'Office',
  },
  {
    date: '2016-05-03',
    name: 'Tom',
    address: 'No. 189, Grove St, Los Angeles',
    tag: 'Home',
  },
  {
    date: '2016-05-02',
    name: 'Tom',
    address: 'No. 189, Grove St, Los Angeles',
    tag: 'Office',
  },
  {
    date: '2016-05-04',
    name: 'Tom',
    address: 'No. 189, Grove St, Los Angeles',
    tag: 'Home',
  },
  {
    date: '2016-05-01',
    name: 'Tom',
    address: 'No. 189, Grove St, Los Angeles',
    tag: 'Office',
  },
  {
    date: '2016-05-03',
    name: 'Tom',
    address: 'No. 189, Grove St, Los Angeles',
    tag: 'Home',
  },
  {
    date: '2016-05-02',
    name: 'Tom',
    address: 'No. 189, Grove St, Los Angeles',
    tag: 'Office',
  },
  {
    date: '2016-05-04',
    name: 'Tom',
    address: 'No. 189, Grove St, Los Angeles',
    tag: 'Home',
  },
  {
    date: '2016-05-01',
    name: 'Tom',
    address: 'No. 189, Grove St, Los Angeles',
    tag: 'Office',
  },
  {
    date: '2016-05-03',
    name: 'Tom',
    address: 'No. 189, Grove St, Los Angeles',
    tag: 'Home',
  },
  {
    date: '2016-05-02',
    name: 'Tom',
    address: 'No. 189, Grove St, Los Angeles',
    tag: 'Office',
  },
  {
    date: '2016-05-04',
    name: 'Tom',
    address: 'No. 189, Grove St, Los Angeles',
    tag: 'Home',
  },
  {
    date: '2016-05-01',
    name: 'Tom',
    address: 'No. 189, Grove St, Los Angeles',
    tag: 'Office',
  },
]
</script>
<template>
  <div class="p-4">
    <HeaderNavbar :namePage="sidebarStore.page" >
      <ButtonPrimary
        :icon="DocumentPlusIcon"
        link="#"
        title="Thêm khoá học"
        />
    </HeaderNavbar>
  </div>
  <div class="px-4 py-2">
    <div class="background-table">
      <div class="lg:flex justify-between pb-2">
        <div class="p-3 flex gap-2">
          <ButtonSecondary
          :icon="ArrowUpOnSquareIcon"
          link="#"
          title="Xuất"
          customStyle="flex-row-reverse"
          />
          <ButtonSecondary
            :icon="FunnelIcon"
            link="#"
            title="Lọc"
          />
        </div>
        <div class="p-3 flex gap-2">
          <InputSearch
          title="Tìm kiếm"
          inputPlaceHoder="Nhập để tìm kiếm..."
          />
        </div>
      </div>
      <div class="py-3">
        <div class="max-w-full overflow-x-auto">
          <el-table class="!dark:el-table" ref="tableRef" row-key="date" :data="tableData" style="width: 100%">
            <el-table-column
              label="Stt"
              width="50"
              sortable
              show-overflow-tooltip
            />
            <el-table-column prop="name" label="Name" width="180" />
            <el-table-column prop="address" label="Address" :formatter="formatter" />
            <el-table-column
              prop="tag"
              label="Tag"
              width="100"
              :filters="[
                { text: 'Home', value: 'Home' },
                { text: 'Office', value: 'Office' },
              ]"
              :filter-method="filterTag"
              filter-placement="bottom-end"
            >
              <template #default="scope">
                <el-tag
                  :type="scope.row.tag === 'Home' ? 'primary' : 'success'"
                  disable-transitions
                  >{{ scope.row.tag }}</el-tag
                >
              </template>
            </el-table-column>
          </el-table>
        </div>
      </div>
    </div>
    <div class="demo-pagination-block mt-5 ">
      <el-pagination 
        class="flex justify-between"
        v-model:current-page="currentPage3"
        v-model:page-size="pageSize3"
        :size="size"
        :disabled="disabled"
        :background="background"
        layout="prev, pager, next, jumper"
        :total="1000"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>
  </div>
</template>

