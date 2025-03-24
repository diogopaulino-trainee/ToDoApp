<script setup lang="ts">
import { ref, watchEffect } from "vue";
import AppLayout from "@/layouts/AppLayout.vue";
import draggable from "vuedraggable";
import { useTasks, Task } from "@/composables/useTasks";
import vFocus from '@/directives/vFocus';
import SubtaskModal from '@/components/SubtaskModal.vue';
import { router } from '@inertiajs/vue3';
import Flatpickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

const showSubtaskModal = ref(false);
const selectedTaskId = ref<number | null>(null);
const selectedTaskTitle = ref<string>("");

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
      flashMessage.value = "";
      errorMessage.value = "";
    }, 3000);
  }
});
</script>

<template>
  <AppLayout title="To-Do List">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
          To-Do List
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-center mb-6">
            <button
              @click="showForm = !showForm"
              class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-400 to-green-600 text-white font-semibold rounded-full shadow-md hover:from-green-500 hover:to-green-700 transition duration-300 transform hover:scale-105"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-plus">
                <path d="M8 2v4"/><path d="M16 2v4"/><path d="M21 13V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8"/>
                <path d="M3 10h18"/><path d="M16 19h6"/><path d="M19 16v6"/>
              </svg>
              {{ showForm ? 'Close Form' : 'Add New To-Do Task' }}
            </button>
          </div>
          <!-- Task Creation Form -->
          <transition name="fade-zoom" mode="out-in">
            <form v-if="showForm" @submit.prevent="submit" class="space-y-4">
              <input v-model="form.title" class="border p-2 rounded w-full dark:bg-gray-700 dark:text-white" placeholder="Task Title" />
              <textarea v-model="form.description" class="border p-2 rounded w-full dark:bg-gray-700 dark:text-white" placeholder="Task Description"></textarea>
              <div class="flex space-x-4">
                <select v-model="form.priority" class="border p-2 rounded w-full dark:bg-gray-700 dark:text-white">
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
                  class="border p-2 rounded w-full dark:bg-gray-700 dark:text-white"
                />
              </div>

              <!-- Subtasks -->
              <div class="space-y-2">
                <label class="block text-white font-semibold">Subtasks</label>

                <transition-group name="fade-zoom" tag="div" class="space-y-2">
                  <div
                    v-for="(subtask, index) in form.subtasks"
                    :key="index"
                    class="flex items-center gap-2"
                  >
                    <input
                      v-model="form.subtasks[index]"
                      type="text"
                      placeholder="Subtask description..."
                      class="w-full border p-2 rounded dark:bg-gray-700 dark:text-white"
                    />
                    <button
                      type="button"
                      @click="removeSubtaskField(index)"
                      class="text-red-500 hover:text-red-700 transition"
                      title="Remove Subtask"
                    >
                      ✕
                    </button>
                  </div>
                </transition-group>

                <button
                  type="button"
                  @click="addSubtaskField"
                  class="mt-2 px-3 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition"
                >
                  + Add Subtask
                </button>
              </div>
              <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 w-full disabled:opacity-50" :disabled="isLoading">
                <span v-if="isLoading">Adding Task...</span>
                <span v-else>Add Task</span>
              </button>
              <div v-if="isLoading" class="mt-2 flex justify-center">
                <svg class="animate-spin h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
                class="border p-2 rounded w-full dark:bg-gray-700 dark:text-white"
              />
            </div>

            <!-- Filters and sorting section -->
            <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-start">

              <!-- Filters group -->
              <div class="flex flex-col gap-4 w-full md:flex-row md:flex-wrap md:items-center md:gap-4">

                <!-- Priority Filter -->
                <div class="flex items-center gap-2 w-full md:w-auto">
                  <label class="text-white whitespace-nowrap">Priority:</label>
                  <select v-model="priorityFilter" class="border p-2 rounded w-full dark:bg-gray-700 dark:text-white">
                    <option value="all">All</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                  </select>
                </div>

                <!-- Date Filter -->
                <div class="flex items-center gap-2 w-full md:w-auto">
                  <label class="text-white whitespace-nowrap">Date:</label>
                  <select v-model="dateFilter" class="border p-2 rounded w-full dark:bg-gray-700 dark:text-white">
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
                <div class="flex items-center gap-2 w-full md:w-auto">
                  <label class="text-white whitespace-nowrap">Attachments:</label>
                  <select v-model="hasAttachmentsFilter" class="border p-2 rounded w-full dark:bg-gray-700 dark:text-white">
                    <option value="all">All</option>
                    <option value="with">With Attachments</option>
                    <option value="without">Without Attachments</option>
                  </select>
                </div>
              </div>

              <!-- Sort section -->
              <div class="flex gap-2 items-center w-full md:w-auto">
                <label class="text-white whitespace-nowrap">Sort by:</label>
                <select v-model="sortBy" class="border p-2 rounded w-full md:w-auto dark:bg-gray-700 dark:text-white">
                  <option value="due_datetime">Due Date</option>
                  <option value="priority">Priority</option>
                </select>
                <button
                  @click="sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'"
                  class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition w-full md:w-auto"
                >
                  {{ sortDirection === 'asc' ? 'Asc' : 'Desc' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Task Columns -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <!-- Pending Tasks -->
            <div class="max-h-[calc(100vh-300px)] overflow-y-auto pr-1 scrollbar-thin">
              <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-3">Pending Tasks</h3>
              <div v-if="!filteredPendingTasks.length && pendingTasks.length" class="text-center text-gray-400 italic py-4">
                No matching pending tasks found for your search. Try adjusting the keywords!
              </div>
              <draggable :list="filteredPendingTasks" group="tasks" itemKey="id" @change="(evt) => updateTasks(evt, false)">
                <template #item="{ element: task, index }">
                  <div>
                    <div class="border p-4 rounded-lg bg-gray-800 text-white transition-all duration-500" :class="{ 'animate-flyToTrash': task.isDeleting }">
                      <div>
                        <transition name="fade-zoom" mode="out-in">
                          <h3
                            v-if="!task.isEditingTitle"
                            class="text-lg font-bold cursor-pointer flex items-center gap-1 hover:underline text-gray-300"
                            title="Click to edit title"
                            @click="task.isEditingTitle = true"
                          >
                            {{ task.title }}
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil shrink-0">
                              <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/>
                              <path d="m15 5 4 4"/>
                            </svg>
                          </h3>
                          <input v-else v-model="task.title" v-focus @blur="() => saveTaskEdit(task)" @keydown.enter.prevent="() => saveTaskEdit(task)" class="text-lg font-bold w-full p-1 bg-gray-900 rounded" />
                        </transition>

                        <transition name="fade-zoom" mode="out-in">
                          <p
                            v-if="!task.isEditingDescription"
                            class="text-sm text-gray-300 cursor-pointer hover:underline flex items-center gap-1"
                            title="Click to edit description"
                            @click="task.isEditingDescription = true"
                          >
                          {{ task.description }}
                          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil shrink-0">
                            <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/>
                            <path d="m15 5 4 4"/>
                            </svg>
                          </p>
                          <textarea v-else v-model="task.description" v-focus @blur="() => saveTaskEdit(task)" @keydown.enter.prevent="() => saveTaskEdit(task)" class="text-sm w-full p-1 bg-gray-900 rounded"></textarea>
                        </transition>

                        <div class="flex items-center mt-2 space-x-3 text-sm">
                          <span
                          @click="togglePriority(task)"
                          class="px-2 py-1 text-white font-semibold rounded cursor-pointer transition duration-150 hover:scale-105"
                                :class="{
                                  'bg-red-500': task.priority === 'high',
                                  'bg-yellow-500': task.priority === 'medium',
                                  'bg-green-500': task.priority === 'low'
                                }">
                            {{ task.priority.toUpperCase() }}
                          </span>
                          <span class="flex items-center text-gray-400 dark:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-clock mr-1">
                              <path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5"/>
                              <path d="M16 2v4"/>
                              <path d="M8 2v4"/>
                              <path d="M3 10h5"/>
                              <path d="M17.5 17.5 16 16.3V14"/>
                              <circle cx="16" cy="16" r="6"/>
                            </svg>

                            <transition name="fade-zoom" mode="out-in">
                              <span
                                v-if="!task.isEditingDate"
                                @click="task.isEditingDate = true"
                                title="Click to edit due date"
                                class="hover:underline hover:text-white cursor-pointer flex items-center gap-1"
                              >
                                Due: {{ task.due_datetime ? task.due_datetime.slice(0, 16).replace('T', ' ') : 'No deadline' }}
                              </span>
                              <input v-else
                                type="datetime-local"
                                :value="task.due_datetime?.slice(0, 16)"
                                @input="(e) => task.due_datetime = (e.target as HTMLInputElement).value"
                                v-focus
                                @blur="() => saveTaskEdit(task)"
                                @keydown.enter.prevent="() => saveTaskEdit(task)"
                                class="bg-gray-900 text-white rounded p-1"
                              />
                            </transition>
                          </span>
                        </div>
                      </div>
                      <div class="flex justify-between items-center space-x-2 mt-3">
                        <a
                          :href="route('tasks.show', task.id)"
                          class="text-cyan-400 hover:text-cyan-600 transition flex items-center gap-1"
                          title="View Task"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                            <circle cx="12" cy="12" r="3"/>
                          </svg>
                          <span class="hidden sm:inline">View</span>
                        </a>
                        <div class="flex space-x-2">
                          <button @click="toggleComplete(task)" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">Complete</button>
                          <button @click="deleteTask(task)" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
                          <button @click="openSubtaskModal(task)" class="px-3 py-1 bg-amber-500 text-white rounded hover:bg-amber-600 transition">Subtasks</button>
                        </div>
                      </div>
                    </div>
                    <hr v-if="index < pendingTasks.length - 1" class="my-2 border-gray-500 dark:border-gray-400 w-11/12 mx-auto"/>
                  </div>
                </template>
              </draggable>
            </div>

            <!-- Completed Tasks -->
            <div class="max-h-[calc(100vh-300px)] overflow-y-auto pr-1 scrollbar-thin">
              <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-3">Completed Tasks</h3>
              <div v-if="!filteredCompletedTasks.length && completedTasks.length" class="text-center text-gray-400 italic py-4">
                No matching completed tasks found. Try a different search term.
              </div>
              <draggable :list="filteredCompletedTasks" group="tasks" itemKey="id" @change="(evt) => updateTasks(evt, true)">
                <template #item="{ element: task, index }">
                  <div>
                    <div class="border p-4 rounded-lg bg-green-700 text-white transition-all duration-500" :class="{ 'animate-flyToTrash': task.isDeleting }">
                      <div>
                        <transition name="fade-zoom" mode="out-in">
                          <h3
                            v-if="!task.isEditingTitle"
                            class="text-lg font-bold cursor-pointer flex items-center gap-1 hover:underline text-gray-300 line-through"
                            title="Click to edit title"
                            @click="task.isEditingTitle = true"
                          >
                            {{ task.title }}
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil shrink-0">
                              <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/>
                              <path d="m15 5 4 4"/>
                            </svg>
                          </h3>
                          <input v-else v-model="task.title" v-focus @blur="() => saveTaskEdit(task)" @keydown.enter.prevent="() => saveTaskEdit(task)" class="text-lg font-bold w-full p-1 bg-gray-900 rounded" />
                        </transition>

                        <transition name="fade-zoom" mode="out-in">
                          <p
                            v-if="!task.isEditingDescription"
                            class="text-sm text-gray-300 cursor-pointer hover:underline flex items-center gap-1"
                            title="Click to edit description"
                            @click="task.isEditingDescription = true"
                          >
                            {{ task.description }}
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil shrink-0">
                              <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/>
                              <path d="m15 5 4 4"/>
                            </svg>
                          </p>
                          <textarea v-else v-model="task.description" v-focus @blur="() => saveTaskEdit(task)" @keydown.enter.prevent="() => saveTaskEdit(task)" class="text-sm w-full p-1 bg-gray-900 rounded"></textarea>
                        </transition>

                        <div class="flex items-center mt-2 space-x-3 text-sm">
                          <span
                          @click="togglePriority(task)"
                          class="px-2 py-1 text-white font-semibold rounded cursor-pointer transition duration-150 hover:scale-105"
                                :class="{
                                  'bg-red-500': task.priority === 'high',
                                  'bg-yellow-500': task.priority === 'medium',
                                  'bg-green-500': task.priority === 'low'
                                }">
                            {{ task.priority.toUpperCase() }}
                          </span>
                          <span class="flex items-center text-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-clock mr-1">
                              <path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5"/>
                              <path d="M16 2v4"/>
                              <path d="M8 2v4"/>
                              <path d="M3 10h5"/>
                              <path d="M17.5 17.5 16 16.3V14"/>
                              <circle cx="16" cy="16" r="6"/>
                            </svg>

                            <transition name="fade-zoom" mode="out-in">
                              <span
                                v-if="!task.isEditingDate"
                                @click="task.isEditingDate = true"
                                title="Click to edit due date"
                                class="hover:underline hover:text-white cursor-pointer flex items-center gap-1"
                              >
                                Due: {{ task.due_datetime ? task.due_datetime.slice(0, 16).replace('T', ' ') : 'No deadline' }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                              </span>
                              <input v-else
                                type="datetime-local"
                                :value="task.due_datetime?.slice(0, 16)"
                                @input="(e) => task.due_datetime = (e.target as HTMLInputElement).value"
                                v-focus
                                @blur="() => saveTaskEdit(task)"
                                @keydown.enter.prevent="() => saveTaskEdit(task)"
                                class="bg-gray-900 text-white rounded p-1"
                              />
                            </transition>
                          </span>
                        </div>
                      </div>
                      <div class="flex justify-between items-center space-x-2 mt-3">
                        <a
                          :href="route('tasks.show', task.id)"
                          class="text-cyan-400 hover:text-cyan-600 transition flex items-center gap-1"
                          title="View Task"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                            <circle cx="12" cy="12" r="3"/>
                          </svg>
                          <span class="hidden sm:inline">View</span>
                        </a>
                        <div class="flex space-x-2">
                          <button @click="toggleComplete(task)" class="px-3 py-1 bg-blue-400 text-white rounded hover:bg-blue-500 transition">Undo</button>
                          <button @click="deleteTask(task)" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">Delete</button>
                          <button @click="openSubtaskModal(task)" class="px-3 py-1 bg-amber-500 text-white rounded hover:bg-amber-600 transition">Subtasks</button>
                        </div>
                      </div>
                    </div>
                    <hr v-if="index < completedTasks.length - 1" class="my-2 border-gray-500 dark:border-gray-400 w-11/12 mx-auto"/>
                  </div>
                </template>
              </draggable>
            </div>
          </div>
          <div v-if="!pendingTasks.length && !completedTasks.length" class="text-center text-gray-400 italic py-4">
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
