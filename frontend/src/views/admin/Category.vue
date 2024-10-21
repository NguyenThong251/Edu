<script setup lang="ts">
import HeaderNavbar from '@/components/admin/Headernavbar/HeaderNavbar.vue'
import { useSidebarStore } from '@/store/sidebar';
import CardCategory from '@/components/admin/Card/CardCategory.vue';
const sidebarStore = useSidebarStore();
</script>
<template>
  <div class="p-4">
    <HeaderNavbar
    :namePage="sidebarStore.page"
    />
  </div>
  <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
    <CardCategory/>
  </div>
</template>
