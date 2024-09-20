<template>
    <div>
        <UserSidebar />
        <main>
            <router-view />
            <!-- <slot></slot> -->
        </main>
    </div>
</template>

<script setup lang="ts">

import UserSidebar from '../components/user/UserSidebar.vue';

</script>

<style scoped></style>