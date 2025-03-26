<script setup>
import DangerButton from '@/components/DangerButton.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import Modal from '@/components/Modal.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Delete Account</h2>

            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download
                any data or information that you wish to retain.
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion">Delete Account</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="rounded-lg bg-white p-6 shadow-md dark:bg-gray-800">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Are you sure you want to delete your account?</h2>

                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                    Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you
                    would like to permanently delete your account.
                </p>

                <div class="mt-6">
                    <InputLabel for="password" value="Password" class="sr-only" />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        placeholder="Password"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2 text-red-500" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton
                        class="bg-gray-300 text-gray-900 hover:bg-gray-400 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
                        @click="closeModal"
                    >
                        Cancel
                    </SecondaryButton>

                    <DangerButton
                        class="ms-3 bg-red-600 text-white hover:bg-red-700"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Delete Account
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
