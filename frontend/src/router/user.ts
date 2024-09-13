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
        path: '/cart',
        name: 'user.course',
        component: () => import('@/views/user/CartPage.vue')
      }
    ]
  }
]

export default user
