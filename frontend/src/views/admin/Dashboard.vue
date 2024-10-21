<script setup lang="ts">
import CardItems from '@/components/admin/Card/CardItems.vue';
import ChartOne from '@/components/admin/Chart/ChartOne.vue';
</script>
<template>
  <div class="p-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4  gap-7">
    <CardItems/>
  </div>
  <div class="p-4  grid grid-cols-12">
    <ChartOne/>
  </div>
</template>

