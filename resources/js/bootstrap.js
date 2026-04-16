import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Import Echo dan Pusher untuk WebSocket
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Setup Pusher
window.Pusher = Pusher;

// Debug mode untuk development
const isDevelopment = import.meta.env.MODE === 'development';

// Setup Echo dengan konfigurasi Reverb
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'your-reverb-key',
    wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    // Untuk development
    debug: isDevelopment,
});

// Log Pusher dinonaktifkan
Pusher.logToConsole = false;
