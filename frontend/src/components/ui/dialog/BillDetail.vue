<template>
    <div>
        <h2 class="text-lg font-bold">Chi tiết hóa đơn</h2>
        <div class="mt-4">
            <p><strong>Mã đơn hàng:</strong> {{ data.order_code }}</p>
            <p><strong>Tổng giá:</strong> {{ data.total_price }} {{ data.currency }}</p>
            <p><strong>Phương thức thanh toán:</strong> {{ data.payment_method }}</p>
            <p><strong>Trạng thái thanh toán:</strong> {{ data.payment_status }}</p>
            <p><strong>Mã thanh toán:</strong> {{ data.payment_code }}</p>
            <p><strong>Ngày tạo:</strong> {{ data.created_at }}</p>
        </div>

        <h3 class="mt-6 font-bold">Các mặt hàng trong đơn hàng</h3>
        <table class="min-w-full mt-2">
            <thead>
                <tr>
                    <th class="px-6 py-2 text-left">ID</th>
                    <th class="px-6 py-2 text-left">Khóa học ID</th>
                    <th class="px-6 py-2 text-left">Giá</th>
                    <th class="px-6 py-2 text-left">Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in data.order_items" :key="item.id">
                    <td class="px-6 py-4">{{ item.id }}</td>
                    <td class="px-6 py-4">{{ item.course_id }}</td>
                    <td class="px-6 py-4">{{ item.price }}</td>
                    <td class="px-6 py-4">{{ item.status }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';
import type { TBill } from '@/interfaces/bill.interface';

defineProps<{ data: TBill }>();

</script>
