<script setup lang="ts">
import { computed } from 'vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import type { BreadcrumbItemType } from '@/types';
import { ref } from 'vue';
import Toast from 'primevue/toast';
import { trashWiggle } from '@/stores/wiggleStore';

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
    <div class="min-h-screen flex flex-col bg-gradient-to-r from-blue-900 via-blue-800 to-blue-600 animate-gradient bg-[length:200%_200%] text-gray-100">
        <Toast />
        <Head>
      <meta name="csrf-token" :content="csrfToken" />
      <title>{{ title }}</title>
    </Head>
  
    <!-- FIXED NAVBAR WITH ANIMATED BORDER -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-900 shadow-md">
        <div class="w-full max-w-6xl mx-auto px-4 py-4 flex flex-col md:flex-row md:justify-between items-center relative border-b-4 border-transparent animate-border-b gap-4">
            
            <!-- Logo + Home -->
            <div class="flex items-center gap-2">
            <h1 class="text-3xl md:text-4xl font-extrabold text-blue-600 dark:text-blue-400">
                To-Do App
            </h1>
            <Link :href="route('welcome')" class="hover:scale-110 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 md:w-8 md:h-8 text-blue-500 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.5L12 3l9 6.5V21a1 1 0 0 1-1 1h-6v-6H10v6H4a1 1 0 0 1-1-1V9.5z" />
                </svg>
            </Link>
            </div>

            <!-- Navigation links -->
            <nav class="flex flex-wrap justify-center gap-2 text-lg">
            <Link v-if="authUser" :href="route('tasks.index')" class="px-3 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600">
                Task List
            </Link>
            <Link v-if="authUser" :href="route('dashboard')" class="px-3 py-2 bg-green-500 text-white rounded shadow hover:bg-green-600">
                Dashboard
            </Link>
            <Link v-if="authUser" :href="route('profile.edit')" class="px-3 py-2 bg-green-500 text-white rounded shadow hover:bg-green-600">
                Profile
            </Link>
            <Link
                v-if="authUser"
                :href="route('tasks.recycle')"
                class="px-3 py-2 bg-gray-700 text-white rounded shadow hover:bg-gray-800 flex items-center justify-center"
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
                    class="lucide lucide-trash-2 w-6 h-6 transition transform"
                    :class="{ 'animate-wiggle': trashWiggle }"
                >
                    <path d="M3 6h18"/>
                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                    <line x1="10" x2="10" y1="11" y2="17"/>
                    <line x1="14" x2="14" y1="11" y2="17"/>
                </svg>
            </Link>

            <Link
                v-if="authUser"
                :href="route('logout')"
                method="post"
                as="button"
                class="px-3 py-2 bg-red-500 text-white rounded shadow hover:bg-red-600 flex items-center justify-center"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-circle-power w-7 h-7">
                <path d="M12 7v4"/>
                <path d="M7.998 9.003a5 5 0 1 0 8-.005"/>
                <circle cx="12" cy="12" r="10"/>
                </svg>
            </Link>
            <Link
                v-if="!authUser && route().has('login')"
                :href="route('login')"
                class="px-3 py-2 bg-green-500 text-white rounded shadow hover:bg-green-600"
            >
                Login
            </Link>
            <Link
                v-if="!authUser && route().has('register')"
                :href="route('register')"
                class="px-3 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded shadow"
            >
                Register
            </Link>
            </nav>

            <!-- Bottom animated border -->
            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 via-green-400 to-purple-500 animate-border"></div>
        </div>
    </header>
  
        <!-- SPACER FOR FIXED NAVBAR -->
        <div class="h-[100px]"></div>
    
        <!-- MAIN CONTENT AREA -->
        <main class="flex-grow w-full max-w-5xl mx-auto p-6 mb-24">
            <slot />
        </main>
    
        <!-- FIXED FOOTER WITH ANIMATED TOP BORDER -->
        <footer class="fixed bottom-0 left-0 right-0 z-50 bg-gray-900 text-white py-4 text-center text-sm shadow-inner">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-400 via-blue-400 to-sky-500 animate-border"></div>
            Stay organized with <span class="text-blue-300 font-semibold">To-Do App</span> – Your tasks, your way!
        </footer>
    </div>
  </template>
