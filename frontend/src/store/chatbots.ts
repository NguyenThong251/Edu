import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/axiosConfig'
import type { Tlevel } from '@/interfaces'
import { ElMessage, ElMessageBox } from 'element-plus'
// const loading = ref(false)
interface TConversation {
  role: string
  content: string
}
export interface THistoryChatBot {
  id: number
  user_id: number
  conversation: TConversation[]
  created_at: string
  updated_at: string
}

export const useChatbot = defineStore('chatbot', () => {
  const listHistoryChatBot = ref<THistoryChatBot[]>([])
  const loading = ref<boolean>(false)
  const error = ref<string | null>(null)
  const responseMessage = ref<string>('')

  const fetchChatHistory = async () => {
    loading.value = true
    try {
      const res = await api.get('/chat-bot/history')
      listHistoryChatBot.value = res.data
    } catch (err) {
      error.value = 'Không thể tải lịch sử hội thoại'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  const saveChat = async (userMessage: string) => {
    loading.value = true
    try {
      const formData = new FormData()
      formData.append('chat_bot_message', userMessage)

      const res = await api.post('/chat-bot/save', formData)
      responseMessage.value = res.data.response

      ElMessage({
        message: res.data.message || 'Đã lưu hội thoại thành công',
        type: 'success'
      })
      await fetchChatHistory()
    } catch (err) {
      error.value = 'Gửi tin nhắn thất bại!'
      ElMessage({
        message: 'Có lỗi xảy ra khi lưu cuộc trò chuyện',
        type: 'error'
      })
      console.error(err)
    } finally {
      loading.value = false
    }
  }
  return {
    listHistoryChatBot,
    fetchChatHistory,
    saveChat
  }
})
