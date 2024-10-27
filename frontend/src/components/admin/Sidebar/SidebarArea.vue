<script setup lang="ts">
import logo from '@/assets/images/logo2.svg'
import logoMinimal from '@/assets/images/minimal-logo.svg'
import SidebarItems from './SidebarItems.vue';
import { computed, ref } from 'vue';
import {WindowIcon, HomeIcon, SquaresPlusIcon, ArchiveBoxIcon, BanknotesIcon, UserGroupIcon, ChatBubbleLeftRightIcon, EnvelopeIcon, DocumentTextIcon} from '@heroicons/vue/24/outline';
import { Cog8ToothIcon, UserCircleIcon } from '@heroicons/vue/20/solid';
import type { MenuGroup } from '@/interfaces/admin.interface';

import { useSidebarStore } from '@/store/sidebar';

const sidebarStore = useSidebarStore();
const currentLogo = computed(() => {
  return sidebarStore.isSidebarOpen ? logoMinimal : logo;
});
const sidebarClass = computed(() => {
  return sidebarStore.isSidebarOpen ? 'w-[90px]' : 'w-[290px]';
});

const menuGroups = ref<MenuGroup[]>([
  {   
    name: 'MENU',
    menuItems: [
      {
        icon: HomeIcon,
        label: 'Bảng điều khiển',
        route: '/admin/dashboard'
      },
      {
        icon: SquaresPlusIcon,
        label: 'Danh mục',
        route: '/admin/category'
      },
      {
        icon: ArchiveBoxIcon,
        label: 'Khoá học',
        route: '#',
        children: [
          {
            label: 'Quản lý khoá học',
            route: '/admin/course/manager-course'
          },
          {
            label: 'Thêm khoá học mới',
            route: '/admin/course/add-course'
          },
          {
            label: 'Phiếu giảm giá',
            route: '/admin/course/manager-coupon'
          },
        ]
      },
      {
        icon: BanknotesIcon,
        label: 'Báo cáo doanh thu',
        route: '#',
        children: [
          {
            label: 'Doanh thu admin',
            route: '/admin/reportpayment/admin-revenua'
          },
          {
            label: 'Doanh thu giáo viên',
            route: '/admin/reportpayment/teacher-revenua'
          },
          {
            label: 'Lịch sử mua hàng',
            route: '/admin/reportpayment/history'
          },
        ]
      },
      {
        icon: UserGroupIcon,
        label: 'Người dùng',
        route: '#',
        children: [
          {
            label: 'Admin',
            route: '#',
            children: [
              {
                label: 'Quản lý admin',
                route: '/admin/user/user-admin/user-manager-admin',
              },
              {
                label: 'Thêm admin',
                route: '/admin/user/user-admin/user-add-admin',
              },
            ]
          },
          {
            label: 'Giáo viên',
            route: '#',
            children: [
              {
                label: 'Quản lý giáo viên',
                route: '/admin/user/user-teacher/user-manager-teacher',
              },
              {
                label: 'Thêm giáo viên',
                route: '/admin/user/user-teacher/user-add-teacher',
              },
              {
                label: 'Thanh toán',
                route: '/admin/user/user-teacher/payout',
              },
              {
                label: 'Cài đặt thanh toán',
                route: '/admin/user/user-teacher/payout-settings',
              },
              {
                label: 'Phê duyệt',
                route: '/admin/user/user-teacher/accept',
              },
            ]
          },
          {
            label: 'Học viên',
            route: '#',
            children: [
              {
                label: 'Quản lý học viên',
                route: '/admin/user/user-student/user-manager-student',
              },
              {
                label: 'Thêm học viên',
                route: '/admin/user/user-student/user-add-student',
              },
            ]
          },
        ]
      },
      {
        icon: ChatBubbleLeftRightIcon,
        label: 'Tin nhắn',
        route: '/admin/message',
      },
      {
        icon: EnvelopeIcon,
        label: 'Tin tức',
        route: '/admin/newletter',
      },
      {
        icon: DocumentTextIcon,
        label: 'Bài viết',
        route: '#',
        children: [
          {
            label: 'Quản lý bài viết',
            route: '/admin/blog/manager-blog'
          },
          {
            label: 'Bài viết đang xử lý',
            route: '/admin/blog/blog-pedding'
          },
          {
            label: 'Doanh mục bài viết',
            route: '/admin/blog/blog-category'
          },
          {
            label: 'Cài đặt bài viết',
            route: '/admin/blog/blog-settings'
          },
        ]
      },
    ]
  },
  {
    name: 'cài đặt',
    menuItems: [
      {
        icon: Cog8ToothIcon,
        label: 'Cài đặt hệ thống',
        route: '#',
        children: [
          {
            label: 'Cài đặt hệ thống',
            route: '/admin/system-settings'
          },
          {
            label: 'Cài đặt website',
            route: '/admin/system-settings/website-settings'
          },
          {
            label: 'Cài đặt thanh toán',
            route: '/admin/system-settings/payment-settings'
          },
          {
            label: 'Cài đặt ngôn ngữ',
            route: '/admin/system-settings/language-settings'
          },
          {
            label: 'Cài đặt SMTP',
            route: '/admin/system-settings/smtp-settings'
          },
          {
            label: 'Cài đặt chứng nhận',
            route: '/admin/system-settings/certificate-settings'
          },
        ]
      },
      {
        icon: UserCircleIcon,
        label: 'Thông tin cá nhân',
        route: '/admin/profile-settings'
      },
    ]

  }
])
</script>

<template>
  <aside class="shadow-xl z-20"
  :class="{
    'ssm:hidden': !sidebarStore.isSidebarOpen,
  }"
  >
    <div class="w-[290px] h-full relative dark:bg-dark-sidebar bg-primary-sidebar rounded-[16px] shadow-sidebar transform duration-100"
    :class="sidebarClass "
    >
      <!-- SIDEBAR HEADER -->
      <router-link to="/admin"> 
        <img 
        class="pl-5 pt-4 " 
        :class="{'p-4':currentLogo ==logoMinimal}" 
        :src="currentLogo" 
        alt="">
        <!-- <WindowIcon 
            class="hidden sm:block w-6 h-6 dark:hover:text-gray-200 text-gray-400"
        /> -->
      </router-link>
      <!-- note 1: Phím show hide sidebar -->
      <!-- END SIDEBAR HEADER -->
       
      <div 
      class="text-white gap-1.5"
      :class="{'p-4':sidebarStore.isSidebarOpen, 'p-6':!sidebarStore.isSidebarOpen}"
      >
        <!-- Sidebar Menu -->
         <nav>
           <template v-for="menuGroup in menuGroups" :key="menuGroup.name">
            <div>
              <h3 
              class="title text-base pl-4 pb-3 uppercase pt-6"
              :class="{'opacity-0':sidebarStore.isSidebarOpen}"
              >{{menuGroup.name}}</h3>
              <ul class="gap-1 flex flex-col">
                <SidebarItems
                 v-for="(menuItem, index) in menuGroup.menuItems"
                 :item="menuItem"
                 :key="index"
                 :index="index"
                />
              </ul>
            </div>
           </template>
         </nav>
         <!-- End Sidebar Menu -->
          <div class="pt-7 pb-5 text-center text-sm text-zinc-400">
            Create by Edunity 2024
          </div>
      </div>
    </div>
  </aside>
</template>

