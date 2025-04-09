import { createApp } from 'vue';
import { createPinia } from 'pinia'; 
import router from './router'; 
import App from './App.vue';

// Initialize the Vue app
const app = createApp(App);

// Set up Pinia (state management)
const pinia = createPinia();
app.use(pinia);

// Set up Vue Router
app.use(router);

// Mount the app to the DOM element with id="app" 
// (matches the div in your Laravel Blade template)
app.mount('#app');
