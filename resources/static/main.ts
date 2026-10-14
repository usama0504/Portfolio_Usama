import { createApp } from 'vue';
import '../css/app.css';
import { initializeTheme } from '@/composables/useAppearance';
import App from './App.vue';

initializeTheme();
createApp(App).mount('#app');
