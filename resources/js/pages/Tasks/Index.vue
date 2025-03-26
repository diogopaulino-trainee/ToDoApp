<script setup lang="ts">
import SubtaskModal from '@/components/SubtaskModal.vue';
import { Task, useTasks } from '@/composables/useTasks';
import vFocus from '@/directives/vFocus';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import 'flatpickr/dist/flatpickr.css';
import { ref, watchEffect } from 'vue';
import Flatpickr from 'vue-flatpickr-component';
import draggable from 'vuedraggable';

const showSubtaskModal = ref(false);
const selectedTaskId = ref<number | null>(null);
const selectedTaskTitle = ref<string>('');

const openSubtaskModal = (task: Task) => {
    selectedTaskId.value = task.id;
    selectedTaskTitle.value = task.title;
    showSubtaskModal.value = true;
};

const closeSubtaskModal = () => {
    showSubtaskModal.value = false;
};

const props = defineProps<{
    tasks: Task[];
}>();

const showForm = ref(false);

const {
    form,
    isLoading,
    pendingTasks,
    completedTasks,
    flashMessage,
    errorMessage,
    filteredPendingTasks,
    filteredCompletedTasks,
    searchTerm,
    sortBy,
    sortDirection,
    priorityFilter,
    dateFilter,
    hasAttachmentsFilter,
    addSubtaskField,
    removeSubtaskField,
    submit,
    updateTasks,
    toggleComplete,
    deleteTask,
    saveTaskEdit,
    togglePriority,
} = useTasks(props.tasks);

watchEffect(() => {
    if (flashMessage.value || errorMessage.value) {
        setTimeout(() => {
            flashMessage.value = '';
            errorMessage.value = '';
        }, 3000);
    }
});
</script>

