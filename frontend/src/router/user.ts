import UserDashboard from '@/layouts/UserDashboard.vue'

const user = [
  {
    path: '/',
    component: () => import('@/layouts/ClientLayout.vue'),
    children: [
      {
        path: '/',
        name: 'user.home',
        component: () => import('@/views/user/HomePage.vue')
      },
      {
        path: '/course',
        name: 'user.course',
        component: () => import('@/views/user/CoursePage.vue')
      },
      {
        path: '/course/detail',
        name: 'user.course.detail',
        component: () => import('@/views/user/CourseDetailView.vue')
      },
      // {
      //   path: '/course/:id',
      //   name: 'user.course.detail',
      //   component: () => import('@/views/user/CourseDetailView.vue'),
      //   prop: true
      // },
      {
        path: '/cart',
        name: 'user.cart',
        component: () => import('@/views/user/CartPage.vue')
      },
      {
        path: '/login',
        name: 'login',
        component: () => import('@/views/user/Login.vue')
      },
      {
        path: '/register',
        name: 'register',
        component: () => import('@/views/user/Register.vue')
      },
      {
        path: '',
        component: UserDashboard,
        children: [
          {
            path: '/mycourses',
            name: 'mycourses',
            component: () => import('@/views/user/MyCourses.vue')
          },
          {
            path: '/myprofile',
            name: 'myprofile',
            component: () => import('@/views/user/MyProfile.vue')
          }
        ]
      }
    ]
  }
]

export default user
