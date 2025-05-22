<script setup lang="ts">
// Importações necessárias do Vue e Inertia
import { ref, onMounted, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Interface que define a estrutura de uma tarefa (Todo)
interface Todo {
  id: number;          // Identificador único da tarefa
  title: string;       // Título/descrição da tarefa
  completed: boolean;  // Status de conclusão da tarefa
  hasEmail: boolean;   // Indica se a tarefa tem notificação por email
  hasDeadline: boolean;// Indica se a tarefa tem prazo
  deadline?: string;   // Data limite da tarefa (opcional)
  transferred: boolean;// Indica se a tarefa foi transferida
}

// Lista de tarefas de exemplo com dados iniciais
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

// Referência para o novo item de tarefa a ser adicionado
const newTodo = ref('');

// Configuração dos breadcrumbs (navegação) da página
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Post Create',
    href: '/posts/create',
  },
];

</script>

<template>
  <!-- Define o título da página -->
  <Head title="Post Create" />

  <!-- Layout principal da aplicação com breadcrumbs -->
  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- Container principal com estilo e sombra -->
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl bg-white p-6 shadow-sm">
      <!-- Cabeçalho da página -->
      <div class="border-b border-gray-200 pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Todo List</h1>
      </div>

      <!-- Seção para adicionar nova tarefa -->
      <div class="flex gap-2">
        <!-- Campo de entrada para nova tarefa -->
        <input
          v-model="newTodo"
          type="text"
          class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-gray-800 focus:border-blue-500 focus:outline-none bg-white"
          placeholder="Add a new task..."
        />
        <!-- Botão para voltar à página anterior -->
        <Link
            href="/posts"
            as="button"
            class="hover:cursor-pointer rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700"
        >
            Back
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
