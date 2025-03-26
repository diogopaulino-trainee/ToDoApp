<script setup lang="ts">
import { Task } from '@/composables/useTasks';
import AppLayout from '@/layouts/AppLayout.vue';
import { router, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { onMounted, ref, watchEffect } from 'vue';

// Props with task details including subtasks and attachments
const props = defineProps<{
    task: Task & {
        subtasks: { id: number; title: string; completed: boolean }[];
        attachments: { id: number; name: string; url: string }[];
    };
}>();

// Toast for notifications (success/error)
const toast = useToast();

const page = usePage();

interface PageProps {
    flash: {
        success?: string;
        error?: string;
    };
}

const flash = (page.props as unknown as PageProps).flash;

onMounted(() => {
    const successMessage = flash?.success;
    const errorMessage = flash?.error;

    if (successMessage) {
        toast.add({ severity: 'success', summary: 'Success', detail: successMessage, life: 3000 });
    }

    if (errorMessage) {
        toast.add({ severity: 'error', summary: 'Error', detail: errorMessage, life: 3000 });
    }
});

// Refs to handle files, drag state, file input element and upload progress
const files = ref<File[]>([]);
const dragOver = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);
const isUploading = ref(false);
const progress = ref(0);

// Refs to handle files, drag state, file input element and upload progress
const previewFiles = ref<{ name: string; url: string }[]>([]);

// Reset preview and input if no files
watchEffect(() => {
    if (!files.value.length && fileInput.value) {
        fileInput.value.value = '';
        previewFiles.value = [];
    }
});

// Generate local image previews for supported files
const updatePreviews = () => {
    previewFiles.value = files.value
        .filter((file) => file.type.startsWith('image/'))
        .map((file) => ({
            name: file.name,
            url: URL.createObjectURL(file),
        }));
};

// Trigger file input dialog
const openFileDialog = () => {
    fileInput.value?.click();
};

// Prevent duplicate file uploads
const isDuplicate = (file: File) => files.value.some((f) => f.name === file.name && f.size === file.size);

// Handle file selection via input
const handleFileInput = (e: Event) => {
    const input = e.target as HTMLInputElement;
    if (input.files) {
        for (const file of input.files) {
            if (!isDuplicate(file)) files.value.push(file);
        }
        updatePreviews();
        uploadFiles(); // Auto upload on select
    }
};

// Handle file drop into the dropzone
const handleFileDrop = (e: DragEvent) => {
    e.preventDefault();
    dragOver.value = false;
    if (e.dataTransfer?.files) {
        for (const file of e.dataTransfer.files) {
            if (!isDuplicate(file)) files.value.push(file);
        }
        updatePreviews();
        uploadFiles(); // Auto upload on drop
    }
};

// Remove a file from the preview list
const removeFile = (index: number) => {
    files.value.splice(index, 1);
};

// Upload files
const uploadFiles = () => {
    if (!files.value.length || isUploading.value) return;

    const formData = new FormData();
    files.value.forEach((file) => formData.append('files[]', file));

    isUploading.value = true;
    progress.value = 0;

    router.post(route('tasks.attachments.upload', props.task.id), formData, {
        forceFormData: true,
        onProgress: (e) => {
            if (e && e.lengthComputable) {
                progress.value = Math.round((e.loaded * 100) / (e.total ?? 1));
            }
        },
        onSuccess: () => {
            files.value = [];
            progress.value = 0;
            router.visit(route('tasks.show', props.task.id), { preserveScroll: true });
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Upload failed.', life: 3000 });
        },
        onFinish: () => {
            isUploading.value = false;
        },
    });
};

