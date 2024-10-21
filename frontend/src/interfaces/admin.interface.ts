
export interface SidebarItemChildren2 {
  label: string
  route?: string
}
// Interface cho mục con của SidebarItem
export interface SidebarItemChildren {
  label: string
  route?: string
  children?: SidebarItemChildren2[]
}
export interface SidebarItem {
  label: string
  route?: string
  icon?: any
  children?: SidebarItemChildren[]
}

//  Interface cho nhóm menu
export interface MenuGroup {
  name: string; // Tên của nhóm menu
  menuItems: SidebarItem[]; // Mảng các mục trong nhóm menu
}


//Interface category
export interface ListCategories {
  img: string;
  icon: any;
  title: string;
  keyword?: string;
  description?: string;
  children?: ListCategoriesChildren[];
}
export interface ListCategoriesChildren { 
  img: string;
  icon: any;
  title: string;
  keyword?: string;
  description?: string;
}