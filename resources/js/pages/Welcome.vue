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
      <section class="w-full max-w-5xl text-center pt-8 pb-12 mx-auto">
        <h2 class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">Boost your productivity</h2>
        <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
          Create your account and start organizing your life with our efficient and intuitive task manager.
        </p>
        <Link
          v-if="canRegister"
          :href="route('register')"
          class="mt-8 text-lg inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold rounded-lg shadow hover:from-blue-600 hover:to-indigo-700 transition"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
               stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="8.5" cy="7" r="4"/>
            <line x1="20" y1="8" x2="20" y2="14"/>
            <line x1="23" y1="11" x2="17" y2="11"/>
          </svg>
          Sign Up Now
        </Link>
        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
          Already have an account?
          <Link :href="route('login')" class="text-blue-400 hover:underline">Log in</Link>
        </p>
      </section>
  
      <!-- Features Section -->
        <section class="w-full max-w-5xl mx-auto py-12 space-y-6">
            <!-- Primeira linha: 3 cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-blue-500 mb-3" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M9 11l3 3L22 4"/>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Easy Task Management</h3>
                <p class="mt-2 text-gray-600 dark:text-gray-300">Create, edit and manage tasks with a simple and clean interface.</p>
                </div>

                <!-- Card 2 -->
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-yellow-500 mb-3" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 6v6l4 2"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Due Date Reminders</h3>
                <p class="mt-2 text-gray-600 dark:text-gray-300">Never forget a task. Set deadlines and stay on track.</p>
                </div>

                <!-- Card 3 -->
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow text-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-green-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4" />
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" fill="none" />
                  </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Mark as Completed</h3>
                <p class="mt-2 text-gray-600 dark:text-gray-300">Click to mark tasks as done. Visualize your progress clearly.</p>
                </div>
            </div>

            <!-- Segunda linha: 2 cards ao centro -->
            <div class="flex flex-col md:flex-row justify-center gap-6">
                <!-- Card 4 -->
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow text-center w-full md:w-80">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-purple-500 mb-3" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M3 6h18"/>
                    <path d="M3 12h18"/>
                    <path d="M3 18h18"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Filter & Sort</h3>
                <p class="mt-2 text-gray-600 dark:text-gray-300">Quickly find what you need with powerful filters and sorting.</p>
                </div>

                <!-- Card 5 -->
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow text-center w-full md:w-80">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-pink-500 mb-3" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 8v4l3 3"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Get Started in Seconds</h3>
                <p class="mt-2 text-gray-600 dark:text-gray-300">Sign up is quick and easy. Start managing your time better today.</p>
                </div>
            </div>
        </section>
    </AppLayout>
  </template>
  
