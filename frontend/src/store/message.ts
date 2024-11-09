// stores/courseStore.ts
import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/axiosConfig'

export const useMessageStore = defineStore('messageStore',  {
  state: () => {
    return { 
      waitingUserChat: null as any
    }
  },
  // could also be defined as
  // state: () => ({ count: 0 })
  actions: {
    setWaitingUserChat(id: any) {
      this.waitingUserChat = id
    }
  },

})
