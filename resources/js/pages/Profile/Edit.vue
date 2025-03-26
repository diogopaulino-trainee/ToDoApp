<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';

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

        <h2 class="mb-6 mt-4 text-2xl font-bold">Profile Settings</h2>

        <div class="space-y-6">
            <div class="rounded-lg bg-white p-6 shadow-md dark:bg-gray-800">
                <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" class="max-w-xl" />
            </div>

            <div class="rounded-lg bg-white p-6 shadow-md dark:bg-gray-800">
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <div class="rounded-lg bg-white p-6 shadow-md dark:bg-gray-800">
                <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </AppLayout>
</template>
