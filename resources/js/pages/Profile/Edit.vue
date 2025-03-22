<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <AppLayout title="Profile">
        <Head title="Profile" />

        <h2 class="text-2xl font-bold mb-6 mt-4">
            Profile Settings
        </h2>

        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 p-6 shadow-md rounded-lg">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 shadow-md rounded-lg">
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 shadow-md rounded-lg">
                <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </AppLayout>
</template>
