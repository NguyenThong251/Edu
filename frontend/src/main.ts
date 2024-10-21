import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router/index'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import '@/assets/index.css'
import '@/assets/css/style.css'


// Icon Awesome
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'; 
import { fas } from '@fortawesome/free-solid-svg-icons';

// video
import VideoPlayer from 'vue-video-player'
import 'video.js/dist/video-js.css'

library.add(fas)

const app = createApp(App)
const pinia = createPinia() // Khởi tạo pinia

app.component('font-awesome-icon', FontAwesomeIcon);

app.use(VideoPlayer)
app.use(ElementPlus)
app.use(router)
app.use(pinia)
app.mount('#app')
