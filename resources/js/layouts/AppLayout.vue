<script setup lang="ts">
import { computed } from 'vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import type { BreadcrumbItemType } from '@/types';

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
    <div class="min-h-screen flex flex-col bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-900 dark:to-gray-800 text-gray-800 dark:text-gray-100">
        <Head :title="title" />

        <!-- NAVBAR GERAL -->
        <header class="w-full max-w-6xl mx-auto px-6 py-4 flex justify-between items-center bg-white dark:bg-gray-900 shadow-md rounded-lg mt-4">
            <h1 class="text-4xl font-extrabold text-blue-600 dark:text-blue-400">
                To-Do App
            </h1>
            <nav class="flex space-x-4">
                <Link :href="route('welcome')" class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600">
                    Home
                </Link>
                <Link v-if="authUser" :href="route('tasks.index')" class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600">
                    Task List
                </Link>
                <!-- <Link v-if="authUser" :href="route('dashboard')" class="px-4 py-2 bg-green-500 text-white rounded shadow hover:bg-green-600">
                    Dashboard
                </Link> -->
                <template v-if="authUser">
                    <Link :href="route('profile.edit')" class="px-4 py-2 bg-green-500 text-white rounded shadow hover:bg-green-600">
                        Profile
                    </Link>
                    <Link :href="route('logout')" method="post" as="button" class="px-4 py-2 bg-red-500 text-white rounded shadow hover:bg-red-600">
                        Logout
                    </Link>
                </template>
                <template v-else>
                    <Link v-if="route().has('login')" :href="route('login')" class="px-4 py-2 bg-green-500 text-white rounded shadow hover:bg-green-600">
                        Login
                    </Link>
                    <Link v-if="route().has('register')" :href="route('register')" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded shadow">
                        Register
                    </Link>
                </template>
            </nav>
        </header>

        <!-- CONTEÚDO PRINCIPAL -->
        <main class="flex-grow w-full max-w-5xl mx-auto p-6">
            <slot />
        </main>

        <!-- FOOTER -->
        <footer class="py-6 text-center text-sm text-gray-500 dark:text-gray-400 mt-10">
            Stay organized with <span class="text-blue-500">To-Do App</span> – Your tasks, your way!
        </footer>
    </div>
</template>
