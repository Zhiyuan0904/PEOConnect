import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    // Set relative base path so that assets are linked correctly
    base: '/build/',

    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],

    // Server section is only for local development
    server: {
        host: '0.0.0.0', // Allow access on local network if needed
        // Remove these to prevent wrong absolute asset links in production build
        // https: true,
        // origin: 'https://peoconnect.onrender.com',
    },
});
