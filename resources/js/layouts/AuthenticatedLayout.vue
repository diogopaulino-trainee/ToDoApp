<script setup>
import ApplicationLogo from '@/components/ApplicationLogo.vue';
import Dropdown from '@/components/Dropdown.vue';
import DropdownLink from '@/components/DropdownLink.vue';
import NavLink from '@/components/NavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const showingNavigationDropdown = ref(false);
const page = usePage();
const isAuthenticated = page.props.auth?.user !== null;
</script>

<template>
    <div
        class="flex min-h-screen flex-col bg-gradient-to-br from-gray-100 to-gray-200 text-gray-800 dark:from-gray-900 dark:to-gray-800 dark:text-gray-100"
    >
        <!-- Navbar -->
        <nav class="mx-auto mt-4 flex w-full max-w-7xl items-center justify-between rounded-lg bg-white px-6 py-4 shadow-md dark:bg-gray-900">
            <div class="flex items-center space-x-6">
                <!-- Logo -->
                <Link :href="route('dashboard')" class="flex items-center space-x-2">
                    <ApplicationLogo class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                    <span class="text-xl font-bold text-blue-600 dark:text-blue-400">To-Do App</span>
                </Link>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 md:flex">
                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')"> Dashboard </NavLink>
                    <NavLink :href="route('tasks.index')" :active="route().current('tasks.index')"> Tasks </NavLink>
                </div>
            </div>

            <!-- User Dropdown or Auth Links -->
            <div class="flex items-center space-x-4">
                <template v-if="isAuthenticated">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                type="button"
                                class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-700 focus:outline-none"
                            >
                                {{ page.props.auth.user.name }}
                                <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.08 1.04l-4.25 4.25a.75.75 0 01-1.08 0L5.25 8.27a.75.75 0 01-.02-1.06z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </template>
                        <template #content>
                            <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button" class="text-red-600 hover:text-red-800">
                                Logout
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </template>

                <template v-else>
                    <Link :href="route('login')" class="rounded bg-green-500 px-4 py-2 text-white shadow hover:bg-green-600"> Login </Link>
                    <Link
                        v-if="page.props.canRegister"
                        :href="route('register')"
                        class="rounded bg-orange-500 px-4 py-2 text-white shadow hover:bg-orange-600"
                    >
                        Register
                    </Link>
                </template>
            </div>
        </nav>

        <!-- Header Slot -->
        <header v-if="$slots.header" class="mx-auto w-full max-w-7xl px-6 py-6">
            <slot name="header" />
        </header>

        <!-- Main Content -->
        <main class="mx-auto w-full max-w-7xl flex-grow px-6 pb-10">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="mt-10 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
            Laravel v{{ page.props.laravelVersion }} (PHP v{{ page.props.phpVersion }})
        </footer>
    </div>
</template>
