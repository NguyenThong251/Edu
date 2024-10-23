import { useCartStore } from '@/store/cart'
import Cookies from 'js-cookie'
import { storeToRefs } from 'pinia'
import { computed, ref, watchEffect } from 'vue'

export function useCart() {
  const cartStore = useCartStore()
  const { cartDb, cartLocal, loading } = storeToRefs(cartStore)
  const isAuthenticated = ref(!!Cookies.get('token_user_edu'))
  watchEffect(() => {
    isAuthenticated.value = !!Cookies.get('token_user_edu')
  })

  const cart = computed(() => {
    return isAuthenticated.value ? cartDb.value : cartLocal.value
  })

  const handleAddToCart = (courseId: number) => {
    cartStore.addCourseToCart(courseId)
  }

  const handleRemoveFromCart = (courseId: number) => {
    cartStore.removeCourseFromCart(courseId)
  }

  return {
    cart,
    isAuthenticated,
    loading,
    clearCart: cartStore.clearCart,
    fetchCartCourses: cartStore.fetchCartCourses,
    handleAddToCart,
    handleRemoveFromCart
  }
}
