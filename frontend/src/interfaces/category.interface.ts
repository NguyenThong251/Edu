export interface Category {
  // id: string
  image: string
  name: string
  countCourse?: number
}


// ====== ADMIN CATEGORY ======

export interface TListCategories {
  img: string;
  icon: any;
  title: string;
  keyword?: string;
  description?: string;
  children?: TListCategoriesChildren[];
}
export interface TListCategoriesChildren { 
  img: string;
  icon: any;
  title: string;
  keyword?: string;
  description?: string;
}