// src/services/axiosConfig.ts
import axios from 'axios'

const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api' // URL của API backend
})

api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}` // Thêm token vào header nếu có
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

export default api
