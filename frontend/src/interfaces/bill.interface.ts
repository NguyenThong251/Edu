export interface TBill {
  id: number
  user_id?: number
  voucher_id?: string
  order_code: string
  total_price: number
  currency: 'usd' | 'vnd'
  payment_method?: string
  payment_status: 'pending' | 'paid'
  payment_code: string
  status: 'active' | 'inactive'
  created_at?: string
}
