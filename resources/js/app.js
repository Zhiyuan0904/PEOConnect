// resources/js/app.js

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import router from './router'
import App from './App.vue'
import axios from './axios'
import '@fortawesome/fontawesome-free/css/all.min.css';
import './assets/main.css';

// 🔧 Create Pinia store and register plugin correctly
const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

// 🔄 Mount app after validating auth
async function initializeApp() {
  try {
    await axios.get('/auth/check')  // 204 OK if session still valid
  } catch {
    router.replace('/login')  // redirect before rendering the app
  } finally {
    createApp(App)
      .use(pinia)
      .use(router)
      .mount('#app')
  }
}

initializeApp()
