<script setup lang="ts">
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import axios from 'axios';

// Interface que define a estrutura de um Post
interface Post {
  id: number;          // Identificador único do post
  title: string;       // Título do post
  body: string;        // Conteúdo do post
  created_at: string;  // Data de criação
  updated_at: string;  // Data de atualização
  deleted_at: string | null;  // Data de exclusão (soft delete)
  expires_at: string | null;  // Data de expiração do post
  completed: boolean;  // Status de conclusão do post
}

// Props recebidas do componente pai
const props = defineProps<{
  posts: any;          // Lista de posts ativos
  trashedPosts: any;   // Lista de posts excluídos (lixeira)
}>();

// Estados reativos do componente
const showCreateModal = ref(false);    // Controla a visibilidade do modal de criação/edição
const editingPost = ref<Post | null>(null);  // Post sendo editado
const showTrashedPosts = ref(false);   // Controla a exibição dos posts excluídos
const filterCompleted = ref<'all' | 'completed' | 'not-completed'>('all');  // Filtro de status

const form = useForm({
  title: '',
  body: '',
  expires_at: ''
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
  {
    title: 'Posts',
    href: '/posts',
  },
];

const openCreateModal = () => {
  form.reset();
  showCreateModal.value = true;
};

const closeModal = () => {
  showCreateModal.value = false;
  editingPost.value = null;
  form.reset();
};

// Função para converter datetime-local para UTC ISO string
function toUTCString(localDateTime: string) {
  if (!localDateTime) return '';
  const date = new Date(localDateTime);
  return date.toISOString().slice(0, 19).replace('T', ' ');
}

const createPost = () => {
  const originalExpiresAt = form.expires_at;
  form.expires_at = toUTCString(form.expires_at);
  form.post('/posts', {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      closeModal();
    },
    onFinish: () => {
      form.expires_at = originalExpiresAt;
    }
  });
};

const openEditModal = (post: Post) => {
  editingPost.value = post;
  form.title = post.title;
  form.body = post.body;
  form.expires_at = post.expires_at || '';
  showCreateModal.value = true;
};

const updatePost = () => {
  if (!editingPost.value) return;
  const originalExpiresAt = form.expires_at;
  form.expires_at = toUTCString(form.expires_at);
  form.put(`/posts/${editingPost.value.id}`, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      closeModal();
    },
    onFinish: () => {
      form.expires_at = originalExpiresAt;
    }
  });
};

const deletePost = (id: number) => {
  if (confirm('Tem certeza que deseja mover este post para a lixeira?')) {
    form.delete(`/posts/${id}`, {
      preserveScroll: true,
      preserveState: true
    });
  }
};

const restorePost = (id: number) => {
  form.put(`/posts/${id}/restore`, {
    preserveScroll: true,
    preserveState: true
  });
};

const forceDeletePost = (id: number) => {
  if (confirm('Tem certeza que deseja excluir permanentemente este post? Esta ação não pode ser desfeita.')) {
    form.delete(`/posts/${id}/force-delete`, {
      preserveScroll: true,
      preserveState: true
    });
  }
};

// Função para atualizar o status de completed
const toggleCompleted = async (post: Post) => {
  try {
    const response = await axios.put(`/posts/${post.id}/completed`, {
      completed: !post.completed
    });
    post.completed = response.data.completed;
  } catch (error) {
    alert('Erro ao atualizar o status do checklist.');
  }
};

// Computed property para ordenar e filtrar os posts
const sortedPosts = computed(() => {
  let posts = props.posts.data;

  if (filterCompleted.value === 'completed') {
    posts = posts.filter((post: Post) => post.completed);
  } else if (filterCompleted.value === 'not-completed') {
    posts = posts.filter((post: Post) => !post.completed);
  }

  return posts;
});

const sortedTrashedPosts = computed(() => {
  return props.trashedPosts.data;
});
</script>

