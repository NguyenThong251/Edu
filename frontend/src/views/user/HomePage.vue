<template>

    <div>
        <UserHero />
    </div>
    <el-button plain @click="open1"> Success </el-button>
    <el-button plain @click="open2"> Warning </el-button>
    <el-button plain @click="open3"> Info </el-button>
    <el-button plain @click="open4"> Error </el-button>
    <AdjustmentsVerticalIcon class="h-6 w-6 text-gray-500" />
    <div class="flex">
        <Button variant="primary" @click="handleClick">Submit</Button>
        <Button variant="default" @click="handleClick">Register</Button>
    </div>
    <button class="font-medium"></button>
</template>

<script lang="ts" setup>
import { ElNotification } from 'element-plus'
import { AdjustmentsVerticalIcon } from "@heroicons/vue/24/outline";
import UserHero from '@/components/user/UserHero.vue';
import { Button } from '@/components/ui/index'

//btn test
const handleClick = () => {
    console.log('Button clicked');
}
// import ClientLayout from '@/layouts/ClientLayout.vue';
const open1 = () => {
    ElNotification({
        title: 'Success',
        message: 'This is a success message',
        type: 'success',
    })
}

const open2 = () => {
    ElNotification({
        title: 'Warning',
        message: 'This is a warning message',
        type: 'warning',
    })
}

const open3 = () => {
    ElNotification({
        title: 'Info',
        message: 'This is an info message',
        type: 'info',
    })
}

const open4 = () => {
    ElNotification({
        title: 'Error',
        message: 'This is an error message',
        type: 'error',
    })
}
</script>