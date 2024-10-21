export interface TCategory {
  id?: string
  children?: TCategory[]
  image: string
  name: string
  countCourse?: number
}
