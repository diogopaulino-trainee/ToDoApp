<script setup>
import { ref, watchEffect } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import draggable from "vuedraggable";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import { router } from '@inertiajs/vue3';

const toast = useToast();
const isLoading = ref(false);

const props = defineProps({
  tasks: {
    type: Array,
    required: true,
    default: () => [],
  },
});

// Capturar mensagens do backend
const flashMessage = ref(usePage().props.flash?.success || "");
const errorMessage = ref(usePage().props.flash?.error || "");

watchEffect(() => {
  if (flashMessage.value || errorMessage.value) {
    setTimeout(() => {
      flashMessage.value = "";
      errorMessage.value = "";
    }, 3000);
  }
});

const form = useForm({
  title: "",
  description: "",
  priority: "medium",
  due_date: "",
});

// Separar tarefas
const pendingTasks = ref(
  props.tasks.filter((task) => !task.completed)
);
const completedTasks = ref(
  props.tasks.filter((task) => task.completed)
);

// Atualizar estado ao arrastar
const updateTasks = (evt) => {
  if (!evt || (!evt.added && !evt.removed)) return;

  let movedTask = evt.added ? evt.added.element : evt.removed.element;

  if (!movedTask) return;

  if (evt.added) {
    movedTask.completed = completedTasks.value.includes(movedTask);
  }

  useForm({ completed: movedTask.completed }).patch(route("tasks.update", movedTask.id), {
    preserveScroll: true,
    onSuccess: () => {
      flashMessage.value = usePage().props.flash?.success;

      toast.add({
        severity: "info",
        summary: "Task Updated",
        detail: movedTask.completed
          ? `"${movedTask.title}" marked as completed!`
          : `"${movedTask.title}" moved back to pending!`,
        life: 3000,
      });
    },
    onError: () => {
      errorMessage.value = usePage().props.flash?.error;

      toast.add({
        severity: "error",
        summary: "Error",
        detail: "Failed to update task!",
        life: 3000,
      });
    },
  });
};

// Criar nova tarefa
const submit = () => {
  isLoading.value = true;

  form.post(route("tasks.store"), {
    onSuccess: () => {
      toast.add({
        severity: "success",
        summary: "Success",
        detail: "Task added successfully! The list will update shortly.",
        life: 3000,
      });

      form.reset();

      setTimeout(() => {
        router.visit(route('tasks.index'), {
          preserveScroll: true,
          preserveState: false,
        });

        isLoading.value = false;
      }, 3000);
    },
    onError: (errors) => {
      console.error("Form submission error:", errors);
      isLoading.value = false;

      const validationErrors = errors?.errors || errors;

      if (typeof validationErrors === "object" && validationErrors !== null) {
        Object.entries(validationErrors).forEach(([field, fieldErrors]) => {
          if (Array.isArray(fieldErrors)) {
            fieldErrors.forEach((errMsg) => {
              toast.add({
                severity: "error",
                summary: `Validation Error (${field})`,
                detail: errMsg,
                life: 3000,
              });
            });
          } else if (typeof fieldErrors === "string") {
            toast.add({
              severity: "error",
              summary: `Validation Error (${field})`,
              detail: fieldErrors,
              life: 3000,
            });
          }
        });
      } else {
        toast.add({
          severity: "error",
          summary: "Error",
          detail: validationErrors?.message || "Failed to add task!",
          life: 3000,
        });
      }
    },
    onFinish: () => {
    },
  });
};

// Alternar estado de completado
const toggleComplete = (task) => {
  task.completed = !task.completed;

  useForm({ completed: task.completed }).patch(route("tasks.update", task.id), {
    preserveScroll: true,
    onSuccess: () => {
      toast.add({ severity: "success", summary: "Success", detail: "Task updated!", life: 3000 });
    },
    onError: () => {
      toast.add({ severity: "error", summary: "Error", detail: "Failed to delete task!", life: 3000 });
    },
  });

  if (task.completed) {
    pendingTasks.value = pendingTasks.value.filter((t) => t.id !== task.id);
    completedTasks.value.push(task);
  } else {
    completedTasks.value = completedTasks.value.filter((t) => t.id !== task.id);
    pendingTasks.value.push(task);
  }
};

