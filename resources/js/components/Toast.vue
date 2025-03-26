<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    message: String,
    type: {
        type: String,
        default: 'success',
    },
});

const showToast = ref(false);

watch(
    () => props.message,
    (newVal) => {
        if (newVal) {
            showToast.value = true;
            setTimeout(() => {
                showToast.value = false;
            }, 3000);
        }
    },
);
</script>

<template>
    <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 transform -translate-y-4"
        enter-to-class="opacity-100 transform translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="showToast"
            class="fixed right-5 top-5 rounded px-4 py-2 shadow-lg"
            :class="type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'"
        >
            {{ message }}
        </div>
    </transition>
</template>
