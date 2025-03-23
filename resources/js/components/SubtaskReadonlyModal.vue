<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from '@/axios';
import { useToast } from 'primevue/usetoast';
import type { Task } from '@/composables/useTasks';

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
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="relative p-6 bg-white dark:bg-gray-800 rounded-lg max-w-md w-full shadow-lg">
            <button
                @click="emit('close')"
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white"
                title="Close"
            >
                ✕
            </button>

            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                Subtasks for "{{ selectedTaskTitle }}"
            </h2>

            <ul class="space-y-2">
                <li
                    v-for="sub in subtasks"
                    :key="sub.id"
                    class="bg-gray-100 dark:bg-gray-700 p-2 rounded text-gray-800 dark:text-white"
                >
                    {{ sub.title }}
                </li>
            </ul>

            <div class="mt-6 flex justify-between items-center gap-4">
                <button
                    @click="goToPrev"
                    :disabled="taskIndex === 0"
                    class="flex-1 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    ← Previous
                </button>

                <button
                    @click="goToNext"
                    :disabled="taskIndex === allTasks.length - 1"
                    class="flex-1 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Next →
                </button>
                </div>

                <button
                @click="emit('close')"
                class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full"
                >
                Close
                </button>
            </div>
        </div>
    </transition>
</template>
