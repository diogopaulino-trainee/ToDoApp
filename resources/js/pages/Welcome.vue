<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <AppLayout title="Welcome">
        <Head title="To-Do App" />

        <!-- Hero Section -->
        <section class="w-full max-w-5xl text-center py-12 mx-auto">
            <h2 class="text-4xl font-bold text-gray-900 dark:text-gray-100">Manage Your Tasks with Ease</h2>
            <p class="mt-3 text-lg text-gray-600 dark:text-gray-300">
                A simple and efficient task manager to help you stay organized.
            </p>
            <Link v-if="canLogin" :href="route('tasks.index')" class="mt-6 inline-block px-6 py-3 bg-blue-500 text-white font-bold rounded-lg shadow hover:bg-blue-600">
                Start Managing Tasks
            </Link>
        </section>

        <!-- Features Section -->
        <section class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-3 gap-6 py-12 mx-auto">
            <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md text-center">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Task Management</h3>
                <p class="mt-2 text-gray-600 dark:text-gray-300">Easily add, edit, and delete tasks.</p>
            </div>
            <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md text-center">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Prioritization</h3>
                <p class="mt-2 text-gray-600 dark:text-gray-300">Set task priorities: High, Medium, or Low.</p>
            </div>
            <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md text-center">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Filtering</h3>
                <p class="mt-2 text-gray-600 dark:text-gray-300">Sort tasks by status and due date.</p>
            </div>
        </section>
    </AppLayout>
</template>
