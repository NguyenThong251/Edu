const user = [
  {
    path: '/',
    component: () => import('@/layouts/ClientLayout.vue'),
    children: [
      {
        path: 'home',
        name: 'user.home',
        component: () => import('@/views/user/HomePage.vue')
      }
    ]
  }
]

export default user
