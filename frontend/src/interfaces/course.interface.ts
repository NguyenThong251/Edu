export interface TCardCourse {
  id: number
  thumbnail: string
  lecture: string
  title: string
  lessons: number
  level: 'Mới bắt đầu' | 'Nâng cao' | 'Đánh giá cao nhất'
  price: number
  oldPrice: number | null
  // review?: string | number
  // rate?: number
  sale_value?: number
  tag: 'Bán chạy' | 'Mới'
  type_sale?: 'percent' | 'price'
  status?: 'active' | 'inactive'
}

export interface CardMyCourse {
  image: string
  name: string
  lecture: string
  completed: number
  total: number
}
