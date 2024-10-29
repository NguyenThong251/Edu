<template>

    <tr>
        <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ voucher_id }}</td> -->
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ order_code }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ total_price }}{{ currency }}</td>
        <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"></td> -->
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ payment_method }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ payment_status }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <span class="block max-w-60 truncate">

                {{ payment_code }} </span>
        </td>

        <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ status }}</td> -->
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ created_at }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex gap-3">

                <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                <EyeIcon class="cursor-pointer h-5 w-5 text-indigo-600" />
            </div>

        </td>
    </tr>

</template>

<script setup lang="ts">
import type { TBill } from '@/interfaces/bill.interface';
import { defineProps } from 'vue';
import { EyeIcon } from "@heroicons/vue/24/outline";


defineProps<TBill>()
</script>
