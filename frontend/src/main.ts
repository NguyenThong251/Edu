import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router/index'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import '@/assets/index.css'
// video
import VideoPlayer from 'vue-video-player'
import 'video.js/dist/video-js.css'

const pinia = createPinia()
const app = createApp(App)
app.use(VideoPlayer)
app.use(ElementPlus)
app.use(router)
app.use(pinia)
app.mount('#app')
