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
  tag?: string
  thumbnail?: string
  title: string
  total_duration?: number
}
// edit
export interface TCourseAdmin {
  id: number
  title: string
  slug: string
  description: string
  short_description: string
  thumbnail: string | null
  language_id: number | string
  level_id: number | string
  category_id: number | string
  price: string
  type_sale: string
  sale_value: number | string
  status: 'active' | 'inactive'
  created_at: string
  updated_at: string
  [key: string]: any; // Index signature
}

export interface TCardMyCourse {
  id: number
  thumbnail?: string
  title: string
  creator: string
  total_lectures: number
  completed_lectures: number
  progress_percent: number
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
}
export interface TSection {
  id?: number | string
  status?: string
  course_id?: number[] | string
  title?: string
  order?: number | string
  lectures?: TLectures[]
  quizzes?: TQuiz[]
  [key: string]: any;
}

export interface TLectures {
  id?: number | string
  title?: string
  type?: string
  section_id?: number | string
  content?: string
  duration?: number | string
  preview?: string
  status?: string
  order?: number | string
  [key: string]: any;
}
export interface TQuiz {
  id?: number | string
  status?: string
  title?: string
  type?: string
  section_id?: number | string
  order?: number | string
  questions?: TQuestion[]
}
export interface TQuestion {
  id?: number | string
  question?: string[]
  answer?: string
  status?: string
  title?: string
  type?: string
  quiz_id?: number | string
  order?: number | string
}