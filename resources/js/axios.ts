import axios from 'axios';

/**
 * Set the default headers for the axios instance.
 */
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Get the CSRF token from the meta tag.
 */
const token = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement;
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

/**
 * Export the axios instance.
 */
export default axios;
