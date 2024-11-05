<template>
    <div class="flex flex-col gap-3">
        <h3 class="text-lg">Áp dụng mã giảm giá</h3>
        <div class="border-2 border-gray-900 flex justify-between">
            <input type="text" v-model="voucher" class="px-5 w-[12rem] py-2 focus-visible:outline-none"
                placeholder="Nhập phiếu giảm giá" />
            <button @click="applyVoucherCode" class="h-full bg-gray-900 px-5 py-2 text-white">
                Áp dụng
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useVoucherStore } from '@/store/voucher';
import { computed, ref } from 'vue';
const voucherStore = useVoucherStore();
const voucher = ref('');
const applyVoucherCode = () => {
    const dd = voucherStore.applyVoucher(voucher.value);
    console.log(dd)
}
</script>
