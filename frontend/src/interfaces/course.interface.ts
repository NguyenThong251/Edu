export interface TCardCourse {
  creator?: string
  current_price: number
  id: number
  lectures_count?: number
  level: string
  old_price?: number
  average_rating?: number
  reviews_count?: number
  status?: 'active' | 'inactive'
  tag?: 'Bán chạy' | 'Mới' | 'none'
  thumbnail?: string
  title: string
  total_duration?: number
}
// export interface TCardCourse {
//   id: number
//   creator: string
//   current_price: number
//   thumbnail: string
//   lecture: string
//   title: string
//   lessons?: number
//   rating_avg: 0
//   // level: 'Mới bắt đầu' | 'Nâng cao' | 'Đánh giá cao nhất'
//   level: string

//   price: number
//   oldPrice: number | null
//   // review?: string | number
//   // rate?: number
//   sale_value?: number
//   tag: 'Bán chạy' | 'Mới'
//   type_sale?: 'percent' | 'price'
//   status?: 'active' | 'inactive'
// }

export interface CardMyCourse {
  image: string
  name: string
  lecture: string
  completed: number
  total: number
}
export interface TCartCourseViewItem {
  id: number
  thumbnail: string
  title: string
  current_price: number
  old_price: number
  category: string
}
export interface TCourseFilters {
  category_ids?: number[]
  duration_range?: number
  min_rating?: number
  max_rating?: number
  level_ids?: number[]
  keyword?: string
  sort_by?: string
  sort_order?: string
  language_ids?: number[]
  // Add more properties as needed based on the filters you are using.
}
