<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Task } from '@/composables/useTasks';
import SubtaskReadonlyModal from '@/components/SubtaskReadonlyModal.vue';

const props = defineProps<{
  deletedTasks: Task[];
}>();

const selectedTaskId = ref<number | null>(null);
const selectedTaskTitle = ref<string>('');
const showModal = ref(false);

const restoringTaskId = ref<number | null>(null);

const restoreTask = (id: number) => {
  restoringTaskId.value = id;

  setTimeout(() => {
    router.patch(route('tasks.restore', id), {}, {
      preserveScroll: true
    });
  }, 300);
};

const openModal = (task: Task) => {
  selectedTaskId.value = task.id;
  selectedTaskTitle.value = task.title;
  showModal.value = true;
};
</script>

<template>
  <AppLayout title="Recycle Bin">
    <div class="py-12 max-w-4xl mx-auto">
      <div class="flex items-center gap-2 mb-6 text-white text-2xl font-bold">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M3 6h18"/>
          <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"/>
          <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
          <line x1="10" y1="11" x2="10" y2="17"/>
          <line x1="14" y1="11" x2="14" y2="17"/>
        </svg>
        Recycle Bin
      </div>

      <div v-if="!deletedTasks.length" class="text-gray-400 italic">
        No deleted tasks found.
      </div>

      <div
        v-for="task in deletedTasks"
        :key="task.id"
        :class="[
        'bg-gray-800 p-4 rounded-lg mb-4 shadow text-white transition-all duration-300 ease-in-out will-animate',
        restoringTaskId === task.id ? 'opacity-0 scale-95' : 'opacity-100 scale-100'
        ]"
        >
        <h3 class="text-xl font-semibold">{{ task.title }}</h3>
        <p class="text-gray-300">{{ task.description }}</p>
        <div class="mt-3 flex gap-2">
          <button
            @click="restoreTask(task.id)"
            class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition"
          >
            Restore
          </button>
          <button
            @click="openModal(task)"
            class="px-3 py-1 bg-amber-500 text-white rounded hover:bg-amber-600 transition"
          >
            View Subtasks
          </button>
        </div>
      </div>
    </div>

    <SubtaskReadonlyModal
        v-if="showModal"
        :task-id="selectedTaskId!"
        :task-title="selectedTaskTitle"
        :all-tasks="deletedTasks"
        :initial-index="deletedTasks.findIndex(t => t.id === selectedTaskId)"
        @close="showModal = false"
    />
  </AppLayout>
</template>
