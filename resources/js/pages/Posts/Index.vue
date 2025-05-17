<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

interface Todo {
  id: number;
  title: string;
  completed: boolean;
  hasEmail: boolean;
  hasDeadline: boolean;
  deadline?: string;
  transferred: boolean;
}

const todos = ref<Todo[]>([
  {
    id: 1,
    title: 'Salvar, Listar (Com paginação), Editar, Excluir, Recuperar e Confirmar e Descontinuar Atividade',
    completed: false,
    hasEmail: true,
    hasDeadline: false,
    transferred: false
  },
  {
    id: 2,
    title: 'Toda atividade finalizada precisa enviar um e-mail',
    completed: false,
    hasEmail: true,
    hasDeadline: false,
    transferred: false
  },
  {
    id: 3,
    title: 'A atividade precisa ter uma data de validade, quando essa data chegar deve-se inativar a atividade (não é email)',
    completed: false,
    hasEmail: false,
    hasDeadline: true,
    deadline: '2025-06-01',
    transferred: false
  },
  {
    id: 4,
    title: 'Transferência de atividade [por e-mail]',
    completed: false,
    hasEmail: true,
    hasDeadline: false,
    transferred: true
  }
]);

const newTodo = ref('');
const showCompletedTasks = ref(true);

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
  {
    title: 'Todo List',
    href: '/todos',
  },
];

const addTodo = () => {
  if (newTodo.value.trim()) {
    todos.value.push({
      id: Date.now(),
      title: newTodo.value,
      completed: false,
      hasEmail: false,
      hasDeadline: false,
      transferred: false
    });
    newTodo.value = '';
  }
};

const toggleComplete = (id: number) => {
  const todo = todos.value.find(todo => todo.id === id);
  if (todo) {
    todo.completed = !todo.completed;
  }
};

const deleteTodo = (id: number) => {
  todos.value = todos.value.filter(todo => todo.id !== id);
};

const filteredTodos = computed(() => {
  if (showCompletedTasks.value) {
    return todos.value;
  } else {
    return todos.value.filter(todo => !todo.completed);
  }
});
</script>

<template>
  <Head title="Todo List" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl bg-white p-6 shadow-sm">
      <div class="border-b border-gray-200 pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Todo List</h1>
      </div>

      <!-- Add new todo -->
      <div class="flex gap-2">
        <input
          v-model="newTodo"
          type="text"
          class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-gray-800 focus:border-blue-500 focus:outline-none"
          placeholder="Add a new task..."
          @keyup.enter="addTodo"
        />
        <button
          @click="addTodo"
          class="hover:cursor-pointer rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700"
        >
          Add
        </button>
      </div>

      <!-- Filter options -->
      <div class="flex items-center gap-2 pt-2">
        <label class="flex items-center gap-2 text-sm text-gray-600">
          <input
            type="checkbox"
            v-model="showCompletedTasks"
            class="h-4 w-4 rounded border-gray-300 text-blue-600"
          />
          Show completed tasks
        </label>
      </div>

      <!-- Todo list -->
      <div class="mt-4 flex flex-col gap-2">
        <div v-if="filteredTodos.length === 0" class="text-center py-6 text-gray-500">
          No tasks found
        </div>

        <div
          v-for="todo in filteredTodos"
          :key="todo.id"
          class="flex items-center gap-3 rounded-lg border border-gray-200 p-4 transition-colors hover:bg-gray-50"
          :class="{ 'bg-gray-50': todo.completed }"
        >
          <input
            type="checkbox"
            :checked="todo.completed"
            @change="toggleComplete(todo.id)"
            class="h-5 w-5 rounded border-gray-300 text-blue-600"
          />

          <div class="flex-1">
            <p class="text-gray-800" :class="{ 'line-through text-gray-400': todo.completed }">
              {{ todo.title }}
            </p>

            <div class="mt-1 flex gap-2">
              <span v-if="todo.hasEmail" class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800">
                Email
              </span>
              <span v-if="todo.hasDeadline" class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">
                Deadline: {{ todo.deadline }}
              </span>
              <span v-if="todo.transferred" class="inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-xs font-medium text-purple-800">
                Transferred
              </span>
            </div>
          </div>


          <div class="flex gap-2">
            <!-- Editar -->
            <button class="text-gray-500 hover:text-blue-600 hover:cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
              </svg>
            </button>
            <!-- Excluir -->
            <button @click="deleteTodo(todo.id)" class="text-gray-500 hover:text-red-600 hover:cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
