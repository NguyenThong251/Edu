export interface TCategory {
  id?: number
  children?: TCategory[]
  image?: string
  name: string
  countCourse?: number
}


// ====== ADMIN CATEGORY ======

export interface TListCategories {
  id: number;
  image: string | undefined;
  icon: any;
  name: string;
  keyword?: string;
  description?: string ;
  status?: string | undefined;
  children?: TListCategoriesChildren[];
}
export interface TListCategoriesChildren extends TListCategories { 
}
export interface CurrentCategoryType {
  id: number | null;
  name: string;
  parentCategoryId: number | null;
  parentCategoryName: string;
}