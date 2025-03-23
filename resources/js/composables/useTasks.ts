import { ref, computed } from "vue";
import { useForm, router, usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";



/**
 * Represents flash messages coming from the backend.
 */
interface FlashMessages {
  success?: string;
  error?: string;
}

/**
 * Represents a task in the to-do list.
 */
export interface Task {
  id: number;
  title: string;
  description: string;
  priority: "low" | "medium" | "high";
  completed: boolean;
  due_date?: string;
  isEditingTitle?: boolean;
  isEditingDescription?: boolean;
  isEditingDate?: boolean;
}

/**
 * Represents the change event emitted by vuedraggable.
 */
interface DraggableChangeEvent<T = unknown> {
  added?: { element: T };
  removed?: { element: T };
}

/**
 * Composable that handles task management logic such as creating,
 * updating, deleting and toggling completion status.
 * 
 * @param propsTasks - Initial list of tasks received as props
 * @returns Object containing form data, state, and task methods
 */
export function useTasks(propsTasks: Task[]) {
  const toast = useToast();
  const isLoading = ref(false);

  const page = usePage();
  const flash = page.props.flash as FlashMessages;

  const flashMessage = ref(flash?.success || "");
  const errorMessage = ref(flash?.error || "");

  /**
   * Form for creating a new task.
   */
  const form = useForm({
    title: "",
    description: "",
    priority: "medium",
    due_date: "",
    subtasks: [] as string[],
  });

  const addSubtaskField = () => {
    form.subtasks.push("");
  };
  
  const removeSubtaskField = (index: number) => {
    form.subtasks.splice(index, 1);
  };

  /**
   * Reactive lists of pending and completed tasks.
   */
  const pendingTasks = ref<Task[]>(propsTasks.filter((task) => !task.completed));
  const completedTasks = ref<Task[]>(propsTasks.filter((task) => task.completed));

  /**
   * Updates task status when moved between lists via drag & drop.
   * 
   * @param evt - Draggable change event
   */
  const updateTasks = (evt: DraggableChangeEvent<Task>, isTargetCompleted: boolean) => {
    if (!evt || !evt.added) return;
  
    const movedTask = evt.added.element;
    if (!movedTask) return;
  
    movedTask.completed = isTargetCompleted;
  
    useForm({ completed: movedTask.completed }).patch(route("tasks.update", movedTask.id), {
      preserveScroll: true,
      onSuccess: () => {
        toast.add({
          severity: "info",
          summary: "Task Updated",
          detail: movedTask.completed
            ? `"${movedTask.title}" marked as completed!`
            : `"${movedTask.title}" moved back to pending!"`,
          life: 3000,
        });
  
        if (movedTask.completed) {
          pendingTasks.value = pendingTasks.value.filter((t) => t.id !== movedTask.id);
          completedTasks.value.push(movedTask);
        } else {
          completedTasks.value = completedTasks.value.filter((t) => t.id !== movedTask.id);
          pendingTasks.value.push(movedTask);
        }
      },
      onError: () => {
        toast.add({
          severity: "error",
          summary: "Error",
          detail: "Failed to update task!",
          life: 3000,
        });
      },
    });
  };

  const showForm = ref(false);

  /**
   * Submits the task creation form to the backend.
   */
  const submit = () => {
    isLoading.value = true;

    form.subtasks = form.subtasks.filter((sub) => sub.trim() !== "");

    form.post(route("tasks.store"), {
      onSuccess: () => {
        toast.add({
          severity: "success",
          summary: "Success",
          detail: "Task added successfully! The list will update shortly.",
          life: 3000,
        });

        form.reset();
        showForm.value = false;

        setTimeout(() => {
          router.visit(route("tasks.index"), {
            preserveScroll: true,
            preserveState: false,
          });
          isLoading.value = false;
        }, 3000);
      },
      onError: (errors) => {
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
            detail: typeof validationErrors === 'string' ? validationErrors : "Failed to add task!",
            life: 3000,
          });
        }
      }
    });
  };

  /**
   * Toggles the completion status of a task and moves it between lists.
   * 
   * @param task - Task to toggle
   */
  const toggleComplete = (task: Task) => {
    task.completed = !task.completed;

    useForm({ completed: task.completed }).patch(route("tasks.update", task.id), {
      preserveScroll: true,
      onSuccess: () => {
        toast.add({ severity: "success", summary: "Success", detail: "Task updated!", life: 3000 });
      },
      onError: () => {
        toast.add({ severity: "error", summary: "Error", detail: "Failed to update task!", life: 3000 });
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

  /**
   * Deletes a task from the system.
   * 
   * @param task - Task to delete
   */
  const deleteTask = (task: Task) => {
    useForm({}).delete(route("tasks.destroy", task.id), {
      onSuccess: () => {
        toast.add({ severity: "success", summary: "Success", detail: "Task deleted!", life: 3000 });
        pendingTasks.value = pendingTasks.value.filter((t) => t.id !== task.id);
        completedTasks.value = completedTasks.value.filter((t) => t.id !== task.id);
      },
      onError: () => {
        toast.add({ severity: "error", summary: "Error", detail: "Failed to delete task!", life: 3000 });
      },
    });
  };

  const isSaving = ref(false);

  const saveTaskEdit = (task: Task) => {
    if (isSaving.value) return;
  
    isSaving.value = true;
    task.isEditingTitle = false;
    task.isEditingDescription = false;
    task.isEditingDate = false;
  
    useForm({
      title: task.title,
      description: task.description,
      due_date: task.due_date,
      priority: task.priority
    }).patch(route("tasks.update", task.id), {
      preserveScroll: true,
      onSuccess: () => {
        toast.add({ severity: "success", summary: "Success", detail: "Task updated.", life: 3000 });
        isSaving.value = false;
      },
      onError: () => {
        toast.add({ severity: "error", summary: "Error", detail: "Failed to save changes.", life: 3000 });
        isSaving.value = false;
      },
    });
  };

  const togglePriority = (task: Task) => {
    const priorities = ["low", "medium", "high"];
    const currentIndex = priorities.indexOf(task.priority);
    task.priority = priorities[(currentIndex + 1) % priorities.length] as "low" | "medium" | "high";
  
    useForm({ priority: task.priority }).patch(route("tasks.update", task.id), {
      preserveScroll: true,
      onSuccess: () => {
        toast.add({
          severity: "info",
          summary: "Priority Updated",
          detail: `Priority changed to ${task.priority.toUpperCase()}`,
          life: 3000,
        });
      },
      onError: () => {
        toast.add({
          severity: "error",
          summary: "Error",
          detail: "Failed to update priority.",
          life: 3000,
        });
      },
    });
  };

  const searchTerm = ref("");
  const sortBy = ref<'priority' | 'due_date'>('due_date');
  const sortDirection = ref<'asc' | 'desc'>('asc');

  const sortTasks = (tasks: Task[]) => {
    return [...tasks].sort((a, b) => {
      if (sortBy.value === 'priority') {
        const priorityOrder = { high: 1, medium: 2, low: 3 };
        const result = priorityOrder[a.priority] - priorityOrder[b.priority];
        return sortDirection.value === 'asc' ? result : -result;
      }

      if (sortBy.value === 'due_date') {
        const dateA = a.due_date || '';
        const dateB = b.due_date || '';
        const result = dateA.localeCompare(dateB);
        return sortDirection.value === 'asc' ? result : -result;
      }

      return 0;
    });
  };

  const matchesSearch = (task: Task) => {
    const search = searchTerm.value.toLowerCase();
    return (
      task.title.toLowerCase().includes(search) ||
      task.description.toLowerCase().includes(search) ||
      (task.due_date && task.due_date.includes(search))
    );
  };

  const filteredPendingTasks = computed(() =>
    sortTasks(pendingTasks.value.filter(matchesSearch))
  );

  const filteredCompletedTasks = computed(() =>
    sortTasks(completedTasks.value.filter(matchesSearch))
  );

  /**
   * Controls the visibility of the Subtask modal.
   */
  const showSubtaskModal = ref(false);

  /**
   * Stores the currently selected task whose subtasks are being managed.
   */
  const selectedTask = ref<Task | null>(null);

  /**
   * Opens the Subtask modal and assigns the selected task.
   * 
   * @param task - The task object to manage subtasks for.
   */
  const openSubtaskModal = (task: Task) => {
    selectedTask.value = task;
    showSubtaskModal.value = true;
  };

  return {
    form,
    isLoading,
    pendingTasks,
    completedTasks,
    flashMessage,
    errorMessage,
    searchTerm,
    sortBy,
    sortDirection,
    filteredPendingTasks,
    filteredCompletedTasks,
    selectedTask,
    showSubtaskModal,
    submit,
    updateTasks,
    toggleComplete,
    deleteTask,
    saveTaskEdit,
    togglePriority,
    openSubtaskModal,
    addSubtaskField,
    removeSubtaskField,
  };
}
