import { useCartStore } from '@/store/cart'

export function useCart() {
  const cartStore = useCartStore()

  // Add course to cart (checks auth internally)
  const handleAddToCart = (courseId: number) => {
    cartStore.addCourseToCart(courseId)
  }

  // Remove course from cart
  const handleRemoveFromCart = (courseId: number) => {
    cartStore.removeCourseFromCart(courseId)
  }

  return {
    handleAddToCart,
    handleRemoveFromCart
  }
}
