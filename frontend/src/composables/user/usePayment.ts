// src/composables/user/usePayment.ts
import { ref } from 'vue'
import { loadStripe } from '@stripe/stripe-js'
import type { Stripe, PaymentIntent, StripeCardElement } from '@stripe/stripe-js'

const stripePromise = loadStripe(import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY as string) // Sử dụng biến môi trường

export function usePayment() {
  const stripe = ref<Stripe | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const initializeStripe = async () => {
    stripe.value = await stripePromise
  }

  const confirmPayment = async (clientSecret: string, cardElement: StripeCardElement) => {
    if (!stripe.value) {
      await initializeStripe()
    }

    if (!stripe.value) {
      throw new Error('Stripe failed to initialize')
    }

    loading.value = true
    try {
      const { error: stripeError, paymentIntent } = (await stripe.value.confirmCardPayment(
        clientSecret,
        {
          payment_method: {
            card: cardElement,
            billing_details: {
              name: 'Customer Name' // Thay bằng dữ liệu động nếu có
            }
          }
        }
      )) as { error: any; paymentIntent: PaymentIntent | null }

      if (stripeError) {
        error.value = stripeError.message || 'Payment failed'
        throw stripeError
      }

      return paymentIntent
    } catch (err: any) {
      error.value = err.message || 'Payment confirmation failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    stripe,
    loading,
    error,
    confirmPayment
  }
}
