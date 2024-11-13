export interface TCategory {
  id?: number
  children?: TCategory[]
  image?: string
  name: string
  courses_count?: number
}

// ====== ADMIN CATEGORY ======

export interface TListCategories {
  id?: number
  image?: string | null
  icon: any
  name: string
  keyword?: string
  description?: string
  status?: string
  children?: TListCategoriesChildren[]
}
export interface TListCategoriesChildren extends TListCategories {}
export interface CurrentCategoryType {
  id?: number | null
  name?: string
  parentCategoryId?: number | null
  parentCategoryName?: string
}
