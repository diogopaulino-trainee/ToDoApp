<script setup lang="ts">
import ChatBot from '@/components/ChatBot.vue';
import { trashWiggle } from '@/stores/wiggleStore';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import Toast from 'primevue/toast';
import { computed } from 'vue';

const triggerTrashWiggle = () => {
    trashWiggle.value = true;
    setTimeout(() => {
        trashWiggle.value = false;
    }, 1000);
};

const csrfToken = usePage().props.csrf_token as string;

interface Props {
    title?: string;
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    title: 'App',
    breadcrumbs: () => [],
});

interface AuthUser {
    id: number;
    name: string;
    email: string;
}

interface InertiaProps {
    auth?: {
        user?: AuthUser;
    };
    [key: string]: any;
}

const page = usePage<InertiaProps>();
const authUser = computed(() => page.props.auth?.user ?? null);
</script>

<template>
    <div
        class="animate-gradient flex min-h-screen flex-col bg-gradient-to-r from-blue-900 via-blue-800 to-blue-600 bg-[length:200%_200%] text-gray-100"
    >
        <Toast />
        <Head>
            <meta name="csrf-token" :content="csrfToken" />
            <title>{{ title }}</title>
        </Head>

        <!-- FIXED NAVBAR WITH ANIMATED BORDER -->
        <header class="fixed left-0 right-0 top-0 z-50 bg-white shadow-md dark:bg-gray-900">
            <div
                class="animate-border-b relative mx-auto flex w-full max-w-6xl flex-col items-center gap-4 border-b-4 border-transparent px-4 py-4 md:flex-row md:justify-between"
            >
                <!-- Logo + Home -->
                <div class="flex items-center gap-2">
                    <h1 class="text-3xl font-extrabold text-blue-600 md:text-4xl dark:text-blue-400">To-Do App</h1>
                    <Link :href="route('welcome')" class="transition hover:scale-110">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-7 w-7 text-blue-500 md:h-8 md:w-8 dark:text-blue-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 9.5L12 3l9 6.5V21a1 1 0 0 1-1 1h-6v-6H10v6H4a1 1 0 0 1-1-1V9.5z"
                            />
                        </svg>
                    </Link>
                </div>

                <!-- Navigation links -->
                <nav class="flex flex-wrap justify-center gap-2 text-lg">
                    <Link v-if="authUser" :href="route('tasks.index')" class="rounded bg-blue-500 px-3 py-2 text-white shadow hover:bg-blue-600">
                        Task List
                    </Link>
                    <Link v-if="authUser" :href="route('dashboard')" class="rounded bg-green-500 px-3 py-2 text-white shadow hover:bg-green-600">
                        Dashboard
                    </Link>
                    <Link v-if="authUser" :href="route('profile.edit')" class="rounded bg-green-500 px-3 py-2 text-white shadow hover:bg-green-600">
                        Profile
                    </Link>
                    <Link
                        v-if="authUser"
                        :href="route('tasks.recycle')"
                        class="flex items-center justify-center rounded bg-gray-700 px-3 py-2 text-white shadow hover:bg-gray-800"
                        title="Recycle Bin"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-trash-2 h-6 w-6 transform transition"
                            :class="{ 'animate-wiggle': trashWiggle }"
                        >
                            <path d="M3 6h18" />
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                            <line x1="10" x2="10" y1="11" y2="17" />
                            <line x1="14" x2="14" y1="11" y2="17" />
                        </svg>
                    </Link>

                    <Link
                        v-if="authUser"
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="flex items-center justify-center rounded bg-red-500 px-3 py-2 text-white shadow hover:bg-red-600"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="28"
                            height="28"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-circle-power h-7 w-7"
                        >
                            <path d="M12 7v4" />
                            <path d="M7.998 9.003a5 5 0 1 0 8-.005" />
                            <circle cx="12" cy="12" r="10" />
                        </svg>
                    </Link>
                    <Link
                        v-if="!authUser && route().has('login')"
                        :href="route('login')"
                        class="rounded bg-green-500 px-3 py-2 text-white shadow hover:bg-green-600"
                    >
                        Login
                    </Link>
                    <Link
                        v-if="!authUser && route().has('register')"
                        :href="route('register')"
                        class="rounded bg-orange-500 px-3 py-2 text-white shadow hover:bg-orange-600"
                    >
                        Register
                    </Link>
                </nav>

                <!-- Bottom animated border -->
                <div class="animate-border absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-blue-400 via-green-400 to-purple-500"></div>
            </div>
        </header>

        <!-- SPACER FOR FIXED NAVBAR -->
        <div class="h-[100px]"></div>

        <!-- MAIN CONTENT AREA -->
        <main class="mx-auto mb-24 w-full max-w-5xl flex-grow p-6">
            <ChatBot />
            <slot />
        </main>

        <!-- FIXED FOOTER WITH ANIMATED TOP BORDER -->
        <footer class="fixed bottom-0 left-0 right-0 z-50 bg-gray-900 py-4 text-center text-sm text-white shadow-inner">
            <div class="animate-border absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-indigo-400 via-blue-400 to-sky-500"></div>
            Stay organized with <span class="font-semibold text-blue-300">To-Do App</span> – Your tasks, your way!
        </footer>
    </div>
</template>
