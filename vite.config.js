import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/public-session.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        hmr: {
            host: 'localhost',
        },
        // Untuk development WebSocket
        cors: true,
    },
    optimizeDeps: {
        include: [
            'alpinejs',
            'livewire',
            'laravel-echo',
            'pusher-js',
            'nprogress',
            'sweetalert2',
            'aos',
            '@alpinejs/focus'
        ],
    },
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});
