import { createApp } from 'vue'
import { createPinia } from 'pinia'
import './style.css'
import App from './App.vue'
import router from './router'

const app = createApp(App)

app.config.errorHandler = (err, vm, info) => {
  console.error('VUE ERROR:', err, info)
}

app.use(createPinia())
app.use(router)

app.mount('#app')
