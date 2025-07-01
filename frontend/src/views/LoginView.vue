<template>
    <PublicNavbar />

    <div class="relative flex items-center justify-center min-h-screen px-4 text-white bg-roomies-gray5">
        <form @submit.prevent="handleLogin"
            class="z-10 w-full max-w-md p-6 space-y-6 shadow-lg bg-roomies-gray3 rounded-xl">
            <h1 class="text-2xl font-bold text-center text-white">Connexion à Roomies</h1>

            <!-- Email -->
            <BaseInput label="Email" name="email" type="email" v-model="email" />

            <!-- Mot de passe avec toggle -->
            <BaseInput label="Mot de passe" name="password" :type="showPassword ? 'text' : 'password'"
                v-model="password" :error="auth.error" @icon-click="showPassword = !showPassword">
                <template #right-icon>
                    <i :class="[
                        'pi text-gray-400 hover:text-white',
                        showPassword ? 'pi-eye-slash' : 'pi-eye'
                    ]" />
                </template>
            </BaseInput>

            <!-- Lien mot de passe oublié -->
            <div class="text-sm text-right">
                <RouterLink to="/forgot-password" class="text-white underline hover:text-roomies-blue">
                    Mot de passe oublié ?
                </RouterLink>
            </div>

            <!-- Bouton -->
            <BaseButton type="submit" :loading="loading" class="w-full">Se connecter</BaseButton>

            <!-- Lien vers l'inscription -->
            <p class="text-sm text-center text-white">
                Pas encore inscrit ?
                <RouterLink to="/register" class="font-medium text-white underline hover:text-roomies-blue">
                    S'inscrire
                </RouterLink>
            </p>
        </form>
        <img src="@/assets/images/hero-illustration-form.svg"
            class="absolute bottom-0 left-0 z-0 object-cover w-full min-h-screen" alt="" />
        <img src="@/assets/images/hero-illustration-left-2.svg" class="absolute bottom-0 left-0 z-0 w-1/3 max-w-xs"
            alt="" />
        <img src="@/assets/images/hero-illustration-right-2.svg" class="absolute bottom-0 right-0 z-0 w-1/3 max-w-xs"
            alt="" />
    </div>

    <AppFooter />
</template>


<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import { useAuthStore } from '@/stores/authStore'
import PublicNavbar from '@/components/layout/PublicNavbar.vue'
import AppFooter from '@/components/layout/AppFooter.vue'

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const loading = ref(false)

const auth = useAuthStore()
const router = useRouter()

async function handleLogin() {
    loading.value = true
    await auth.login(email.value, password.value)
    loading.value = false
    if (auth.user) {
        router.push('/dashboard')
    }
}
</script>