// Eliminar tarefa
const deleteTask = (task) => {
  useForm().delete(route("tasks.destroy", task.id), {
    onSuccess: () => {
      toast.add({ severity: "success", summary: "Success", detail: "Task deleted!", life: 3000 });
      pendingTasks.value = pendingTasks.value.filter((t) => t.id !== task.id);
      completedTasks.value = completedTasks.value.filter((t) => t.id !== task.id);
    },
    onError: () => {
      toast.add({ severity: "error", summary: "Error", detail: "Failed to update task!", life: 3000 });
    },
  });
};
</script>

<template>
  <AppLayout title="To-Do List">
    <Toast />
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
          To-Do List
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          
          <!-- Task Creation Form -->
          <form @submit.prevent="submit" class="space-y-4">
            <input v-model="form.title" class="border p-2 rounded w-full dark:bg-gray-700 dark:text-white" placeholder="Task Title" />
            <textarea v-model="form.description" class="border p-2 rounded w-full dark:bg-gray-700 dark:text-white" placeholder="Task Description"></textarea>
            <div class="flex space-x-4">
              <select v-model="form.priority" class="border p-2 rounded w-full dark:bg-gray-700 dark:text-white">
                <option value="low">Low Priority</option>
                <option value="medium">Medium Priority</option>
                <option value="high">High Priority</option>
              </select>
              <input v-model="form.due_date" type="date" class="border p-2 rounded w-full dark:bg-gray-700 dark:text-white" />
            </div>
            <button type="submit" 
                    class="mt-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 w-full disabled:opacity-50"
                    :disabled="isLoading">
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

          <!-- Task Columns -->
          <div class="grid grid-cols-2 gap-6 mt-6">
            <!-- Pending Tasks -->
            <div>
              <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-3">Pending Tasks</h3>
              <draggable v-model="pendingTasks" group="tasks" itemKey="id" @change="updateTasks">
                <template #item="{ element: task, index }">
                  <div>
                    <div class="border p-4 rounded-lg bg-gray-800 text-white">
                      <div>
                        <h3 class="text-lg font-bold">{{ task.title }}</h3>
                        <p class="text-sm">{{ task.description }}</p>
                        <div class="flex items-center mt-2 space-x-3 text-sm">
                          <span class="px-2 py-1 text-white font-semibold rounded"
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
                            <span>Due: {{ task.due_date || 'No deadline' }}</span>
                          </span>
                        </div>
                      </div>
                      <div class="flex justify-end space-x-2 mt-3">
                        <button @click="toggleComplete(task)" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600">
                          Complete
                        </button>
                        <button @click="deleteTask(task)" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                          Delete
                        </button>
                      </div>
                    </div>
                    <hr v-if="index < pendingTasks.length - 1" class="my-2 border-gray-500 dark:border-gray-400 w-11/12 mx-auto"/>
                  </div>
                </template>
              </draggable>
            </div>

            <!-- Completed Tasks -->
            <div>
              <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-3">Completed Tasks</h3>
              <draggable v-model="completedTasks" group="tasks" itemKey="id" @change="updateTasks">
                <template #item="{ element: task, index }">
                  <div>
                    <div class="border p-4 rounded-lg bg-green-700 text-white">
                      <div>
                        <h3 class="text-lg font-bold line-through">{{ task.title }}</h3>
                        <p class="text-sm">{{ task.description }}</p>
                        <div class="flex items-center mt-2 space-x-3 text-sm">
                          <span class="px-2 py-1 text-white font-semibold rounded"
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
                            <span>Due: {{ task.due_date || 'No deadline' }}</span>
                          </span>
                        </div>
                      </div>
                      <div class="flex justify-end space-x-2 mt-3">
                        <button @click="toggleComplete(task)" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                          Undo
                        </button>
                        <button @click="deleteTask(task)" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                          Delete
                        </button>
                      </div>
                    </div>
                    <hr v-if="index < completedTasks.length - 1" class="my-2 border-gray-500 dark:border-gray-400 w-11/12 mx-auto"/>
                  </div>
                </template>
              </draggable>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
