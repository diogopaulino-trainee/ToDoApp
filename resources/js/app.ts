// Import global CSS styles (e.g., Tailwind or custom styles)
import '../css/app.css';

// Inertia core function for Vue 3
import { createInertiaApp } from '@inertiajs/vue3';

// Helps resolve pages dynamically from the `/pages` folder
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Vue type import to support better type inference for dynamic components
import type { DefineComponent } from 'vue';

// Vue core functions
import { createApp, h } from 'vue';

// Ziggy client-side routing (Laravel route helper)
import { ZiggyVue } from 'ziggy-js';

// PrimeVue UI framework and services
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice';

// Dark/light mode theme initializer (custom composable)
import { initializeTheme } from './composables/useAppearance';

// Custom directive to auto-focus elements
import vFocus from './directives/vFocus';

// Vue Final Modal - external modal management plugin
import { createVfm } from 'vue-final-modal';
import 'vue-final-modal/style.css'; // Modal styles

// Pinia - state management for Vue (modern alternative to Vuex)
import { createPinia } from 'pinia';

/*
  Optional: Extend Vite's ImportMeta interface for better intellisense and type safety
  in environments that support `import.meta.env` and glob imports.
*/
// declare module 'vite/client' {
//     interface ImportMetaEnv {
//         readonly VITE_APP_NAME: string;
//         [key: string]: string | boolean | undefined;
//     }

//     interface ImportMeta {
//         readonly env: ImportMetaEnv;
//         readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
//     }
// }

// Read the application name from the environment or fallback to "Laravel"
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Initialize the Inertia app
createInertiaApp({
    // Dynamically generate the document title
    title: (title) => `${title} - ${appName}`,

    // Resolve pages from the ./pages directory
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),

    // Setup Vue application
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        // Register custom directives
        app.directive('focus', vFocus);

        // Create instances of Pinia and Vue Final Modal
        const pinia = createPinia();
        const vfm = createVfm();

        // Register plugins
        app.use(plugin) // Inertia plugin
            .use(ZiggyVue) // Laravel routing (Ziggy)
            .use(pinia) // Global store
            .use(PrimeVue) // UI component framework
            .use(ToastService) // Toast notification service
            .use(vfm) // Modal support

            // Mount the app
            .mount(el);
    },

    // Configure Inertia progress bar color
    progress: {
        color: '#4B5563', // Tailwind gray-600
    },
});

// Set the app's theme (e.g., light or dark) based on user/system preference
initializeTheme();
