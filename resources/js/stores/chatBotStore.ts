import {
    badWordsReplies,
    commandReplies,
    fallbackReplies,
    motivationalQuotes,
    productivityTips,
    sarcasticBotReplies,
    welcomeMessages,
} from '@/utils/quotes';
import { defineStore } from 'pinia';
import { ref, watch } from 'vue';

/**
 * Helper: Returns a random item from an array, ensuring it's not the same as last time.
 * @param list Array of items
 * @param last Last returned item
 * @returns A different random item from the list
 */
function getRandomWithNoRepeat<T>(list: T[], last: T | null): T {
    if (list.length === 1) return list[0];
    let item: T;
    do {
        item = list[Math.floor(Math.random() * list.length)];
    } while (item === last);
    return item;
}

/**
 * ChatBot Pinia Store
 *
 * This store manages the AI assistant chatbot:
 * - Opening/closing the chat
 * - Sending user messages and bot responses
 * - Auto-messaging mode with motivational quotes
 * - Handling commands (dashboard, profile, tasks, recycle)
 * - Responding to bad words, jokes, tips, help requests, and unknown inputs
 * - Avoids sending the same command response twice in a row
 */
export const useChatBotStore = defineStore('chatBot', () => {
    const isOpen = ref(false);

    const messages = ref<{ sender: 'bot' | 'user'; text: string; route?: string }[]>([]);
    const autoMode = ref(getInitialAutoMode());

    // Memory for last used replies per command
    let lastTaskReply: { text: string; route: string } | null = null;
    let lastDashboardReply: { text: string; route: string } | null = null;
    let lastProfileReply: { text: string; route: string } | null = null;
    let lastRecycleReply: { text: string; route: string } | null = null;

    /**
     * Retrieves the initial state of the auto messaging mode from localStorage.
     * Defaults to `true` if no value is found.
     *
     * @returns {boolean} Whether auto mode should be enabled.
     */
    function getInitialAutoMode(): boolean {
        const stored = localStorage.getItem('autoMode');
        return stored !== null ? stored === 'true' : true;
    }

    /**
     * Saves the current value of `autoMode` to localStorage
     * so the user's preference persists across sessions.
     */
    function saveAutoMode() {
        localStorage.setItem('autoMode', autoMode.value.toString());
    }

    /**
     * Toggles the `autoMode` value between true and false,
     * and persists the new value in localStorage.
     */
    function toggleAutoMode() {
        autoMode.value = !autoMode.value;
        saveAutoMode();
    }

    /**
     * Opens the chatbot window if it's not already open,
     * and pushes a randomized welcome message to the chat.
     */
    function openChat() {
        if (!isOpen.value) {
            isOpen.value = true;
            const welcome = getRandomWithNoRepeat(welcomeMessages, null);
            messages.value.push({ sender: 'bot', text: welcome });
        }
    }

    /**
     * Closes the chatbot window.
     */
    function closeChat() {
        isOpen.value = false;
    }

    /**
     * Appends a user message to the message list and
     * triggers the bot to respond after a short delay.
     *
     * @param {string} text - The user's input message.
     */
    function sendUserMessage(text: string) {
        messages.value.push({ sender: 'user', text });
        setTimeout(() => {
            respondToUser(text);
        }, 500);
    }

    /**
     * Stores an optional callback function that is executed
     * after the bot sends a response (e.g., for scrolling behavior).
     *
     * @param {() => void} cb - The callback function.
     */
    let onBotResponded: (() => void) | null = null;

    function setBotResponseCallback(cb: () => void) {
        onBotResponded = cb;
    }

    /**
     * Responds to user input with logic for commands, jokes, bad words, etc.
     * Avoids repeating the last command message if multiple options exist.
     */
    function respondToUser(text: string) {
        const lower = text.toLowerCase();
        let reply = '';
        let route: string | null = null;

        const badWordsList = ['damn', 'hell', 'shit', 'fuck', 'crap', 'bitch', 'cunt'];

        if (badWordsList.some((word) => lower.includes(word))) {
            reply = getRandomWithNoRepeat(badWordsReplies, null);
        } else if (lower.includes('dashboard')) {
            const response = getRandomWithNoRepeat(commandReplies.dashboard, lastDashboardReply);
            reply = response.text;
            route = response.route;
            lastDashboardReply = response;
        } else if (lower.includes('profile')) {
            const response = getRandomWithNoRepeat(commandReplies.profile, lastProfileReply);
            reply = response.text;
            route = response.route;
            lastProfileReply = response;
        } else if (lower.includes('tasks')) {
            const response = getRandomWithNoRepeat(commandReplies.tasks, lastTaskReply);
            reply = response.text;
            route = response.route;
            lastTaskReply = response;
        } else if (lower.includes('recycle')) {
            const response = getRandomWithNoRepeat(commandReplies.recycle, lastRecycleReply);
            reply = response.text;
            route = response.route;
            lastRecycleReply = response;
        } else if (lower.includes('hello') || lower.includes('hi')) {
            reply = 'Hey friend! Let’s smash those goals today! 💪';
        } else if (lower.includes('help')) {
            reply = getRandomWithNoRepeat(productivityTips, null);
        } else if (lower.includes('motivate') || lower.includes('boost')) {
            reply = getRandomWithNoRepeat(motivationalQuotes, null);
        } else if (lower.includes('tip')) {
            reply = getRandomWithNoRepeat(productivityTips, null);
        } else if (lower.includes('joke') || lower.includes('funny')) {
            reply = getRandomWithNoRepeat(sarcasticBotReplies, null);
        } else {
            reply = getRandomWithNoRepeat(fallbackReplies, null);
        }

        messages.value.push({ sender: 'bot', text: reply, route: route ?? undefined });
        onBotResponded?.();
    }

    /**
     * Sends a motivational quote every 5 minutes if autoMode is enabled.
     */
    function autoSendQuote() {
        if (!autoMode.value) return;
        const quote = getRandomWithNoRepeat(motivationalQuotes, null);
        messages.value.push({ sender: 'bot', text: quote });
        onBotResponded?.();
    }

    setInterval(
        () => {
            if (autoMode.value) {
                if (!isOpen.value) openChat();
                autoSendQuote();
            }
        },
        5 * 60 * 1000,
    );

    watch(autoMode, saveAutoMode);

    return {
        isOpen,
        messages,
        openChat,
        closeChat,
        sendUserMessage,
        setBotResponseCallback,
        toggleAutoMode,
        autoMode,
    };
});
