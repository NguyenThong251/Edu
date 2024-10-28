<script setup lang="ts">
import SiderArea from '@/components/admin/Sidebar/SidebarArea.vue';
import HeaderArea from '@/components/admin/Header/HeaderArea.vue';
</script>
<template>
  <!-- ========= Bắt đầu trang============ -->
  <div class="container-admin" >
    <div class="p-3 flex gap-3 w-full h-screen">
      <!-- ========= Bắt đầu Siderbar============ -->
      <SiderArea/>
      <!-- ========= Kết thúc Siderbar============ -->

      <!-- ========= Nội dung các trang ============ -->
      <main class="main-admin text-black dark:text-white">
        <HeaderArea/> 
        <router-view></router-view>
      </main>
      <!-- ========= Kết thúc nội dung các trang ============ -->
    </div>
  </div>
  <!-- ========= Kết thúc trang============ -->
</template>

