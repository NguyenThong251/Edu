import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/axiosConfig'
import type { AuthState, TUserAuth } from '@/interfaces'
import Cookies from 'js-cookie'
import { useRouter } from 'vue-router'
import { ElNotification } from 'element-plus'
import type { TVoucher, TVoucherStoreState } from '@/interfaces/voucher'
export const useVoucherStore = defineStore('voucher', () => {
  const state = ref<TVoucherStoreState>({
    vouchers: [],
    deletedVouchers: [],
    voucherDetails: null,
    appliedVoucher: '',
    error: null
  })
  //  Get toàn bộ Voucher
  const fetchVouchers = async () => {
    try {
      const response = await api.get('/auth/vouchers/')
      state.value.vouchers = response.data
    } catch (error) {
      state.value.error = 'Không thể tải danh sách vouchers'
    }
  }
  // Get chi tiết Voucher (Không get Voucher đã xóa mềm)
  const fetchVoucherDetails = async (code: string) => {
    try {
      const response = await api.get(`/auth/vouchers/${code}`)
      state.value.voucherDetails = response.data
    } catch (error) {
      state.value.error = 'Không thể tải thông tin voucher'
    }
  }
  //   Get Voucher đã xóa mềm
  const fetchDeletedVouchers = async () => {
    try {
      const response = await api.get('/auth/vouchers/deleted')
      state.value.deletedVouchers = response.data
    } catch (error) {
      state.value.error = 'Không thể tải danh sách vouchers đã xóa'
    }
  }
  //   Tạo voucher (Kiểm lỗi khi tạo voucher trùng lặp)
  const createVoucher = async (voucherData: TVoucher) => {
    try {
      await api.post('/auth/vouchers/create', voucherData)
      await fetchVouchers()
    } catch (error) {
      state.value.error = 'Không thể tạo voucher mới'
    }
  }
  //   Xóa mềm Voucher
  const deleteVoucher = async (code: string) => {
    try {
      await api.post('/auth/vouchers/delete', { code })
      await fetchVouchers()
    } catch (error) {
      state.value.error = 'Không thể xóa voucher'
    }
  }
  //   Khôi phục Voucher đã xóa
  const restoreVoucher = async (code: string) => {
      try {
        await api.post('/auth/vouchers/restore', { code })
        await fetchDeletedVouchers()
      } catch (error) {
        state.value.error = 'Không thể khôi phục voucher'
      }
    },
    applyVoucher = async (voucherCode: string) => {
      const voucher = state.value.vouchers.find((v) => v.code === voucherCode)
      //   state.value.appliedVoucher = voucherCode
      if (voucher) {
        state.value.appliedVoucher = voucherCode
        ElNotification({
          title: 'Thành công',
          message: `Voucher "${voucherCode}" đã được áp dụng.`,
          type: 'success'
        })
      } else {
        state.value.error = 'Voucher không hợp lệ'
        ElNotification({
          title: 'Lỗi',
          message: 'Voucher không hợp lệ hoặc không tồn tại.',
          type: 'error'
        })
      }
    }
  //   const fetchAllVoucher

  return {
    fetchVouchers,
    fetchVoucherDetails,
    createVoucher,
    deleteVoucher,
    restoreVoucher,
    applyVoucher
  }
})
