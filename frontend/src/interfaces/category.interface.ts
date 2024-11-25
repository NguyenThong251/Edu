export interface TCategory {
  id?: number
  image?: string | File
  name: string
  icon?: string
  courses_count?: number
  keyword?: string
  description?: string
  status?: 'active' | 'inactive'
  children?: TCategory[]
}

// ====== ADMIN CATEGORY ======

export interface TListCategories {
  id?: number| string
  image?: string
  icon: any
  name: string
  keyword?: string
  description?: string
  status?: string
  children?: TListCategories[]
}
export interface CurrentCategoryType {
  id?: number
  name?: string
  parentCategoryId?: number
  parentCategoryName?: string
}
