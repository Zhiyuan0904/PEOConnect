import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    // Add these two options:
    base: 'https://peoconnect.onrender.com/build/',
    server: {
        https: true,
        host: 'peoconnect.onrender.com',
        origin: 'https://peoconnect.onrender.com',
    },
});
