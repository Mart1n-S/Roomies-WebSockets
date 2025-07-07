<!-- src/components/form/ResetPasswordForm.vue -->
<template>
    <form @submit.prevent="handleSubmit"
        class="z-10 w-full max-w-md p-6 space-y-6 shadow-lg bg-roomies-gray3 rounded-xl">
        <h1 class="text-2xl font-bold text-center text-white">Définir un nouveau mot de passe</h1>

        <EmailInput label="Email" name="email" autocomplete="email" v-model="email"
            :error="auth.error?.includes('email') ? auth.error : ''" />

        <PasswordInput ref="passwordRef" name="password" label="Nouveau mot de passe" v-model="password"
            :show-conditions="true" :show-confirmation="true" @valid="(v) => isPasswordValid = v" />

        <BaseButton type="submit" :loading="loading" class="w-full">
            Réinitialiser
        </BaseButton>
    </form>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'

import EmailInput from '@/components/form/inputs/EmailInput.vue'
import PasswordInput from '@/components/form/inputs/PasswordInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import { useAuthStore } from '@/stores/authStore'

const route = useRoute()
const router = useRouter()
const toast = useToast()
const auth = useAuthStore()

const email = ref('')
const password = ref('')
const emailError = ref('')
const isPasswordValid = ref(false)
const loading = ref(false)

const token = ref('')
const passwordRef = ref<InstanceType<typeof PasswordInput> | null>(null)

onMounted(() => {
    auth.resetError()
    token.value = route.query.token as string || ''
})

async function handleSubmit() {
    emailError.value = ''

    if (!isPasswordValid.value || !passwordRef.value) {
        toast.error('Merci de remplir tous les champs correctement.')
        return
    }

    const confirmPassword = passwordRef.value.confirmPassword

    try {
        loading.value = true
        const response = await auth.resetPassword(email.value, token.value, password.value, confirmPassword)
        toast.success(response.message || 'Mot de passe réinitialisé avec succès.')
        router.push('/login')
    } catch {
        toast.error(auth.error)
    } finally {
        loading.value = false
    }
}
</script>
