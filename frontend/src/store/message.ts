// stores/courseStore.ts
import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/axiosConfig'

export const useMessageStore = defineStore('messageStore', () => {
  // State
  const waitingUserId = ref<any>(null)

  const setwWitingUserId = (id: any) => {
    // this.waitingUserId = id
  }


  return {

  }
})
