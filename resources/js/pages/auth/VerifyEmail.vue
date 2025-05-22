<script setup lang="ts">
// Importação dos componentes necessários
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

// Definição das props do componente
defineProps<{
    status?: string;  // Status da mensagem de verificação
}>();

// Inicialização do formulário vazio
const form = useForm({});

// Função que processa o reenvio do email de verificação
const submit = () => {
    form.post(route('verification.send'));
};
</script>

<template>
    <!-- Layout base para autenticação -->
    <AuthLayout title="Verify email" description="Please verify your email address by clicking on the link we just emailed to you.">
        <Head title="Email verification" />

        <!-- Exibe mensagem de sucesso quando o link é reenviado -->
        <div v-if="status === 'verification-link-sent'" class="mb-4 text-center text-sm font-medium text-green-600">
            A new verification link has been sent to the email address you provided during registration.
        </div>

        <!-- Formulário de verificação de email -->
        <form @submit.prevent="submit" class="space-y-6 text-center">
            <!-- Botão para reenviar email de verificação -->
            <Button :disabled="form.processing" variant="secondary">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                Resend verification email
            </Button>

            <!-- Link para fazer logout -->
            <TextLink :href="route('logout')" method="post" as="button" class="mx-auto block text-sm"> Log out </TextLink>
        </form>
    </AuthLayout>
</template>