<template>
    <AppLayout title="To-Do List">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">To-Do List</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <div class="neon-wrapper overflow-hidden bg-white p-6 shadow-xl sm:rounded-lg dark:bg-gray-800">
                    <div class="mb-6 flex justify-center">
                        <button
                            @click="showForm = !showForm"
                            class="flex transform items-center gap-2 rounded-full bg-gradient-to-r from-green-400 to-green-600 px-6 py-3 font-semibold text-white shadow-md transition duration-300 hover:scale-105 hover:from-green-500 hover:to-green-700"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="22"
                                height="22"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-calendar-plus"
                            >
                                <path d="M8 2v4" />
                                <path d="M16 2v4" />
                                <path d="M21 13V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8" />
                                <path d="M3 10h18" />
                                <path d="M16 19h6" />
                                <path d="M19 16v6" />
                            </svg>
                            {{ showForm ? 'Close Form' : 'Add New To-Do Task' }}
                        </button>
                    </div>
                    <!-- Task Creation Form -->
                    <transition name="fade-zoom" mode="out-in">
                        <form v-if="showForm" @submit.prevent="submit" class="space-y-4">
                            <input v-model="form.title" class="w-full rounded border p-2 dark:bg-gray-700 dark:text-white" placeholder="Task Title" />
                            <textarea
                                v-model="form.description"
                                class="w-full rounded border p-2 dark:bg-gray-700 dark:text-white"
                                placeholder="Task Description"
                            ></textarea>
                            <div class="flex space-x-4">
                                <select v-model="form.priority" class="w-full rounded border p-2 dark:bg-gray-700 dark:text-white">
                                    <option value="low">Low Priority</option>
                                    <option value="medium">Medium Priority</option>
                                    <option value="high">High Priority</option>
                                </select>
                                <flatpickr
                                    v-model="form.due_datetime"
                                    :config="{
                                        enableTime: true,
                                        dateFormat: 'd/m/Y H:i',
                                        time_24hr: true,
                                        allowInput: true,
                                    }"
                                    placeholder="Select due date and time"
                                    class="w-full rounded border p-2 dark:bg-gray-700 dark:text-white"
                                />
                            </div>

                            <!-- Subtasks -->
                            <div class="space-y-2">
                                <label class="block font-semibold text-white">Subtasks</label>

                                <transition-group name="fade-zoom" tag="div" class="space-y-2">
                                    <div v-for="(subtask, index) in form.subtasks" :key="index" class="flex items-center gap-2">
                                        <input
                                            v-model="form.subtasks[index]"
                                            type="text"
                                            placeholder="Subtask description..."
                                            class="w-full rounded border p-2 dark:bg-gray-700 dark:text-white"
                                        />
                                        <button
                                            type="button"
                                            @click="removeSubtaskField(index)"
                                            class="text-red-500 transition hover:text-red-700"
                                            title="Remove Subtask"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                </transition-group>

                                <button
                                    type="button"
                                    @click="addSubtaskField"
                                    class="mt-2 rounded bg-yellow-600 px-3 py-1 text-white transition hover:bg-yellow-700"
                                >
                                    + Add Subtask
                                </button>
                            </div>
                            <button
                                type="submit"
                                class="mt-2 w-full rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600 disabled:opacity-50"
                                :disabled="isLoading"
                            >
                                <span v-if="isLoading">Adding Task...</span>
                                <span v-else>Add Task</span>
                            </button>
                            <div v-if="isLoading" class="mt-2 flex justify-center">
                                <svg class="h-6 w-6 animate-spin text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </div>
                        </form>
                    </transition>

                    <!-- Search, Sort & Filters -->
                    <div class="mb-6 mt-4 flex flex-col gap-4">
                        <!-- Search input on top -->
                        <div>
                            <input
                                v-model="searchTerm"
                                type="text"
                                placeholder="Search tasks..."
                                class="w-full rounded border p-2 dark:bg-gray-700 dark:text-white"
                            />
                        </div>

                        <!-- Filters and sorting section -->
                        <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                            <!-- Filters group -->
                            <div class="flex w-full flex-col gap-4 md:flex-row md:flex-wrap md:items-center md:gap-4">
                                <!-- Priority Filter -->
                                <div class="flex w-full items-center gap-2 md:w-auto">
                                    <label class="whitespace-nowrap text-white">Priority:</label>
                                    <select v-model="priorityFilter" class="w-full rounded border p-2 dark:bg-gray-700 dark:text-white">
                                        <option value="all">All</option>
                                        <option value="high">High</option>
                                        <option value="medium">Medium</option>
                                        <option value="low">Low</option>
                                    </select>
                                </div>

                                <!-- Date Filter -->
                                <div class="flex w-full items-center gap-2 md:w-auto">
                                    <label class="whitespace-nowrap text-white">Date:</label>
                                    <select v-model="dateFilter" class="w-full rounded border p-2 dark:bg-gray-700 dark:text-white">
                                        <option value="all">All</option>
                                        <option value="today">Today</option>
                                        <option value="week">This Week</option>
                                        <option value="month">This Month</option>
                                        <option value="next_month">Next Month</option>
                                        <option value="after_this_month">After This Month</option>
                                        <option value="overdue">Overdue</option>
                                    </select>
                                </div>

                                <!-- Attachments Filter -->
                                <div class="flex w-full items-center gap-2 md:w-auto">
                                    <label class="whitespace-nowrap text-white">Attachments:</label>
                                    <select v-model="hasAttachmentsFilter" class="w-full rounded border p-2 dark:bg-gray-700 dark:text-white">
                                        <option value="all">All</option>
                                        <option value="with">With Attachments</option>
                                        <option value="without">Without Attachments</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Sort section -->
                            <div class="flex w-full items-center gap-2 md:w-auto">
                                <label class="whitespace-nowrap text-white">Sort by:</label>
                                <select v-model="sortBy" class="w-full rounded border p-2 md:w-auto dark:bg-gray-700 dark:text-white">
                                    <option value="due_datetime">Due Date</option>
                                    <option value="priority">Priority</option>
                                </select>
                                <button
                                    @click="sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'"
                                    class="w-full rounded bg-blue-500 px-3 py-1 text-white transition hover:bg-blue-600 md:w-auto"
                                >
                                    {{ sortDirection === 'asc' ? 'Asc' : 'Desc' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Task Columns -->
                    <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Pending Tasks -->
                        <div class="scrollbar-thin max-h-[calc(100vh-300px)] overflow-y-auto pr-1">
                            <h3 class="mb-3 text-lg font-bold text-gray-800 dark:text-gray-200">Pending Tasks</h3>
                            <div v-if="!filteredPendingTasks.length && pendingTasks.length" class="py-4 text-center italic text-gray-400">
                                No matching pending tasks found for your search. Try adjusting the keywords!
                            </div>
                            <draggable :list="filteredPendingTasks" group="tasks" itemKey="id" @change="(evt) => updateTasks(evt, false)">
                                <template #item="{ element: task, index }">
                                    <div>
                                        <div
                                            class="rounded-lg border bg-gray-800 p-4 text-white transition-all duration-500"
                                            :class="{ 'animate-flyToTrash': task.isDeleting }"
                                        >
                                            <div>
                                                <transition name="fade-zoom" mode="out-in">
                                                    <h3
                                                        v-if="!task.isEditingTitle"
                                                        class="flex cursor-pointer items-center gap-1 text-lg font-bold text-gray-300 hover:underline"
                                                        title="Click to edit title"
                                                        @click="task.isEditingTitle = true"
                                                    >
                                                        {{ task.title }}
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            width="16"
                                                            height="16"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="lucide lucide-pencil shrink-0"
                                                        >
                                                            <path
                                                                d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"
                                                            />
                                                            <path d="m15 5 4 4" />
                                                        </svg>
                                                    </h3>
                                                    <input
                                                        v-else
                                                        v-model="task.title"
                                                        v-focus
                                                        @blur="() => saveTaskEdit(task)"
                                                        @keydown.enter.prevent="() => saveTaskEdit(task)"
                                                        class="w-full rounded bg-gray-900 p-1 text-lg font-bold"
                                                    />
                                                </transition>

                                                <transition name="fade-zoom" mode="out-in">
                                                    <p
                                                        v-if="!task.isEditingDescription"
                                                        class="flex cursor-pointer items-center gap-1 text-sm text-gray-300 hover:underline"
                                                        title="Click to edit description"
                                                        @click="task.isEditingDescription = true"
                                                    >
                                                        {{ task.description }}
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            width="14"
                                                            height="14"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="lucide lucide-pencil shrink-0"
                                                        >
                                                            <path
                                                                d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"
                                                            />
                                                            <path d="m15 5 4 4" />
                                                        </svg>
                                                    </p>
                                                    <textarea
                                                        v-else
                                                        v-model="task.description"
                                                        v-focus
                                                        @blur="() => saveTaskEdit(task)"
                                                        @keydown.enter.prevent="() => saveTaskEdit(task)"
                                                        class="w-full rounded bg-gray-900 p-1 text-sm"
                                                    ></textarea>
                                                </transition>

                                                <div class="mt-2 flex items-center space-x-3 text-sm">
                                                    <span
                                                        @click="togglePriority(task)"
                                                        class="cursor-pointer rounded px-2 py-1 font-semibold text-white transition duration-150 hover:scale-105"
                                                        :class="{
                                                            'bg-red-500': task.priority === 'high',
                                                            'bg-yellow-500': task.priority === 'medium',
                                                            'bg-green-500': task.priority === 'low',
                                                        }"
                                                    >
                                                        {{ task.priority.toUpperCase() }}
                                                    </span>
                                                    <span class="flex items-center text-gray-400 dark:text-gray-300">
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            width="18"
                                                            height="18"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="lucide lucide-calendar-clock mr-1"
                                                        >
                                                            <path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5" />
                                                            <path d="M16 2v4" />
                                                            <path d="M8 2v4" />
                                                            <path d="M3 10h5" />
                                                            <path d="M17.5 17.5 16 16.3V14" />
                                                            <circle cx="16" cy="16" r="6" />
                                                        </svg>

                                                        <transition name="fade-zoom" mode="out-in">
                                                            <span
                                                                v-if="!task.isEditingDate"
                                                                @click="task.isEditingDate = true"
                                                                title="Click to edit due date"
                                                                class="flex cursor-pointer items-center gap-1 hover:text-white hover:underline"
                                                            >
                                                                Due:
                                                                {{
                                                                    task.due_datetime
                                                                        ? task.due_datetime.slice(0, 16).replace('T', ' ')
                                                                        : 'No deadline'
                                                                }}
                                                            </span>
                                                            <input
                                                                v-else
                                                                type="datetime-local"
                                                                :value="task.due_datetime?.slice(0, 16)"
                                                                @input="(e) => (task.due_datetime = (e.target as HTMLInputElement).value)"
                                                                v-focus
                                                                @blur="() => saveTaskEdit(task)"
                                                                @keydown.enter.prevent="() => saveTaskEdit(task)"
                                                                class="rounded bg-gray-900 p-1 text-white"
                                                            />
                                                        </transition>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mt-3 flex items-center justify-between space-x-2">
                                                <a
                                                    :href="route('tasks.show', task.id)"
                                                    class="flex items-center gap-1 text-cyan-400 transition hover:text-cyan-600"
                                                    title="View Task"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                    >
                                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" />
                                                        <circle cx="12" cy="12" r="3" />
                                                    </svg>
                                                    <span class="hidden sm:inline">View</span>
                                                </a>
                                                <div class="flex space-x-2">
                                                    <button
                                                        @click="toggleComplete(task)"
                                                        class="rounded bg-green-500 px-3 py-1 text-white hover:bg-green-600"
                                                    >
                                                        Complete
                                                    </button>
                                                    <button
                                                        @click="deleteTask(task)"
                                                        class="rounded bg-red-500 px-3 py-1 text-white hover:bg-red-600"
                                                    >
                                                        Delete
                                                    </button>
                                                    <button
                                                        @click="openSubtaskModal(task)"
                                                        class="rounded bg-amber-500 px-3 py-1 text-white transition hover:bg-amber-600"
                                                    >
                                                        Subtasks
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <hr
                                            v-if="index < pendingTasks.length - 1"
                                            class="mx-auto my-2 w-11/12 border-gray-500 dark:border-gray-400"
                                        />
                                    </div>
                                </template>
                            </draggable>
                        </div>

                        <!-- Completed Tasks -->
                        <div class="scrollbar-thin max-h-[calc(100vh-300px)] overflow-y-auto pr-1">
                            <h3 class="mb-3 text-lg font-bold text-gray-800 dark:text-gray-200">Completed Tasks</h3>
                            <div v-if="!filteredCompletedTasks.length && completedTasks.length" class="py-4 text-center italic text-gray-400">
                                No matching completed tasks found. Try a different search term.
                            </div>
                            <draggable :list="filteredCompletedTasks" group="tasks" itemKey="id" @change="(evt) => updateTasks(evt, true)">
                                <template #item="{ element: task, index }">
                                    <div>
                                        <div
                                            class="rounded-lg border bg-green-700 p-4 text-white transition-all duration-500"
                                            :class="{ 'animate-flyToTrash': task.isDeleting }"
                                        >
                                            <div>
                                                <transition name="fade-zoom" mode="out-in">
                                                    <h3
                                                        v-if="!task.isEditingTitle"
                                                        class="flex cursor-pointer items-center gap-1 text-lg font-bold text-gray-300 line-through hover:underline"
                                                        title="Click to edit title"
                                                        @click="task.isEditingTitle = true"
                                                    >
                                                        {{ task.title }}
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            width="16"
                                                            height="16"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="lucide lucide-pencil shrink-0"
                                                        >
                                                            <path
                                                                d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"
                                                            />
                                                            <path d="m15 5 4 4" />
                                                        </svg>
                                                    </h3>
                                                    <input
                                                        v-else
                                                        v-model="task.title"
                                                        v-focus
                                                        @blur="() => saveTaskEdit(task)"
                                                        @keydown.enter.prevent="() => saveTaskEdit(task)"
                                                        class="w-full rounded bg-gray-900 p-1 text-lg font-bold"
                                                    />
                                                </transition>

                                                <transition name="fade-zoom" mode="out-in">
                                                    <p
                                                        v-if="!task.isEditingDescription"
                                                        class="flex cursor-pointer items-center gap-1 text-sm text-gray-300 hover:underline"
                                                        title="Click to edit description"
                                                        @click="task.isEditingDescription = true"
                                                    >
                                                        {{ task.description }}
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            width="14"
                                                            height="14"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="lucide lucide-pencil shrink-0"
                                                        >
                                                            <path
                                                                d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"
                                                            />
                                                            <path d="m15 5 4 4" />
                                                        </svg>
                                                    </p>
                                                    <textarea
                                                        v-else
                                                        v-model="task.description"
                                                        v-focus
                                                        @blur="() => saveTaskEdit(task)"
                                                        @keydown.enter.prevent="() => saveTaskEdit(task)"
                                                        class="w-full rounded bg-gray-900 p-1 text-sm"
                                                    ></textarea>
                                                </transition>

                                                <div class="mt-2 flex items-center space-x-3 text-sm">
                                                    <span
                                                        @click="togglePriority(task)"
                                                        class="cursor-pointer rounded px-2 py-1 font-semibold text-white transition duration-150 hover:scale-105"
                                                        :class="{
                                                            'bg-red-500': task.priority === 'high',
                                                            'bg-yellow-500': task.priority === 'medium',
                                                            'bg-green-500': task.priority === 'low',
                                                        }"
                                                    >
                                                        {{ task.priority.toUpperCase() }}
                                                    </span>
                                                    <span class="flex items-center text-gray-200">
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            width="18"
                                                            height="18"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="lucide lucide-calendar-clock mr-1"
                                                        >
                                                            <path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5" />
                                                            <path d="M16 2v4" />
                                                            <path d="M8 2v4" />
                                                            <path d="M3 10h5" />
                                                            <path d="M17.5 17.5 16 16.3V14" />
                                                            <circle cx="16" cy="16" r="6" />
                                                        </svg>

                                                        <transition name="fade-zoom" mode="out-in">
                                                            <span
                                                                v-if="!task.isEditingDate"
                                                                @click="task.isEditingDate = true"
                                                                title="Click to edit due date"
                                                                class="flex cursor-pointer items-center gap-1 hover:text-white hover:underline"
                                                            >
                                                                Due:
                                                                {{
                                                                    task.due_datetime
                                                                        ? task.due_datetime.slice(0, 16).replace('T', ' ')
                                                                        : 'No deadline'
                                                                }}
                                                                <svg
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    class="h-4 w-4 text-gray-400 hover:text-white"
                                                                    fill="none"
                                                                    viewBox="0 0 24 24"
                                                                    stroke="currentColor"
                                                                >
                                                                    <path
                                                                        stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                                    />
                                                                </svg>
                                                            </span>
                                                            <input
                                                                v-else
                                                                type="datetime-local"
                                                                :value="task.due_datetime?.slice(0, 16)"
                                                                @input="(e) => (task.due_datetime = (e.target as HTMLInputElement).value)"
                                                                v-focus
                                                                @blur="() => saveTaskEdit(task)"
                                                                @keydown.enter.prevent="() => saveTaskEdit(task)"
                                                                class="rounded bg-gray-900 p-1 text-white"
                                                            />
                                                        </transition>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mt-3 flex items-center justify-between space-x-2">
                                                <a
                                                    :href="route('tasks.show', task.id)"
                                                    class="flex items-center gap-1 text-cyan-400 transition hover:text-cyan-600"
                                                    title="View Task"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                    >
                                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" />
                                                        <circle cx="12" cy="12" r="3" />
                                                    </svg>
                                                    <span class="hidden sm:inline">View</span>
                                                </a>
                                                <div class="flex space-x-2">
                                                    <button
                                                        @click="toggleComplete(task)"
                                                        class="rounded bg-blue-400 px-3 py-1 text-white transition hover:bg-blue-500"
                                                    >
                                                        Undo
                                                    </button>
                                                    <button
                                                        @click="deleteTask(task)"
                                                        class="rounded bg-red-500 px-3 py-1 text-white transition hover:bg-red-600"
                                                    >
                                                        Delete
                                                    </button>
                                                    <button
                                                        @click="openSubtaskModal(task)"
                                                        class="rounded bg-amber-500 px-3 py-1 text-white transition hover:bg-amber-600"
                                                    >
                                                        Subtasks
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <hr
                                            v-if="index < completedTasks.length - 1"
                                            class="mx-auto my-2 w-11/12 border-gray-500 dark:border-gray-400"
                                        />
                                    </div>
                                </template>
                            </draggable>
                        </div>
                    </div>
                    <div v-if="!pendingTasks.length && !completedTasks.length" class="py-4 text-center italic text-gray-400">
                        You don't have any tasks yet. Start by adding your first To-Do task and take control of your productivity!
                    </div>
                </div>
            </div>
        </div>
        <transition name="fade-slide">
            <SubtaskModal
                v-if="showSubtaskModal"
                :task-id="selectedTaskId!"
                :task-title="selectedTaskTitle"
                @close="closeSubtaskModal"
                @updated="router.visit(route('tasks.index'), { preserveScroll: true })"
            />
        </transition>
    </AppLayout>
</template>
