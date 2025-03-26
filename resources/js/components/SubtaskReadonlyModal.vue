<script setup lang="ts">
import axios from '@/axios';
import type { Task } from '@/composables/useTasks';
import { useToast } from 'primevue/usetoast';
import { onMounted, ref } from 'vue';

const props = defineProps<{
    taskId: number;
    taskTitle: string;
    allTasks: Task[];
    initialIndex: number;
}>();

const emit = defineEmits(['close']);
const toast = useToast();

const subtasks = ref<{ id: number; title: string; completed: boolean }[]>([]);

const selectedTaskTitle = ref(props.taskTitle);
const selectedTaskId = ref(props.taskId);
const allTasks = ref(props.allTasks);
const taskIndex = ref(props.initialIndex);

const goToNext = () => {
    if (taskIndex.value < allTasks.value.length - 1) {
        taskIndex.value++;
        updateTaskInfo();
    }
};

const goToPrev = () => {
    if (taskIndex.value > 0) {
        taskIndex.value--;
        updateTaskInfo();
    }
};

const updateTaskInfo = () => {
    const task = allTasks.value[taskIndex.value];
    selectedTaskTitle.value = task.title;
    selectedTaskId.value = task.id;
    loadSubtasks();
};

const loadSubtasks = async () => {
    try {
        const { data } = await axios.get(`/api/tasks/${selectedTaskId.value}/subtasks`);
        subtasks.value = data;
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to load subtasks.',
            life: 3000,
        });
    }
};

onMounted(loadSubtasks);
</script>

<template>
    <transition name="fade-slide">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-gray-800">
                <button
                    @click="emit('close')"
                    class="absolute right-2 top-2 text-gray-500 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white"
                    title="Close"
                >
                    ✕
                </button>

                <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Subtasks for "{{ selectedTaskTitle }}"</h2>

                <ul class="space-y-2">
                    <li v-for="sub in subtasks" :key="sub.id" class="rounded bg-gray-100 p-2 text-gray-800 dark:bg-gray-700 dark:text-white">
                        {{ sub.title }}
                    </li>
                </ul>

                <div class="mt-6 flex items-center justify-between gap-4">
                    <button
                        @click="goToPrev"
                        :disabled="taskIndex === 0"
                        class="flex-1 rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        ← Previous
                    </button>

                    <button
                        @click="goToNext"
                        :disabled="taskIndex === allTasks.length - 1"
                        class="flex-1 rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Next →
                    </button>
                </div>

                <button @click="emit('close')" class="mt-4 w-full rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Close</button>
            </div>
        </div>
    </transition>
</template>
