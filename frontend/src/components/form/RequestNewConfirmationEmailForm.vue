<template>
    <form @submit.prevent="handleSubmit" class="w-full max-w-md p-6 space-y-6 shadow-lg bg-roomies-gray3 rounded-xl">
        <h1 class="text-2xl font-bold text-center text-white">Renvoyer l’email de confirmation</h1>

        <EmailInput label="Email" name="email" v-model="email" autocomplete="email"
            :error="auth.error?.includes('email') ? auth.error : ''" @valid="(v) => isEmailValid = v"
            @error="(msg) => emailError = msg" />

        <BaseButton type="submit" :loading="loading" class="w-full">
            Renvoyer
        </BaseButton>
    </form>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useToast } from 'vue-toastification'
import EmailInput from '@/components/form/inputs/EmailInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import { useAuthStore } from '@/stores/authStore'

const toast = useToast()
const auth = useAuthStore()

const email = ref('')
const emailError = ref('')
const isEmailValid = ref(false)
const loading = ref(false)

auth.resetError()

async function handleSubmit() {
    emailError.value = ''

    if (!isEmailValid.value) {
        toast.error('Merci d’entrer une adresse email valide.')
        return
    }

    try {
        loading.value = true
        const res = await auth.requestNewConfirmationEmail(email.value)
        toast.success(res.message || 'Si un compte existe, un nouvel email de confirmation a été envoyé.')
    } catch {
        toast.error(auth.error)
    } finally {
        loading.value = false
    }
}
</script>
