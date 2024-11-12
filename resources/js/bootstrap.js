import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';




window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key:'87892ed076b91483ee2a',       // Reverb key
    cluster: 'mt1',
   
 forceTLS: true,
    encrypted: false,
    enabledTransports: ['ws', 'wss'],
    disableStats: false,
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';
