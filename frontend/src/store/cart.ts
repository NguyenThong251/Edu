import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/axiosConfig'
import Cookies from 'js-cookie'

export const useCartStore = defineStore('cart', () => {
  // const cart = ref<number[]>([])
  const cart = ref<any[]>([])
  const loading = ref(false)
  const token = Cookies.get('token_user_edu')

  const isAuthenticated = computed(() => !!token)
  const loadCartFromLocalStorage = () => {
    const storeCart: string | null = localStorage.getItem('cart_courses')
    if (storeCart) {
      cart.value = JSON.parse(storeCart)
    } else {
      cart.value = []
    }
  }
  const saveCartToLocalStorage = () => {
    localStorage.setItem('cart_courses', JSON.stringify(cart.value))
  }

  const addCourseToCart = async (courseId: number) => {
    loading.value = true
    try {
      if (isAuthenticated.value) {
        // Add course to user’s cart via API
        await api.post('/auth/cart/courses', { course_id: courseId })
        await fetchCartCourses()
      } else {
        // If user is not authenticated, store in localStorage
        cart.value.push(courseId)
        saveCartToLocalStorage()
        loading.value = false
      }
    } catch (error) {
      console.error('Error adding course to cart:', error)
    } finally {
      loading.value = false
    }
  }

  const fetchCartCourses = async () => {
    loading.value = true
    try {
      if (isAuthenticated.value) {
        const response = await api.get('/auth/cart/courses')
        cart.value = await response.data.courses
      } else {
        // Load from localStorage if not authenticated
        loadCartFromLocalStorage()
        return cart.value // Return the loaded cart data
      }
    } catch (error) {
      console.error('Error fetching cart courses:', error)
    } finally {
      loading.value = false
    }
  }

  const removeCourseFromCart = async (courseId: number) => {
    loading.value = true
    try {
      if (isAuthenticated.value) {
        // Remove course from user's cart via API
        await api.delete(`/auth/cart/courses/${courseId}`)
        await fetchCartCourses()
      } else {
        // If user is not authenticated, remove from localStorage
        cart.value = cart.value.filter((id) => id !== courseId)
        saveCartToLocalStorage()
        loading.value = false
      }
    } catch (error) {
      console.error('Error removing course from cart:', error)
    } finally {
      loading.value = false
    }
  }
  const clearCart = async () => {
    loading.value = true
    try {
      if (isAuthenticated.value) {
        // Clear the cart for the authenticated user via API
        await api.delete('/auth/cart/courses')
        await fetchCartCourses()
        cart.value = []
      } else {
        // Clear the cart in localStorage if not authenticated
        cart.value = []
        saveCartToLocalStorage()
        loading.value = false
      }
    } catch (error) {
      console.error('Error clearing cart:', error)
    } finally {
      loading.value = false
    }
  }
  return {
    cart,
    loading,
    addCourseToCart,
    fetchCartCourses,
    removeCourseFromCart,
    clearCart
  }
})
