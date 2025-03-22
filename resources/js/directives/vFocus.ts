import type { Directive } from 'vue';

const vFocus: Directive = {
  mounted(el: HTMLElement) {
    el.focus();
  },
};

export default vFocus;
