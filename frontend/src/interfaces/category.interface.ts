export interface TCategory {
  id?: number
  children?: TCategory[]
  image?: string
  name: string
  courses_count?: number
}
