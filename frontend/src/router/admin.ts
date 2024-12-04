import AdminLayout from '@/layouts/AdminLayout.vue'
import Dashboard from '@/views/admin/Dashboard.vue'
import Category from '@/views/admin/Category.vue'
import AddCategory from '@/views/admin/AddCategory.vue'
const admin = [
  {
    path: '/admin/dashboard',
    name: 'admin',
    component: AdminLayout,
    meta: {
      title: 'Trang quản trị | Edunity'
    },
    children: [
      {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: Dashboard,
        meta: {
          title: 'Bảng điều khiển | Edunity'
        }
      },
      {
        path: '/admin/category',
        name: 'admin.category',
        component: Category,
        meta: {
          title: 'Danh mục khoá học | Edunity'
        }
      },
      {
        path: '/admin/add-category',
        name: 'admin.add-category',
        component: AddCategory,
        meta: {
          title: 'Danh mục khoá học | Edunity'
        }
      }
    ]
  }
]
export default admin
