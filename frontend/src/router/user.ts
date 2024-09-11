const user = [
  {
    path: '/',
    component: () => import('@/layouts/ClientLayout.vue'),
    children: [
      {
        path: '/',
        name: 'user.home',
        component: () => import('@/views/user/HomePage.vue')
      }
    ]
  }
]

export default user
