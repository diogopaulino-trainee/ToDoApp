<script setup>
import Checkbox from '@/components/Checkbox.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Email" class="text-white" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full border border-gray-600 bg-gray-800 text-white placeholder-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-400"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Enter your email"
                />

                <InputError class="mt-2 text-red-400" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" class="text-white" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full border border-gray-600 bg-gray-800 text-white placeholder-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-400"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="Enter your password"
                />

                <InputError class="mt-2 text-red-400" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center text-gray-300">
                    <Checkbox name="remember" v-model:checked="form.remember" class="text-blue-500" />
                    <span class="ml-2 text-white">Remember me</span>
                </label>

                <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-blue-400 hover:text-blue-300">
                    Forgot your password?
                </Link>
            </div>

            <div class="flex items-center justify-end">
                <PrimaryButton
                    class="w-full rounded bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
