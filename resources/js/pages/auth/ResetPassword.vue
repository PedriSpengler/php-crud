<script setup lang="ts">
// Importação dos componentes necessários
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

// Interface para as props do componente
interface Props {
    token: string;  // Token de redefinição de senha
    email: string;  // Email do usuário
}

const props = defineProps<Props>();

// Inicialização do formulário com os campos necessários
const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

// Função que processa a redefinição de senha
const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');  // Limpa os campos de senha após o envio
        },
    });
};
</script>

<template>
    <!-- Layout base para autenticação -->
    <AuthLayout title="Reset password" description="Please enter your new password below">
        <Head title="Reset password" />

        <!-- Formulário de redefinição de senha -->
        <form @submit.prevent="submit">
            <div class="grid gap-6">
                <!-- Campo de email (somente leitura) -->
                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input id="email" type="email" name="email" autocomplete="email" v-model="form.email" class="mt-1 block w-full" readonly />
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>

                <!-- Campo de nova senha -->
                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        v-model="form.password"
                        class="mt-1 block w-full"
                        autofocus
                        placeholder="Password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <!-- Campo de confirmação da nova senha -->
                <div class="grid gap-2">
                    <Label for="password_confirmation"> Confirm Password </Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        class="mt-1 block w-full"
                        placeholder="Confirm password"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <!-- Botão de redefinição com indicador de carregamento -->
                <Button type="submit" class="mt-4 w-full" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Reset password
                </Button>
            </div>
        </form>
    </AuthLayout>
</template>
