export interface TVoucher {
  code: string
  description?: string
  discount_type?: string
  discount_value?: number
  usage_limit?: number
  expires_at?: string
  min_order_value?: number
  max_discount_value?: number
  status?: 'active' | 'inactive'
}
export interface TVoucherStoreState {
  vouchers: TVoucher[]
  deletedVouchers: TVoucher[]
  voucherDetails: TVoucher | null
  appliedVoucher: string
  error: string | null
  total_price?: number
  discount?: number
  total_price_after_discount?: number
}
