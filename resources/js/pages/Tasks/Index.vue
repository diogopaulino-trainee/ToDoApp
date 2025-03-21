<script setup>
import { ref, computed, watchEffect } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import Toast from "@/components/Toast.vue";
import draggable from "vuedraggable";

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

  // Define `completed` com base na lista onde a tarefa foi adicionada
  if (evt.added) {
    movedTask.completed = completedTasks.value.includes(movedTask);
  }

  // Atualiza no backend
  useForm({ completed: movedTask.completed }).patch(route("tasks.update", movedTask.id), {
    preserveScroll: true,
    onSuccess: () => {
      flashMessage.value = usePage().props.flash?.success;
    },
    onError: () => {
      errorMessage.value = usePage().props.flash?.error;
    },
  });
};

// Criar nova tarefa
const submit = () => {
  form.post(route("tasks.store"), {
    onSuccess: () => {
      flashMessage.value = usePage().props.flash?.success;
      form.reset();
    },
    onError: (errors) => {
      errorMessage.value = Object.values(errors).flat().join(" ");
    },
  });
};

// Alternar estado de completado
const toggleComplete = (task) => {
  task.completed = !task.completed;

  useForm({ completed: task.completed }).patch(route("tasks.update", task.id), {
    preserveScroll: true,
    onSuccess: () => {
      flashMessage.value = usePage().props.flash?.success;
    },
    onError: () => {
      errorMessage.value = usePage().props.flash?.error;
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
      flashMessage.value = usePage().props.flash?.success;
      pendingTasks.value = pendingTasks.value.filter((t) => t.id !== task.id);
      completedTasks.value = completedTasks.value.filter((t) => t.id !== task.id);
    },
    onError: () => {
      errorMessage.value = usePage().props.flash?.error;
    },
  });
};
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

    <!-- Toast Notification -->
    <Toast v-if="flashMessage" :message="flashMessage" type="success" />
    <Toast v-if="errorMessage" :message="errorMessage" type="error" />

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
            <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 w-full">
              Add Task
            </button>
          </form>

          <!-- Task Columns -->
          <div class="grid grid-cols-2 gap-6 mt-6">
            <!-- Pending Tasks -->
            <div>
              <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-3">Pending Tasks</h3>
              <draggable v-model="pendingTasks" group="tasks" itemKey="id" @change="updateTasks">
                <template #item="{ element: task, index }">
                  <div>
                    <div class="border p-4 flex justify-between items-center rounded-lg bg-gray-800 text-white">
                      <div>
                        <h3 class="text-lg font-bold">{{ task.title }}</h3>
                        <p class="text-sm">{{ task.description }}</p>
                      </div>
                      <div class="flex space-x-2">
                        <button @click="toggleComplete(task)" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600">
                          Complete
                        </button>
                        <button @click="deleteTask(task)" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                          Delete
                        </button>
                      </div>
                    </div>
                    <!-- Adiciona um separador entre tarefas, exceto na última -->
                    <hr v-if="index < pendingTasks.length - 1" class="my-2 border-gray-500 dark:border-gray-400" />
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
                    <div class="border p-4 flex justify-between items-center rounded-lg bg-green-700 text-white">
                      <div>
                        <h3 class="text-lg font-bold line-through">{{ task.title }}</h3>
                        <p class="text-sm">{{ task.description }}</p>
                      </div>
                      <div class="flex space-x-2">
                        <button @click="toggleComplete(task)" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                          Undo
                        </button>
                        <button @click="deleteTask(task)" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                          Delete
                        </button>
                      </div>
                    </div>
                    <!-- Adiciona um separador entre tarefas, exceto na última -->
                    <hr v-if="index < completedTasks.length - 1" class="my-2 border-gray-500 dark:border-gray-400" />
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
