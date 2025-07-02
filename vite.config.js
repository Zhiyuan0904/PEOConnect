import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    base: '/build/',  // relative base for built assets
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    server: {
        https: true,
        origin: 'https://peoconnect.onrender.com', // important for absolute URLs on build
        host: '0.0.0.0', // local dev server binding
    },
});
