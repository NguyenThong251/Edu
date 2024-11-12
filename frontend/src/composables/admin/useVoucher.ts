import { ref } from 'vue'
import { useVoucherStore } from '@/store/voucher'
import dayjs from 'dayjs'
import type { TVoucher } from '@/interfaces/voucher'

export function useVoucher() {
  const voucherStore = useVoucherStore()
  const voucherForm = ref<TVoucher>({
    code: '',
    description: '',
    discount_type: 'percent',
    discount_value: 0,
    usage_limit: 0,
    expires_at: '',
    min_order_value: 0,
    max_discount_value: 0,
    status: 'active'
  })

  const fetchVouchers = async () => {
    await voucherStore.fetchVouchers()
  }

  const handleSubmitCreate = async () => {
    voucherForm.value.expires_at = dayjs(voucherForm.value.expires_at).format('YYYY-MM-DD')
    await voucherStore.createVoucher(voucherForm.value)
  }

  const handleSubmitUpdate = async () => {
    voucherForm.value.expires_at = dayjs(voucherForm.value.expires_at).format('YYYY-MM-DD')
    await voucherStore.updateVoucher(voucherForm.value)
  }
  const deleteVoucher = async (code: number | string) => {
    await voucherStore.deleteVoucher(code)
    await voucherStore.fetchVouchers()
  }
  const restoreVoucher = async (code: number | string) => {
    await voucherStore.restoreVoucher(code)
  }
  const fetchDeletedVouchers = async () => {
    await voucherStore.fetchDeletedVouchers()
  }
  return {
    voucherForm,
    fetchVouchers,
    handleSubmitCreate,
    handleSubmitUpdate,
    deleteVoucher,
    restoreVoucher,
    fetchDeletedVouchers
  }
}
