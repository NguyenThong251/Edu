import LayoutCourseVideo from '@/layouts/LayoutCourseVideo.vue'
import TeacherLayout from '@/layouts/TeacherLayout.vue'

const teacher = [
  {
    path: '/teacher',
    component: TeacherLayout,
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
