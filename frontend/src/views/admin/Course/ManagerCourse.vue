<script setup lang="ts">
import ButtonPrimary from '@/components/admin/Button/ButtonPrimary.vue';
import ButtonSecondary from '@/components/admin/Button/ButtonSecondary.vue';
import InputSearch from '@/components/admin/Button/InputSearch.vue';
import HeaderNavbar from '@/components/admin/Headernavbar/HeaderNavbar.vue';
import RenderOptions from '@/components/admin/Table/RenderOptions.vue';
import { useSidebarStore } from '@/store/sidebar';
import { ArrowUpOnSquareIcon, DocumentPlusIcon } from '@heroicons/vue/24/outline';
import { EllipsisVerticalIcon} from '@heroicons/vue/24/solid';
import { reactive, ref } from 'vue';
import TableLite from 'vue3-table-lite/ts';

// Khai báo store
const sidebarStore = useSidebarStore();

// Dialog
const dialogVisible = ref(false);
const openDialog = () => {
  dialogVisible.value = true;
};
// Hàm toggle menu
const toggleMenu = (id: string) => {
  const menu = document.getElementById(`menu-${id}`);
  if (menu) {
    menu.classList.toggle('hidden');
  }
};
// Cấu hình bảng
const table = reactive({
  isLoading: false,
  columns: [
    { label: "ID", field: "id", width: "5%", sortable: true },
    { label: "Title", field: "title", width: "30%", sortable: true },
    { label: "Instructor", field: "instructor", width: "15%", sortable: true },
    { label: "Category", field: "category", width: "10%", sortable: true },
    { label: "Lessons", field: "lessons", width: "10%", sortable: true },
    { label: "Sections", field: "sections", width: "10%", sortable: true },
    { label: "Enrolled Students", field: "enrolled", width: "10%", sortable: true },
    { label: "Status", field: "status", width: "10%", sortable: true },
    { label: "Price", field: "price", width: "10%", sortable: true },
    {
        label: "Options",
        field: "options",
        width: "20%",
      },

  ],
  messages: {
    pagingInfo: "Hiển thị {0} - {1} trên {2}",
    pageSizeChangeLabel: "Số hàng:",
    gotoPageLabel: "Số trang hiện tại:",
    noDataAvailable: "Không có dữ liệu",
  },
  rows: [] as Array<any>,
  totalRecordCount: 0,
  sortable: { order: "id", sort: "asc" },
});

// Dữ liệu giả cho khóa học
const sampleCourses = (offset: number, limit: number) => {
  const courses = [
    {
      id: 1,
      title: "Responsive Web Design Essentials - HTML5 CSS Bootstrap",
      instructor: "John Doe",
      category: "Responsive Design",
      lessons: 13,
      sections: 4,
      enrolled: 3,
      status: "Active",
      price: "25 $ 30 $",
    },
    {
      id: 2,
      title: "Build Websites from Scratch with HTML & CSS",
      instructor: "James Mariyati",
      category: "HTML & CSS",
      lessons: 7,
      sections: 4,
      enrolled: 1,
      status: "Active",
      price: "Free",
    },
    // Thêm các khóa học khác...
    {
      id: 20,
      title: "How to Use Lighting Design to Transform your Home",
      instructor: "John Doe",
      category: "Lighting Design",
      lessons: 13,
      sections: 4,
      enrolled: 0,
      status: "Active",
      price: "66 $",
    },
  ];

  return courses.slice(offset, offset + limit);
};

// Hàm tìm kiếm
const doSearch = (offset: number, limit: number, order: string, sort: string) => {
  table.isLoading = true;
  setTimeout(() => {
    if (offset >= 10 || limit >= 20) {
      limit = 20;
    }
    table.rows = sort === "asc" ? sampleCourses(offset, limit) : sampleCourses(offset, limit);
    table.totalRecordCount = 20; // Giả lập tổng số bản ghi
    table.sortable.order = order; 
    table.sortable.sort = sort; 
    table.isLoading = false; 
  }, 600);
};

// Lần đầu tiên lấy dữ liệu
doSearch(0, 10, "id", "asc");

</script>

<template>
  <div class="p-4">
    <HeaderNavbar :namePage="sidebarStore.page">
      <ButtonPrimary :icon="DocumentPlusIcon" link="#" title="Thêm khoá học" />
    </HeaderNavbar>
    <div class="px-4 py-2">
      <div class="background-table">
        <div class="lg:flex justify-between pb-2">
          <div class="p-3 flex gap-2">
            <ButtonSecondary :icon="ArrowUpOnSquareIcon" link="#" title="Xuất" customStyle="flex-row-reverse" />
          </div>
          <div class="p-3 flex gap-2">
            <InputSearch title="Lọc" inputPlaceHoder="10/01/2024 - 10/31/2024" modelValue="dateRange" @update:modelValue="updateDateRange" />
          </div>
        </div>
        <div class="py-3 px-2">
          <table-lite
            :max-height="300"
            :is-loading="table.isLoading"
            :columns="table.columns"
            :rows="table.rows"
            :total="table.totalRecordCount"
            :sortable="table.sortable"
            :messages="table.messages"
            @do-search="doSearch"
            @is-finished="table.isLoading = false"
          >
          <template v-slot:options="{ row }">
              <RenderOptions :row="row" />
            </template>
        </table-lite>
        </div>
      </div>
    </div>
  </div>
</template>

