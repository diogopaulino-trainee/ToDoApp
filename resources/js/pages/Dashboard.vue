<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface AuthUser {
  id: number;
  name: string;
  email: string;
}

interface PageProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
    };
  };
  [key: string]: unknown;
}

const page = usePage<PageProps>();
const user = computed(() => page.props.auth.user);
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout title="Dashboard">
    <template #default>
      <div class="py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
              Welcome back, {{ user?.name || 'User' }}!
            </h2>

            <p class="text-gray-600 dark:text-gray-300 mb-6">
              Here’s a quick overview to get you started.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <!-- Task List -->
              <div class="bg-gray-100 dark:bg-gray-700 p-5 rounded-lg shadow hover:shadow-lg transition">
                <div class="flex items-center gap-2 mb-3 text-blue-600 dark:text-blue-400">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M9 11l3 3L22 4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  <span class="font-semibold">Your To-Do Tasks</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300">Check and manage your pending and completed tasks.</p>
                <Link :href="route('tasks.index')" class="inline-block mt-4 text-sm text-blue-500 hover:underline">Go to Tasks →</Link>
              </div>

              <!-- Profile Settings -->
              <div class="bg-gray-100 dark:bg-gray-700 p-5 rounded-lg shadow hover:shadow-lg transition">
                <div class="flex items-center gap-2 mb-3 text-green-600 dark:text-green-400">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M20 21v-2a4 4 0 0 0-3-3.87" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M4 21v-2a4 4 0 0 1 3-3.87" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="7" r="4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  <span class="font-semibold">Profile Settings</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300">Update your personal info and change your password.</p>
                <Link :href="route('profile.edit')" class="inline-block mt-4 text-sm text-green-500 hover:underline">Edit Profile →</Link>
              </div>

              <!-- Explore App -->
              <div class="bg-gray-100 dark:bg-gray-700 p-5 rounded-lg shadow hover:shadow-lg transition">
                <div class="flex items-center gap-2 mb-3 text-purple-600 dark:text-purple-400">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M3 4a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v2H3V4z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3 10h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V10z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  <span class="font-semibold">Explore Features</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300">Discover how to maximize your productivity with To-Do App.</p>
                <Link :href="route('welcome')" class="inline-block mt-4 text-sm text-purple-500 hover:underline">Learn More →</Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </AppLayout>
</template>
