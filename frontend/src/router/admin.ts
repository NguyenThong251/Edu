import AdminLayout from "@/layouts/AdminLayout.vue";
import Dashboard from "@/views/admin/Dashboard.vue";
import Category from "@/views/admin/Category.vue";
import ManagerCourse from "@/views/admin/Course/ManagerCourse.vue";
import ManagerCoupon from "@/views/admin/Course/ManagerCoupon.vue";
import AddCourse from "@/views/admin/Course/AddCourse.vue";
import AddCourseDetail from "@/views/admin/Course/AddCourseDetail.vue";
import ReportpaymentAdmin from "@/views/admin/Reportpayment/ReportpaymentAdmin.vue";
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
        path: '/admin/course',
        name: 'admin.course',
        component: ManagerCourse,
        meta: {
          title: 'Khoá học | Edunity'
        },
      },
      {
        path: '/admin/course/manager-course', 
        name: 'admin.manager-course',
        component: ManagerCourse,
        meta: {
          title: 'Quản lý khoá học | Edunity'
        }
      },
      {
        path: '/admin/course/add-course',
        name: 'admin.add-course',
        component: AddCourse,
        meta: {
          title: 'Thêm khoá học mới | Edunity'
        }
      },
      {
        path: '/admin/course/manager-coupon',
        name: 'admin.manager-coupon',
        component: ManagerCoupon,
        meta: {
          title: 'Thêm khoá học mới | Edunity'
        }
      },
      {
        path: '/admin/course/add-course-detail', 
        name: 'admin.add-course-detail',
        component: AddCourseDetail, 
        meta: {
          title: 'Thêm khoá học chi tiết | Edunity'
        }
      },
      {
        path: '/admin/reportpayment', 
        name: 'admin.reportpayment',
        component: ReportpaymentAdmin, 
        meta: {
          title: 'Báo cáo danh thu ADMIN  | Edunity'
        }
      },
      {
        path: '/admin/reportpayment/admin-revenua', 
        name: 'admin.admin-revenua',
        component: ReportpaymentAdmin, 
        meta: {
          title: 'Báo cáo danh thu ADMIN  | Edunity'
        }
      },
    ]
  }
]
export default admin