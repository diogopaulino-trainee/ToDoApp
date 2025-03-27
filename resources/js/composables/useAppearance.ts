import { onMounted, ref } from 'vue';

/**
 * Appearance type
 * @typedef {('light' | 'dark' | 'system')} Appearance
 */
type Appearance = 'light' | 'dark' | 'system';

/**
 * Updates the theme based on the appearance preference.
 * @param {Appearance} value - The appearance preference ('light', 'dark', or 'system')
 */
export function updateTheme(value: Appearance) {
    if (typeof window === 'undefined') {
        return;
    }

    if (value === 'system') {
        const mediaQueryList = window.matchMedia('(prefers-color-scheme: dark)');
        const systemTheme = mediaQueryList.matches ? 'dark' : 'light';

        document.documentElement.classList.toggle('dark', systemTheme === 'dark');
    } else {
        document.documentElement.classList.toggle('dark', value === 'dark');
    }
}

/**
 * Sets a cookie with the given name and value.
 * @param {string} name - The name of the cookie
 * @param {string} value - The value of the cookie
 * @param {number} days - The number of days to store the cookie (default is 365)
 */
const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') {
        return;
    }

    const maxAge = days * 24 * 60 * 60;

    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

/**
 * Returns the media query for the system theme.
 * @returns {MediaQueryList | null} The media query for the system theme or null if not supported
 */
const mediaQuery = () => {
    if (typeof window === 'undefined') {
        return null;
    }

    return window.matchMedia('(prefers-color-scheme: dark)');
};

/**
 * Returns the stored appearance from localStorage.
 * @returns {Appearance | null} The stored appearance or null if not supported
 */
const getStoredAppearance = () => {
    if (typeof window === 'undefined') {
        return null;
    }

    return localStorage.getItem('appearance') as Appearance | null;
};

/**
 * Handles the system theme change event.
 * @returns {void}
 */
const handleSystemThemeChange = () => {
    const currentAppearance = getStoredAppearance();

    updateTheme(currentAppearance || 'system');
};

/**
 * Initializes the theme based on the saved preference or defaults to system.
 * @returns {void}
 */
export function initializeTheme() {
    if (typeof window === 'undefined') {
        return;
    }

    // Initialize theme from saved preference or default to system...
    const savedAppearance = getStoredAppearance();
    updateTheme(savedAppearance || 'system');

    // Set up system theme change listener...
    mediaQuery()?.addEventListener('change', handleSystemThemeChange);
}

/**
 * Returns the appearance state and methods.
 * @returns {Object} The appearance state and methods
 */
export function useAppearance() {
    const appearance = ref<Appearance>('system');

    onMounted(() => {
        initializeTheme();

        const savedAppearance = localStorage.getItem('appearance') as Appearance | null;

        if (savedAppearance) {
            appearance.value = savedAppearance;
        }
    });

    /**
     * Updates the appearance preference.
     * @param {Appearance} value - The appearance preference ('light', 'dark', or 'system')
     */
    function updateAppearance(value: Appearance) {
        appearance.value = value;

        // Store in localStorage for client-side persistence...
        localStorage.setItem('appearance', value);

        // Store in cookie for SSR...
        setCookie('appearance', value);

        updateTheme(value);
    }

    /**
     * Returns the appearance state and methods.
     * @returns {Object} The appearance state and methods
     */
    return {
        appearance,
        updateAppearance,
    };
}
