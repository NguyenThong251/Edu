<template>
    <div>
        <!-- <UserHeader /> -->
        <main>
            <router-view />
            <!-- <slot></slot> -->
        </main>
        <UserFooter />
    </div>
</template>
<script lang="ts" setup>
import UserHeader from '@/components/user/UserHeader.vue';
import UserFooter from '@/components/user/UserFooter.vue';

</script>