<template>
  <Head title="Posts" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl bg-white p-6 shadow-sm">
      <div class="flex items-center justify-between border-b border-gray-200 pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Posts</h1>
        <div class="flex gap-4">
          <div class="flex gap-2">
            <button
              @click="filterCompleted = 'all'"
              :class="[
                'rounded-lg px-4 py-2 text-sm font-medium',
                filterCompleted === 'all'
                  ? 'bg-blue-600 text-white'
                  : 'border border-gray-300 text-gray-700 hover:bg-gray-50'
              ]"
            >
              Todos
            </button>
            <button
              @click="filterCompleted = 'completed'"
              :class="[
                'rounded-lg px-4 py-2 text-sm font-medium',
                filterCompleted === 'completed'
                  ? 'bg-green-600 text-white'
                  : 'border border-gray-300 text-gray-700 hover:bg-gray-50'
              ]"
            >
              Concluídos
            </button>
            <button
              @click="filterCompleted = 'not-completed'"
              :class="[
                'rounded-lg px-4 py-2 text-sm font-medium',
                filterCompleted === 'not-completed'
                  ? 'bg-yellow-600 text-white'
                  : 'border border-gray-300 text-gray-700 hover:bg-gray-50'
              ]"
            >
              Pendentes
            </button>
          </div>
          <button
            @click="showTrashedPosts = !showTrashedPosts"
            class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50"
          >
            {{ showTrashedPosts ? 'Mostrar Posts Ativos' : 'Mostrar Posts Excluídos' }}
          </button>
          <button
            v-if="!showTrashedPosts"
            @click="openCreateModal"
            class="rounded-lg bg-blue-600 hover:cursor-pointer px-4 py-2 font-medium text-white hover:bg-blue-700"
          >
            Novo Post
          </button>
        </div>
      </div>

      <!-- Lista de Posts -->
      <div class="flex flex-col gap-4">
        <div v-if="!showTrashedPosts && sortedPosts.length === 0" class="text-center py-6 text-gray-500">
          Nenhum post encontrado
        </div>
        <div v-if="showTrashedPosts && sortedTrashedPosts.length === 0" class="text-center py-6 text-gray-500">
          Nenhum post na lixeira
        </div>

        <template v-if="!showTrashedPosts">
          <div
            v-for="post in sortedPosts"
            :key="post.id"
            class="rounded-lg border border-gray-200 p-4"
          >
            <div class="flex items-start justify-between">
              <div>
                <div class="flex items-center gap-2">
                  <input type="checkbox" :checked="post.completed" @change="toggleCompleted(post)" />
                  <h2 :class="['text-lg font-semibold', post.completed ? 'line-through text-gray-400' : 'text-gray-800']">
                    {{ post.title }}
                  </h2>
                </div>
                <p class="mt-2 text-gray-600">{{ post.body }}</p>
                <p class="mt-2 text-sm text-gray-500">
                  Criado em: {{ new Date(post.created_at).toLocaleDateString('pt-BR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                  }) }}
                </p>
                <p v-if="post.expires_at" class="mt-1 text-sm text-gray-500">
                  Expira em: {{ new Date(post.expires_at).toLocaleDateString('pt-BR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                  }) }}
                </p>
              </div>
              <div class="flex gap-2">
                <button
                  @click="openEditModal(post)"
                  class="text-gray-500 hover:text-blue-600 hover:cursor-pointer"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                  </svg>
                </button>
                <button
                  @click="deletePost(post.id)"
                  class="text-gray-500 hover:text-red-600 hover:cursor-pointer"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
          <!-- Paginação -->
          <div v-if="props.posts.links && props.posts.links.length > 3" class="flex justify-center mt-4 gap-1 flex-wrap">
            <button
              v-for="link in props.posts.links"
              :key="link.label"
              v-html="link.label"
              :disabled="!link.url"
              @click="link.url && router.visit(link.url)"
              class="px-3 py-1 rounded border text-sm"
              :class="{
                'bg-blue-600 text-white': link.active,
                'bg-white text-gray-700 hover:bg-gray-100': !link.active,
                'opacity-50 cursor-not-allowed': !link.url
              }"
            />
          </div>
        </template>

        <template v-else>
          <div
            v-for="post in sortedTrashedPosts"
            :key="post.id"
            class="rounded-lg border border-gray-200 p-4 bg-gray-50"
          >
            <div class="flex items-start justify-between">
              <div>
                <h2 class="text-lg font-semibold text-gray-800">{{ post.title }}</h2>
                <p class="mt-2 text-gray-600">{{ post.body }}</p>
                <p class="mt-2 text-sm text-gray-500">
                  Excluído em: {{ new Date(post.deleted_at!).toLocaleDateString('pt-BR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                  }) }}
                </p>
              </div>
              <div class="flex gap-2">
                <button
                  @click="restorePost(post.id)"
                  class="text-gray-500 hover:text-green-600 hover:cursor-pointer"
                  title="Restaurar post"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                </button>
                <button
                  @click="forceDeletePost(post.id)"
                  class="text-gray-500 hover:text-red-600 hover:cursor-pointer"
                  title="Excluir permanentemente"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
          <!-- Paginação para posts excluídos -->
          <div v-if="props.trashedPosts.links && props.trashedPosts.links.length > 3" class="flex justify-center mt-4 gap-1 flex-wrap">
            <button
              v-for="link in props.trashedPosts.links"
              :key="link.label"
              v-html="link.label"
              :disabled="!link.url"
              @click="link.url && router.visit(link.url)"
              class="px-3 py-1 rounded border text-sm"
              :class="{
                'bg-blue-600 text-white': link.active,
                'bg-white text-gray-700 hover:bg-gray-100': !link.active,
                'opacity-50 cursor-not-allowed': !link.url
              }"
            />
          </div>
        </template>
      </div>

      <!-- Modal de Criar/Editar Post -->
      <div
        v-if="showCreateModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
      >
        <div class="w-full max-w-md rounded-lg bg-white p-6">
          <h2 class="text-xl font-bold text-gray-800">
            {{ editingPost ? 'Editar Post' : 'Novo Post' }}
          </h2>
          <form @submit.prevent="editingPost ? updatePost() : createPost()" class="mt-4">
            <div class="mb-4">
              <label class="mb-2 block text-sm font-medium text-gray-700">
                Título
              </label>
              <input
                v-model="form.title"
                type="text"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-blue-500 focus:outline-none"
                required
              />
            </div>
            <div class="mb-4">
              <label class="mb-2 block text-sm font-medium text-gray-700">
                Conteúdo
              </label>
              <textarea
                v-model="form.body"
                rows="4"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-blue-500 focus:outline-none"
                required
              ></textarea>
            </div>
            <div class="mb-4">
              <label class="mb-2 block text-sm font-medium text-gray-700">
                Data de Expiração
              </label>
              <input
                v-model="form.expires_at"
                type="datetime-local"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-blue-500 focus:outline-none"
              />
            </div>
            <div class="flex justify-end gap-2">
              <button
                type="button"
                @click="closeModal"
                class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50"
              >
                Cancelar
              </button>
              <button
                type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700"
                :disabled="form.processing"
              >
                {{ editingPost ? 'Salvar' : 'Criar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
