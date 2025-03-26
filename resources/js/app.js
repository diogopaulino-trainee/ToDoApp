// Import base Tailwind/Custom CSS styles
import '../css/app.css';

// Import any Laravel-specific bootstrapping (e.g., Axios, CSRF, etc.)
import './bootstrap';

// Import Inertia's Vue 3 integration
import { createInertiaApp } from '@inertiajs/vue3';

// Helper to resolve Vue components from the /pages folder
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Vue core functions
import { createApp, h } from 'vue';

// Ziggy provides route helpers for client-side routing
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// PrimeVue UI framework and its toast service
import PrimeVue from 'primevue/config';
import Toast from 'primevue/toast';
import ToastService from 'primevue/toastservice';

// Pinia (Vue's modern alternative to Vuex) for global state management
import { createPinia } from 'pinia';

// Use the app name defined in your .env file, or fallback to 'Laravel'
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Main Inertia app initialization
createInertiaApp({
    // Set the page title dynamically using the current page + app name
    title: (title) => `${title} - ${appName}`,

    // Resolve pages dynamically from the `/pages` folder
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`, // Path to the page
            import.meta.glob('./pages/**/*.vue'), // All pages in that folder
        ),

    // Setup Vue app instance
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        // Create a Pinia store instance for global state
        const pinia = createPinia();

        // Register all necessary plugins
        app.use(plugin) // Inertia plugin
            .use(ZiggyVue) // Laravel routes in Vue
            .use(pinia) // Global state manager
            .use(PrimeVue) // PrimeVue component framework
            .use(ToastService); // Toast notification service

        // Register Toast globally
        app.component('Toast', Toast);

        // Mount the Vue app to the root element
        app.mount(el);
    },

    // Progress bar config when navigating between pages
    progress: {
        color: '#4B5563', // Tailwind gray-600
    },
});
