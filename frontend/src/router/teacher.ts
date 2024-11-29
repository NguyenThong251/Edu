import LayoutCourseVideo from '@/layouts/LayoutCourseVideo.vue'
import TeacherLayout from '@/layouts/TeacherLayout.vue'
import UserDashboard from '@/layouts/UserDashboard.vue'
import CourseListPage from '@/views/teacher/CourseListPage.vue'
import PaymentPage from '@/views/teacher/PaymentPage.vue'
import MyProfile from '@/views/user/MyProfile.vue'
import Security from '@/views/user/Security.vue'

const teacher = [
  {
    path: '/teacher',
    // component: () => import('@/layouts/ClientLayout.vue'),
    component: TeacherLayout,
    meta: { requiresAuth: true, role: 'instructor', title: 'Thống kê | Edunity' },
    children: [
      {
        path: '/teacher',
        name: 'teacher.index',
        component: () => import('@/views/teacher/DashboardPage.vue'),
        meta: { requiresAuth: true, role: 'instructor', title: 'Thống kê | Edunity' }
      },
      {
        path: '/teacher/security',
        name: 'teacher.security',
        component: Security,
        meta: { requiresAuth: true, role: 'instructor', title: 'Bảo mật | Edunity' }
      },
      {
        path: '/teacher/profile-setting',
        name: 'teacher.profile-setting',
        component: MyProfile,
        meta: { requiresAuth: true, role: 'instructor', title: 'Cài đặt| Edunity' }
      },
      {
        path: '/teacher/course/list',
        name: 'teacher.course',
        component: CourseListPage,
        meta: { requiresAuth: true, role: 'instructor', title: 'Bảo mật | Edunity' }
      },
      {
        path: '/teacher/payment',
        name: 'teacher.payment',
        component: PaymentPage,
        meta: { requiresAuth: true, role: 'instructor', title: 'Bảo mật | Edunity' }
      }
    ]
  }
]
export default teacher
