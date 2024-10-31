export interface TCategory {
  id?: number
  children?: TCategory[]
  image?: string
  name: string
  countCourse?: number
}


// ====== ADMIN CATEGORY ======

export interface TListCategories {
  image: string | undefined;
  icon: any;
  name: string;
  keyword?: string;
  description?: string;
  status?: 'active' | 'unactive';
  children?: TListCategoriesChildren[];
}
export interface TListCategoriesChildren extends TListCategories { 
}