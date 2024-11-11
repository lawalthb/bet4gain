import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '123456',
    wsHost: '127.0.0.1',
    cluster: 'mt1',
    wsPort: 6001,
    forceTLS: false,
    encrypted: false,
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
});
