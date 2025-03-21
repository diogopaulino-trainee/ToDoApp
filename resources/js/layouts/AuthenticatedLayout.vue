<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/components/ApplicationLogo.vue';
import Dropdown from '@/components/Dropdown.vue';
import DropdownLink from '@/components/DropdownLink.vue';
import NavLink from '@/components/NavLink.vue';
import ResponsiveNavLink from '@/components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const page = usePage();
const isAuthenticated = page.props.auth?.user !== null;
</script>

<template>
    <div class="min-h-screen flex flex-col bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-900 dark:to-gray-800 text-gray-800 dark:text-gray-100">
        <!-- Navbar -->
        <nav class="w-full max-w-7xl mx-auto px-6 py-4 flex justify-between items-center bg-white dark:bg-gray-900 shadow-md rounded-lg mt-4">
            <div class="flex items-center space-x-6">
                <!-- Logo -->
                <Link :href="route('dashboard')" class="flex items-center space-x-2">
                    <ApplicationLogo class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                    <span class="text-xl font-bold text-blue-600 dark:text-blue-400">To-Do App</span>
                </Link>

                <!-- Navigation Links -->
                <div class="hidden md:flex space-x-4">
                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                        Dashboard
                    </NavLink>
                    <NavLink :href="route('tasks.index')" :active="route().current('tasks.index')">
                        Tasks
                    </NavLink>
                </div>
            </div>

            <!-- User Dropdown or Auth Links -->
            <div class="flex items-center space-x-4">
                <template v-if="isAuthenticated">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                type="button"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-sm hover:bg-gray-700 focus:outline-none"
                            >
                                {{ page.props.auth.user.name }}
                                <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.08 1.04l-4.25 4.25a.75.75 0 01-1.08 0L5.25 8.27a.75.75 0 01-.02-1.06z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </template>
                        <template #content>
                            <DropdownLink :href="route('profile.edit')">
                                Profile
                            </DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button" class="text-red-600 hover:text-red-800">
                                Logout
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </template>

                <template v-else>
                    <Link :href="route('login')" class="px-4 py-2 bg-green-500 text-white rounded shadow hover:bg-green-600">
                        Login
                    </Link>
                    <Link v-if="page.props.canRegister" :href="route('register')" class="px-4 py-2 bg-orange-500 text-white rounded shadow hover:bg-orange-600">
                        Register
                    </Link>
                </template>
            </div>
        </nav>

        <!-- Header Slot -->
        <header v-if="$slots.header" class="w-full max-w-7xl mx-auto px-6 py-6">
            <slot name="header" />
        </header>

        <!-- Main Content -->
        <main class="w-full max-w-7xl mx-auto px-6 pb-10 flex-grow">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="py-6 text-center text-sm text-gray-500 dark:text-gray-400 mt-10">
            Laravel v{{ page.props.laravelVersion }} (PHP v{{ page.props.phpVersion }})
        </footer>
    </div>
</template>
