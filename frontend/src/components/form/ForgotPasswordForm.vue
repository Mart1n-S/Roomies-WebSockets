<!-- src/components/form/ForgotPasswordForm.vue -->
<template>
    <form @submit.prevent="handleSubmit"
        class="z-10 w-full max-w-md p-6 space-y-6 shadow-lg bg-roomies-gray3 rounded-xl">
        <h1 class="text-2xl font-bold text-center text-white">Mot de passe oublié</h1>

        <!-- Email sans validation locale, mais affichage d'erreur du store -->
        <EmailInput name="email" label="Adresse email" v-model="email" autocomplete="email"
            :error="auth.error?.includes('email') ? auth.error : ''" />

        <BaseButton type="submit" :loading="auth.loading" class="w-full">
            Envoyer le lien de réinitialisation
        </BaseButton>
    </form>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { useAuthStore } from '@/stores/authStore'

import EmailInput from '@/components/form/inputs/EmailInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'

const email = ref('')
const auth = useAuthStore()
const toast = useToast()
const router = useRouter()

auth.resetError()

async function handleSubmit() {
    try {
        const response = await auth.requestPasswordReset(email.value)
        toast.success(`${response.message}`)
        router.push('/login')
    } catch {
        toast.error(auth.error || 'Une erreur est survenue.')
    }
}
</script>
