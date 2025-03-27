<script setup lang="ts">
/**
 * Importing required modules and utilities:
 * - useChatBotStore: Pinia store for chat state and logic
 * - ref, lifecycle hooks: for reactivity and DOM interactions
 * - Link: Inertia.js component for SPA-style routing
 */
import { useChatBotStore } from '@/stores/chatBotStore';
import { Link } from '@inertiajs/vue3';
import { nextTick, onBeforeUnmount, onMounted, ref } from 'vue';

// Local reactive state for user message input
const store = useChatBotStore();
const newMessage = ref('');

/**
 * Submits a new user message to the chatbot store.
 * Clears the input field and scrolls chat to the bottom.
 */
function submit() {
    if (newMessage.value.trim() === '') return;
    store.sendUserMessage(newMessage.value.trim());
    newMessage.value = '';
    scrollToBottom();
}

/**
 * Closes the chat window when the user clicks outside the chat box,
 * but not if clicking the open button again.
 */
function handleClickOutside(event: MouseEvent) {
    if (!store.isOpen) return;

    const chatBox = document.getElementById('chat-box');
    const openButton = document.querySelector('[title="Open Assistant"]');

    if (chatBox && !chatBox.contains(event.target as Node) && !(openButton && openButton.contains(event.target as Node))) {
        store.closeChat();
    }
}

/**
 * Smooth scroll to the bottom of the chat window to show latest message.
 */
function scrollToBottom() {
    nextTick(() => {
        const chatWindow = document.getElementById('chat-window');
        chatWindow?.scrollTo({ top: chatWindow.scrollHeight, behavior: 'smooth' });
    });
}

/**
 * Toggles the auto message mode (motivational quotes every few minutes).
 */
const toggleAuto = () => {
    store.toggleAutoMode();
};

/**
 * Setup event listeners when component mounts:
 * - click outside detection
 * - auto scroll when bot sends new message
 */
onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    store.setBotResponseCallback(() => {
        scrollToBottom();
    });
});

/**
 * Cleanup event listeners when component unmounts.
 */
onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div>
        <!-- Floating chat button to open assistant -->
        <button
            @click.stop="store.openChat"
            v-if="!store.isOpen"
            class="fixed bottom-20 right-6 h-14 w-14 transform rounded-full bg-blue-600 text-white shadow-lg ring-1 ring-blue-500 ring-offset-2 ring-offset-blue-900 transition hover:scale-105 hover:bg-blue-700"
            title="Open Assistant"
        >
            💬
        </button>

        <!-- Main chat box -->
        <div
            v-if="store.isOpen"
            id="chat-box"
            class="fixed bottom-20 right-6 w-80 rounded-lg border border-gray-300 bg-white text-gray-900 shadow-lg"
        >
            <!-- Chat header with title, toggle, help, and close -->
            <div class="relative flex items-center justify-between rounded-t-lg bg-blue-600 p-3 font-bold text-white">
                <div class="flex items-center gap-2">
                    <span>AI Assistant</span>

                    <!-- Toggle auto mode button (bell icon) -->
                    <button
                        @click.stop="toggleAuto"
                        :title="store.autoMode ? 'Disable auto quotes' : 'Enable auto quotes'"
                        class="text-white transition hover:opacity-80"
                    >
                        <!-- Bell icon (active) -->
                        <svg
                            v-if="store.autoMode"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-bell-icon lucide-bell"
                        >
                            <path d="M10.268 21a2 2 0 0 0 3.464 0" />
                            <path
                                d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"
                            />
                        </svg>

                        <!-- Bell off icon (inactive) -->
                        <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-bell-off-icon lucide-bell-off"
                        >
                            <path d="M10.268 21a2 2 0 0 0 3.464 0" />
                            <path d="M17 17H4a1 1 0 0 1-.74-1.673C4.59 13.956 6 12.499 6 8a6 6 0 0 1 .258-1.742" />
                            <path d="m2 2 20 20" />
                            <path d="M8.668 3.01A6 6 0 0 1 18 8c0 2.687.77 4.653 1.707 6.05" />
                        </svg>
                    </button>
                </div>

                <!-- Tooltip and close chat -->
                <div class="flex items-center gap-2">
                    <!-- Help tooltip with suggestions -->
                    <div class="group relative">
                        <button
                            class="flex h-6 w-6 items-center justify-center rounded-full bg-white text-xs font-bold text-blue-600 transition hover:bg-blue-100"
                        >
                            ?
                        </button>
                        <div
                            class="pointer-events-none absolute right-0 top-8 z-50 w-52 rounded bg-white p-3 text-sm text-gray-800 opacity-0 shadow-lg transition-opacity duration-300 group-hover:pointer-events-auto group-hover:opacity-100"
                        >
                            <p class="mb-1 font-semibold text-blue-600">Try saying:</p>
                            <ul class="list-inside list-disc space-y-1">
                                <li><code>"hello"</code></li>
                                <li><code>"motivate"</code></li>
                                <li><code>"tip"</code></li>
                                <li><code>"help"</code></li>
                                <li><code>"joke"</code></li>
                                <li><code>"dashboard"</code></li>
                                <li><code>"profile"</code></li>
                                <li><code>"tasks"</code></li>
                                <li><code>"recycle"</code></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Close chat button -->
                    <button @click="store.closeChat" title="Close" class="text-lg leading-none text-white hover:opacity-75">&times;</button>
                </div>
            </div>

            <!-- Message list display -->
            <div id="chat-window" class="max-h-[520px] min-h-[300px] space-y-2 overflow-y-auto p-3">
                <div v-for="(msg, index) in store.messages" :key="index">
                    <div :class="msg.sender === 'bot' ? 'text-left' : 'text-right'" class="text-sm">
                        <!-- Normal text message -->
                        <span
                            v-if="!msg.route"
                            :class="msg.sender === 'bot' ? 'bg-gray-100 text-gray-800' : 'bg-blue-500 text-white'"
                            class="inline-block max-w-[75%] rounded-lg px-3 py-2"
                        >
                            {{ msg.text }}
                        </span>

                        <!-- Route message (as link) -->
                        <Link
                            v-else
                            :href="msg.route"
                            class="inline-block max-w-[75%] rounded-lg bg-blue-100 px-3 py-2 text-blue-800 hover:underline"
                        >
                            {{ msg.text }}
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Message input bar -->
            <form @submit.prevent="submit" class="flex border-t p-2">
                <input v-model="newMessage" type="text" class="flex-1 rounded border px-2 py-1 text-sm" placeholder="Ask me anything..." />
                <button type="submit" class="ml-2 rounded bg-blue-500 px-3 py-1 text-sm text-white hover:bg-blue-600">Send</button>
            </form>
        </div>
    </div>
</template>
