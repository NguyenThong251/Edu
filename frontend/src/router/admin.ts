// const router = useRouter()
// router/index.ts
const admin = [
  {
    path: '/admin',
    component: () => import('@/views/admin/DashboardPage.vue'),
    meta: { requiresAuth: true, role: 'admin' } // Yêu cầu xác thực và vai trò admin
  }
]

export default admin
