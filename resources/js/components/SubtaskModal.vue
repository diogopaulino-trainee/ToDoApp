<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import axios from '@/axios';
import vFocus from '@/directives/vFocus';

const props = defineProps<{
  taskId: number;
  taskTitle: string;
}>();

const emit = defineEmits(['close', 'updated']);
const toast = useToast();
const subtasks = ref<
  { id: number; title: string; completed: boolean; isEditing: boolean }[]
>([]);

const loadSubtasks = async () => {
  try {
    const { data } = await axios.get(`/api/tasks/${props.taskId}/subtasks`);
    subtasks.value = data.map((sub: any) => ({
      ...sub,
      isEditing: false,
    }));
  } catch (e) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load subtasks.', life: 3000 });
  }
};

const saveSubtaskEdit = async (subtask: any) => {
  if (!subtask.title.trim() || !subtask.isEditing) return;

  subtask.isEditing = false;

  try {
    await axios.patch(`/subtasks/${subtask.id}`, { title: subtask.title });
    toast.add({ severity: 'success', summary: 'Updated', detail: 'Subtask title updated!', life: 3000 });
  } catch (e) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to update subtask title.', life: 3000 });
  }
};

const toggleSubtask = async (subtaskId: number) => {
  try {
    await axios.patch(`/subtasks/${subtaskId}/toggle`);
    toast.add({ severity: 'success', summary: 'Updated', detail: 'Subtask updated!', life: 3000 });
    loadSubtasks();
  } catch (e) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to toggle subtask.', life: 3000 });
  }
};

const newSubtasks = ref<string[]>([]);

const addSubtaskField = () => {
  newSubtasks.value.push('');
};

const removeNewSubtaskField = (index: number) => {
  newSubtasks.value.splice(index, 1);
};

const deleteSubtask = async (subtaskId: number) => {
  try {
    await axios.delete(`/subtasks/${subtaskId}`);
    toast.add({ severity: 'success', summary: 'Deleted', detail: 'Subtask deleted!', life: 3000 });
    loadSubtasks();
  } catch (e) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete subtask.', life: 3000 });
  }
};

const saveNewSubtasks = async () => {
  const filtered = newSubtasks.value.map(s => s.trim()).filter(s => s !== '');
  if (!filtered.length) return;

  try {
    await axios.post(`/tasks/${props.taskId}/subtasks`, {
        titles: filtered,
        task_id: props.taskId,
    });
    toast.add({ severity: 'success', summary: 'Added', detail: 'Subtasks added!', life: 3000 });
    newSubtasks.value = [];
    loadSubtasks();
  } catch (e) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to add subtasks.', life: 3000 });
  }
};

onMounted(loadSubtasks);
</script>

<template>
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
          Subtasks for "{{ taskTitle }}"
        </h2>
  
        <ul class="space-y-2">
            <li
                v-for="sub in subtasks"
                :key="sub.id"
                class="flex justify-between items-center bg-gray-100 dark:bg-gray-700 p-2 rounded"
                >
                <div class="w-full">
                    <input
                        v-if="sub.isEditing"
                        v-model="sub.title"
                        v-focus
                        @blur="saveSubtaskEdit(sub)"
                        @keydown.enter.prevent="saveSubtaskEdit(sub)"
                        class="w-full p-1 rounded bg-gray-200 dark:bg-gray-600 dark:text-white"
                    />
                    <span
                        v-else
                        @click="sub.isEditing = true"
                        :class="{
                        'line-through text-gray-400': sub.completed,
                        'text-gray-800 dark:text-white': !sub.completed,
                        'cursor-pointer': true
                        }"
                        title="Click to edit"
                    >
                        {{ sub.title }}
                    </span>
                </div>

                <div class="flex items-center gap-2 ml-4">
                    <button
                        @click="toggleSubtask(sub.id)"
                        class="p-2 rounded text-white"
                        :class="sub.completed ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600'"
                        :title="sub.completed ? 'Undo' : 'Complete'"
                    >
                    <svg
                        v-if="!sub.completed"
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M5 13l4 4L19 7"/>
                    </svg>

                    <svg
                        v-else
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M3 7v6h6"/>
                        <path d="M21 17a9 9 0 0 0-15-6.7L3 13"/>
                    </svg>
                    </button>

                    <button
                    @click="deleteSubtask(sub.id)"
                    class="text-red-500 hover:text-red-700 p-1"
                    title="Delete Subtask"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M3 6h18"/>
                        <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"/>
                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                        <line x1="10" y1="11" x2="10" y2="17"/>
                        <line x1="14" y1="11" x2="14" y2="17"/>
                    </svg>
                    </button>
                </div>
            </li>
        </ul>

        <div class="mt-4">
            <h3 class="text-gray-800 dark:text-white font-semibold mb-2">Add New Subtasks</h3>

            <div v-for="(sub, index) in newSubtasks" :key="index" class="flex items-center gap-2 mb-2">
                <input
                v-model="newSubtasks[index]"
                type="text"
                placeholder="Subtask title"
                class="w-full p-2 rounded bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white"
                />
                <button @click="removeNewSubtaskField(index)" class="text-red-500 hover:text-red-700">✕</button>
            </div>

            <div class="flex gap-2">
                <button @click="addSubtaskField" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">+ Add</button>
                <button @click="saveNewSubtasks" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Save</button>
            </div>
        </div>
  
        <button
          @click="emit('close')"
          class="mt-4 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 w-full"
        >
          Close
        </button>
      </div>
    </div>
  </template>
