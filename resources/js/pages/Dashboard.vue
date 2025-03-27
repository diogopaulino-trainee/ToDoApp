<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import confetti from 'canvas-confetti';
import { BarElement, CategoryScale, Chart as ChartJS, Legend, LinearScale, Title, Tooltip } from 'chart.js';
import { computed, onMounted } from 'vue';
import { Bar } from 'vue-chartjs';

/**
 * Register the ChartJS components.
 */
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

/**
 * Define the page.
 */
const page = usePage<{
    completedCount: number;
    pendingCount: number;
    currentLevel: { id: number; name: string; description: string } | null;
    levels: { id: number; name: string; description: string; required_tasks: number }[];
    showConfetti: boolean;
    auth: { user: AuthUser };
}>();

/**
 * Define the user.
 */
const user = computed(() => page.props.auth.user);

/**
 * Define the completed tasks.
 */
const completedTasks = computed(() => page.props.completedCount);

/**
 * Define the pending tasks.
 */
const pendingTasks = computed(() => page.props.pendingCount);

/**
 * Define the chart data.
 */
const chartData = computed(() => ({
    labels: ['Completed', 'Pending'],
    datasets: [
        {
            label: 'Tasks',
            data: [completedTasks.value, pendingTasks.value],
            backgroundColor: ['#10B981', '#3B82F6'],
            borderRadius: 8,
            barThickness: 30,
        },
    ],
}));

/**
 * Define the chart options.
 */
const chartOptions = {
    indexAxis: 'y' as const,
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        x: {
            beginAtZero: true,
            ticks: {
                color: '#d1d5db',
            },
        },
        y: {
            ticks: {
                color: '#d1d5db',
            },
        },
    },
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            enabled: true,
        },
    },
};

/**
 * Define the AuthUser interface.
 */
interface AuthUser {
    id: number;
    name: string;
    email: string;
}

/**
 * Define the PageProps interface.
 */
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

onMounted(() => {
    if (page.props.showConfetti) {
        confetti({
            particleCount: 150,
            spread: 100,
            origin: { y: 0.6 },
        });
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout title="Dashboard">
        <template #default>
            <div class="py-12">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="bg-white p-6 shadow-xl sm:rounded-lg dark:bg-gray-800">
                        <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">Welcome back, {{ user?.name || 'User' }}!</h2>

                        <p class="mb-6 text-gray-600 dark:text-gray-300">Here’s a quick overview to get you started.</p>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <!-- Task List -->
                            <div class="rounded-lg bg-gray-100 p-5 shadow transition hover:shadow-lg dark:bg-gray-700">
                                <div class="mb-3 flex items-center gap-2 text-blue-600 dark:text-blue-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path d="M9 11l3 3L22 4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                    <span class="font-semibold">Your To-Do Tasks</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Check and manage your pending and completed tasks.</p>
                                <Link :href="route('tasks.index')" class="mt-4 inline-block text-sm text-blue-500 hover:underline"
                                    >Go to Tasks →</Link
                                >
                            </div>

                            <!-- Profile Settings -->
                            <div class="rounded-lg bg-gray-100 p-5 shadow transition hover:shadow-lg dark:bg-gray-700">
                                <div class="mb-3 flex items-center gap-2 text-green-600 dark:text-green-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path d="M20 21v-2a4 4 0 0 0-3-3.87" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M4 21v-2a4 4 0 0 1 3-3.87" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <circle cx="12" cy="7" r="4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span class="font-semibold">Profile Settings</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Update your personal info and change your password.</p>
                                <Link :href="route('profile.edit')" class="mt-4 inline-block text-sm text-green-500 hover:underline"
                                    >Edit Profile →</Link
                                >
                            </div>

                            <!-- Explore App -->
                            <div class="rounded-lg bg-gray-100 p-5 shadow transition hover:shadow-lg dark:bg-gray-700">
                                <div class="mb-3 flex items-center gap-2 text-purple-600 dark:text-purple-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path
                                            d="M3 4a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v2H3V4z"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                        <path
                                            d="M3 10h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V10z"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                    <span class="font-semibold">Explore Features</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Discover how to maximize your productivity with To-Do App.</p>
                                <Link :href="route('welcome')" class="mt-4 inline-block text-sm text-purple-500 hover:underline">Learn More →</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Chart Card -->
            <div class="rounded-lg bg-gray-100 p-5 shadow transition hover:shadow-lg dark:bg-gray-800">
                <div class="mb-3 flex items-center gap-2 text-indigo-600 dark:text-indigo-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M11 11V7a4 4 0 018 0v4m-8 4v4a4 4 0 01-8 0v-4m4-6h.01"
                        />
                    </svg>
                    <span class="font-semibold">Task Summary</span>
                </div>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">Overview of your current tasks.</p>

                <div class="relative h-[220px] w-full">
                    <Bar :data="chartData" :options="chartOptions" />
                </div>
            </div>
            <!-- Levels Progress -->
            <div class="mt-10 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <div class="mb-3 flex items-center gap-2 text-yellow-600 dark:text-yellow-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a10 10 0 1010 10A10 10 0 0012 2z" />
                    </svg>
                    <span class="font-semibold">Your Level Progress</span>
                </div>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">Advance levels by completing more tasks. Keep the momentum going!</p>

                <div class="flex flex-col gap-4">
                    <div
                        v-for="level in page.props.levels"
                        :key="level.id"
                        class="relative flex flex-col items-center justify-center rounded-lg border p-4 text-center transition-all duration-300"
                        :class="{
                            'scale-105 border-green-600 bg-green-200 font-bold text-black dark:border-green-400 dark:bg-green-700 dark:text-white':
                                page.props.currentLevel?.id === level.id,
                            'border-gray-400 bg-gray-100 text-gray-400 dark:border-gray-600 dark:bg-gray-700':
                                (page.props.currentLevel?.id || 0) < level.id,
                        }"
                    >
                        <div
                            v-if="(page.props.currentLevel?.id || 0) < level.id"
                            class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-black/40 backdrop-blur-sm"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-white opacity-80"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 11c-1.1 0-2 .9-2 2v1c0 .6.4 1 1 1h2c.6 0 1-.4 1-1v-1c0-1.1-.9-2-2-2zM17 8V7a5 5 0 00-10 0v1M5 8h14v12H5z"
                                />
                            </svg>
                        </div>

                        <span class="z-20 text-xl">{{ level.name }}</span>
                        <small class="z-20 mt-1 text-sm">{{ level.description }}</small>
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
</template>
