<script setup lang="ts">
// Importação dos componentes necessários
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

// Definição das props do componente
defineProps<{
    status?: string;  // Status da mensagem de sucesso
}>();

// Inicialização do formulário com o campo de email
const form = useForm({
    email: '',
});

// Função que processa o envio do email de recuperação
const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <!-- Layout base para autenticação -->
    <AuthLayout title="Forgot password" description="Enter your email to receive a password reset link">
        <Head title="Forgot password" />

        <!-- Exibe mensagem de status se existir -->
        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="space-y-6">
            <!-- Formulário de recuperação de senha -->
            <form @submit.prevent="submit">
                <!-- Campo de email -->
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" name="email" autocomplete="off" v-model="form.email" autofocus placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <!-- Botão de envio com indicador de carregamento -->
                <div class="my-6 flex items-center justify-start">
                    <Button class="w-full" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Email password reset link
                    </Button>
                </div>
            </form>

            <!-- Link para retornar ao login -->
            <div class="space-x-1 text-center text-sm text-muted-foreground">
                <span>Or, return to</span>
                <TextLink :href="route('login')">log in</TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
