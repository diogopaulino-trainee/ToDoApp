import { ref } from 'vue';

export const trashWiggle = ref(false);

export const triggerTrashWiggle = () => {
  trashWiggle.value = true;
  setTimeout(() => {
    trashWiggle.value = false;
  }, 1500);
};