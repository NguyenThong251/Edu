export interface CardCourse {
  // id: string
  image: string
  lecture: string
  name: string
  lessons: number | string
  level: 'Mới bắt đầu' | 'Nâng cao' | 'Đánh giá cao nhất'
  price: string
  oldPrice: string
  review?: string | number
  rate?: number
  status: 'Bán chạy' | 'Mới'
}
