import type { Directive } from 'vue';

/**
 * Directive to focus on an element when it is mounted.
 * @param {HTMLElement} el - The element to focus on
 */
const vFocus: Directive = {
    mounted(el: HTMLElement) {
        el.focus();
    },
};

/**
 * Export the vFocus directive.
 * @returns {Directive} The vFocus directive
 */
export default vFocus;
