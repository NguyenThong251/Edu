import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/axiosConfig'
import type { Tlevel } from '@/interfaces'
import { ElMessage } from 'element-plus'

export interface TReview {
  id: number
  course_id: number
  rating: number
  comment: string
  status: 'active' | 'inactive'
  first_name: string
  last_name: string
  user: {
    id: number
    avatar: string
  }
}

interface TReviewState {
  listReview: TReview[]
}

export const useReviewsStore = defineStore('reviews', () => {
  const state = ref<TReviewState>({
    listReview: []
  })

  const fetchReviews = async (id: number) => {
    try {
      const response = await api.get(`/courses/${id}/review`)
      state.value.listReview = response.data.data
    } catch (error) {
      console.log(error)
    }
  }

  return {
    state,
    fetchReviews
  }
})
