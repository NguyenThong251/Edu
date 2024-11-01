import LayoutCourseVideo from '@/layouts/LayoutCourseVideo.vue'
import UserDashboard from '@/layouts/UserDashboard.vue'

const teacher = [
  {
    path: '/teacher',
    component: () => import('@/layouts/ClientLayout.vue'),
    children: [
      {
        path: '',
        name: 'teacher.index',
        component: () => import('@/views/teacher/DashboardPage.vue')
      }
    ]
  }
]
export default teacher
