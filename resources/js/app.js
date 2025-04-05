import { createApp } from 'vue';
import App from './App.vue';
import router from './router'; // Ensure Vue Router is imported

const app = createApp(App);
app.use(router);
app.mount('#app'); // Ensure this ID matches the div in your Blade template
