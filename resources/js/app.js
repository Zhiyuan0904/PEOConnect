import { createApp } from 'vue';
import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'; 
import router from './router';
import App from './App.vue';
import '@fortawesome/fontawesome-free/css/all.min.css';

const app = createApp(App);

const pinia = createPinia();
pinia.use(piniaPluginPersistedstate); 

app.use(pinia);
app.use(router);

app.mount('#app');
