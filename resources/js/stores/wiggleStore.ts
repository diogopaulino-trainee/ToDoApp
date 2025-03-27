/**
 * Store for managing trash icon wiggle animation state
 * @module wiggleStore
 */

import { ref } from 'vue';

/**
 * Reactive reference to control trash icon wiggle animation state
 * @type {import('vue').Ref<boolean>}
 */
export const trashWiggle = ref(false);

/**
 * Triggers a wiggle animation on the trash icon
 * Sets the wiggle state to true and automatically resets it after 1.5 seconds
 * @function
 */
export const triggerTrashWiggle = () => {
    trashWiggle.value = true;
    setTimeout(() => {
        trashWiggle.value = false;
    }, 1500);
};
