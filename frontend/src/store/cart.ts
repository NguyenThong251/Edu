import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/axiosConfig'
import Cookies from 'js-cookie'

export const useCartStore = defineStore('cart', () => {
  const cart = ref<number[]>([])
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
    if (isAuthenticated.value) {
      try {
        // Add course to user’s cart via API
        const response = await api.post('/auth/cart/courses', { course_id: courseId })
        cart.value.push(courseId)
      } catch (error) {
        console.error('Error adding course to cart:', error)
      }
    } else {
      // If user is not authenticated, store in localStorage
      cart.value.push(courseId)
      saveCartToLocalStorage()
    }
  }

  const fetchCartCourses = async () => {
    if (isAuthenticated.value) {
      try {
        const response = await api.get('/auth/cart/courses')
        // cart.value = response.data.courses
        return response.data.courses
      } catch (error) {
        console.error('Error fetching cart courses:', error)
      }
    } else {
      // Load from localStorage if not authenticated
      loadCartFromLocalStorage()
    }
  }

  const removeCourseFromCart = async (courseId: number) => {
    if (isAuthenticated.value) {
      try {
        // Remove course from user's cart via API
        await api.delete(`/auth/cart/courses/${courseId}`)
        cart.value = cart.value.filter((id) => id !== courseId)
      } catch (error) {
        console.error('Error removing course from cart:', error)
      }
    } else {
      // If user is not authenticated, remove from localStorage
      cart.value = cart.value.filter((id) => id !== courseId)
      saveCartToLocalStorage()
    }
  }
  const clearCart = async () => {
    if (isAuthenticated.value) {
      try {
        // Clear the cart for the authenticated user via API
        await api.delete('/auth/cart/courses')
        cart.value = []
      } catch (error) {
        console.error('Error clearing cart:', error)
      }
    } else {
      // Clear the cart in localStorage if not authenticated
      cart.value = []
      saveCartToLocalStorage()
    }
  }
  return {
    cart,
    addCourseToCart,
    fetchCartCourses,
    removeCourseFromCart,
    clearCart
  }
})
