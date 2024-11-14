import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';

createApp(App).mount('#app');

import PublicChat from './components/PublicChat.vue';

createApp(PublicChat).mount('#chat');
