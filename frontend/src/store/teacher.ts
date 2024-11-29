import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/axiosConfig'
import type { Tlevel } from '@/interfaces'
import { ElMessage, ElMessageBox } from 'element-plus'

export interface TReview {
  id: number
  course_id: number
  rating: number
  comment: string
  created_at: string
  status: 'active' | 'inactive'
  user: {
    first_name: string
    last_name: string
    id: number
    avatar: string
  }
}

interface TReviewState {
  listReview: TReview[]
}

export const useTeacherStore = defineStore('teacher', () => {
  const state = ref<TReviewState>({
    listReview: []
  })

  return {
    state
  }
})