const deleteAttachment = (attachmentId: number) => {
    router.delete(route('tasks.attachments.delete', [props.task.id, attachmentId]), {
        preserveScroll: true,
        onSuccess: () => {
            router.visit(route('tasks.show', props.task.id), { preserveScroll: true });
        },
    });
};
</script>
<template>
    <AppLayout :title="`Task Details - ${props.task.title}`">
        <div class="mx-auto max-w-3xl px-6 py-12">
            <div class="space-y-6 rounded-xl bg-gray-900 p-8 text-white shadow-lg">
                <!-- Title -->
                <div class="flex items-center justify-between">
                    <h2 class="text-3xl font-bold">{{ props.task.title }}</h2>
                    <span
                        class="rounded-full px-3 py-1 text-sm font-semibold"
                        :class="{
                            'bg-red-500 text-white': props.task.priority === 'high',
                            'bg-yellow-500 text-gray-900': props.task.priority === 'medium',
                            'bg-green-500 text-white': props.task.priority === 'low',
                        }"
                    >
                        {{ props.task.priority.toUpperCase() }}
                    </span>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <p class="text-gray-300">
                        <span class="font-semibold">Description:</span>
                        {{ props.task.description || 'No description provided.' }}
                    </p>

                    <p class="flex items-center gap-2 text-gray-300">
                        <svg
                            class="h-5 w-5 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5" />
                            <path d="M16 2v4M8 2v4M3 10h5" />
                            <path d="M17.5 17.5 16 16.3V14" />
                            <circle cx="16" cy="16" r="6" />
                        </svg>
                        <span class="font-semibold">Due Date:</span>
                        {{ props.task.due_datetime ? props.task.due_datetime.replace('T', ' ').slice(0, 16) : 'No deadline' }}
                    </p>
                </div>

                <!-- Subtasks -->
                <div>
                    <h3 class="mb-2 text-xl font-semibold">Subtasks</h3>
                    <ul v-if="props.task.subtasks.length" class="space-y-2">
                        <li
                            v-for="sub in props.task.subtasks"
                            :key="sub.id"
                            class="flex items-center justify-between rounded-md bg-gray-800 px-4 py-2"
                        >
                            <span :class="{ 'text-gray-500 line-through': sub.completed, 'text-white': !sub.completed }">
                                {{ sub.title }}
                            </span>
                            <span v-if="sub.completed" class="text-green-400 transition hover:text-green-500">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M20 6 9 17l-5-5" />
                                </svg>
                            </span>
                        </li>
                    </ul>
                    <p v-else class="italic text-gray-400">No subtasks available.</p>
                </div>

                <!-- Attachments -->
                <div>
                    <h3 class="mb-2 text-xl font-semibold">Attachments</h3>

                    <!-- Dropzone -->
                    <div
                        class="cursor-pointer rounded-lg border-2 border-dashed p-4 text-center transition"
                        :class="dragOver ? 'border-blue-400 bg-gray-800' : 'border-gray-600'"
                        @dragover.prevent="dragOver = true"
                        @dragleave.prevent="dragOver = false"
                        @drop="handleFileDrop"
                    >
                        <p class="text-gray-400">Drag & drop files here or click to upload</p>
                        <input type="file" multiple class="hidden" ref="fileInput" @change="handleFileInput" />
                        <button @click="openFileDialog" class="mt-2 rounded bg-blue-500 px-4 py-1 text-white hover:bg-blue-600">Choose Files</button>
                    </div>

                    <!-- Preview das imagens -->
                    <div v-if="previewFiles.length" class="mt-4 flex gap-4 overflow-x-auto">
                        <div v-for="img in previewFiles" :key="img.name" class="h-20 w-20 overflow-hidden rounded">
                            <img :src="img.url" class="h-full w-full rounded border border-gray-700 object-cover" />
                        </div>
                    </div>

                    <!-- Lista de ficheiros + progresso -->
                    <ul v-if="files.length" class="mt-4 space-y-2">
                        <li v-for="(file, i) in files" :key="i" class="flex items-center justify-between rounded bg-gray-800 px-4 py-2">
                            <span>{{ file.name }}</span>
                            <button @click="removeFile(i)" class="text-sm font-semibold text-red-500 hover:text-red-700">✕</button>
                        </li>
                        <div v-if="isUploading" class="mt-2 h-2 w-full rounded bg-gray-600">
                            <div class="h-2 rounded bg-blue-500 transition-all duration-300" :style="{ width: progress + '%' }"></div>
                        </div>
                    </ul>

                    <!-- Uploaded attachments -->
                    <ul v-if="props.task.attachments.length" class="mt-4 space-y-2">
                        <li
                            v-for="att in props.task.attachments"
                            :key="att.id"
                            class="flex items-center justify-between rounded bg-gray-800 px-4 py-2"
                        >
                            <a :href="att.url" target="_blank" class="text-blue-400 hover:underline">
                                {{ att.name }}
                            </a>
                            <button @click="deleteAttachment(att.id)" class="text-red-500 transition hover:text-red-700" title="Delete">✕</button>
                        </li>
                    </ul>
                    <p v-else class="mt-2 italic text-gray-400">No attachments yet.</p>
                </div>

                <!-- Back -->
                <div>
                    <a :href="route('tasks.index')" class="inline-flex items-center gap-2 text-blue-400 transition hover:text-blue-500">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Task List
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
