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
      }
    ]
  }
]

export default user
