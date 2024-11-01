import { createRouter, createWebHistory } from 'vue-router'

import user from './user'
import teacher from './teacher'
import admin from './admin'
import { useAuthStore } from '@/store/auth'
import { storeToRefs } from 'pinia'
import { onMounted } from 'vue'
import { ElNotification } from 'element-plus'
const routes = [...user, ...teacher, ...admin]
const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  const { state } = storeToRefs(authStore)
  await authStore.userData()
  const isAuthenticated = state.value.token
  const userRole = state.value.user?.role
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
  }

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
  } else if (to.meta.role) {
    if (!userRole) {
      next('/')
      return
    }
    // Kiểm tra quyền truy cập dựa trên vai trò
    if (to.meta.role === 'admin' && userRole !== 'admin') {
      next('/') // Chặn nếu không phải admin
      ElNotification({
        title: 'Cảnh báo',
        message: 'Bạn không có quyền try cập!',
        type: 'warning'
      })
    } else if (to.meta.role === 'teacher' && !['teacher', 'admin'].includes(userRole)) {
      ElNotification({
        title: 'Cảnh báo',
        message: 'Bạn không có quyền try cập!',
        type: 'warning'
      })
      next('/') // Chặn nếu không phải teacher hoặc admin
    } else if (to.meta.role === 'student' && userRole !== 'student') {
      ElNotification({
        title: 'Cảnh báo',
        message: 'Bạn không có quyền try cập!',
        type: 'warning'
      })
      next('/') // Chặn nếu không phải user
    } else {
      next() // Cho phép truy cập nếu vai trò phù hợp
    }
  } else {
    next() // Cho phép truy cập nếu route không yêu cầu vai trò đặc biệt
  }
})
export default router
