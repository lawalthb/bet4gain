import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '87892ed076b91483ee2a',
    cluster: 'mt1',
    // wsPort: 6001 ?? 80,
    // wssPort: 6001 ?? 443,
    forceTLS: ('http' ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
