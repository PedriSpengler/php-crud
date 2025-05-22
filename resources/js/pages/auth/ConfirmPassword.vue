<script setup lang="ts">
// Importação dos componentes necessários
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

// Inicialização do formulário com o campo de senha
const form = useForm({
    password: '',
});

// Função que processa a confirmação da senha
const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();  // Limpa o formulário após o envio
        },
    });
};
</script>

<template>
    <!-- Layout base para autenticação -->
    <AuthLayout title="Confirm your password" description="This is a secure area of the application. Please confirm your password before continuing.">
        <Head title="Confirm password" />

        <!-- Formulário de confirmação de senha -->
        <form @submit.prevent="submit">
            <div class="space-y-6">
                <!-- Campo de senha -->
                <div class="grid gap-2">
                    <Label htmlFor="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        autofocus
                    />

                    <InputError :message="form.errors.password" />
                </div>

                <!-- Botão de confirmação com indicador de carregamento -->
                <div class="flex items-center">
                    <Button class="w-full" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Confirm Password
                    </Button>
                </div>
            </div>
        </form>
    </AuthLayout>
</template>
