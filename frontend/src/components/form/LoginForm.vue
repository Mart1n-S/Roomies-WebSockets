<!-- components/form/LoginForm.vue -->
<template>
    <form @submit.prevent="handleLogin"
        class="z-10 w-full max-w-md p-6 space-y-6 shadow-lg bg-roomies-gray3 rounded-xl">
        <h1 class="text-2xl font-bold text-center text-white">Connexion à Roomies</h1>

        <!-- Email -->
        <EmailInput label="Email" name="email" autocomplete="email" v-model="email"
            :error="auth.error?.includes('email') ? auth.error : ''" />

        <!-- Mot de passe -->
        <PasswordInput label="Mot de passe" name="password" v-model="password"
            :error="auth.error?.includes('mot de passe') ? auth.error : ''" :show-conditions="false"
            :show-confirmation="false" />

        <!-- Lien mot de passe oublié -->
        <div class="text-sm text-right">
            <RouterLink to="/forgot-password" class="text-white underline hover:text-roomies-blue">
                Mot de passe oublié ?
            </RouterLink>
        </div>

        <!-- Bouton -->
        <BaseButton type="submit" :loading="loading" class="w-full">Se connecter</BaseButton>

        <!-- Redirection -->
        <p class="text-sm text-center text-white">
            Pas encore inscrit ?
            <RouterLink to="/register" class="font-medium text-white underline hover:text-roomies-blue">
                S'inscrire
            </RouterLink>
        </p>
    </form>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { useAuthStore } from '@/stores/authStore'

import EmailInput from '@/components/form/inputs/EmailInput.vue'
import PasswordInput from '@/components/form/inputs/PasswordInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'

const email = ref('')
const password = ref('')
const loading = ref(false)

const auth = useAuthStore()
const toast = useToast()
const router = useRouter()

async function handleLogin() {
    auth.resetError()
    loading.value = true

    await auth.login(email.value, password.value)

    loading.value = false

    if (auth.user) {
        router.push('/dashboard')
    } else if (auth.error) {
        toast.error(auth.error || 'Une erreur est survenue.')
    }
}
</script>