export interface TCategory {
  id?: number
  children?: TCategory[]
  image?: string
  name: string
  countCourse?: number
}
