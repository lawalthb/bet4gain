import 'bootstrap';
import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

axios.get('/settings').then(response => {
    // console.log('Pusher Key:', response.data.pusher_key);
    // console.log('Pusher Cluster:', response.data.pusher_cluster);


  localStorage.setItem('pusherKey', response.data.pusher_key);
    localStorage.setItem('pusherCluster', response.data.pusher_cluster);


    window.Pusher = Pusher;

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: response.data.pusher_key,
        cluster: response.data.pusher_cluster,
        forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
});
