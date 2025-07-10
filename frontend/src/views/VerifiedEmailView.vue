<template>
    <PublicNavbar />

    <div class="relative flex items-center justify-center min-h-screen px-4 py-24 text-white bg-roomies-gray5">
        <!-- Message principal -->
        <div class="z-10 max-w-lg p-8 space-y-4 text-center shadow-xl rounded-xl bg-roomies-gray3">
            <h1 class="text-3xl font-bold">
                {{ status === 'success' ? '🎉 Email confirmé !' : '❌ Échec de la vérification' }}
            </h1>
            <p class="text-lg">
                {{ messageText }}
            </p>
            <RouterLink v-if="status === 'success'" to="/login"
                class="inline-flex items-center justify-center w-full px-4 py-2 font-medium text-white transition rounded disabled:opacity-50 bg-roomies-blue hover:bg-roomies-hover active:bg-roomies-active">
                Se connecter
            </RouterLink>

            <RouterLink v-else to="/resend-confirmation-email"
                class="inline-flex items-center justify-center w-full px-4 py-2 font-medium text-white transition rounded disabled:opacity-50 bg-roomies-blue hover:bg-roomies-hover active:bg-roomies-active">
                Renvoyer un email de confirmation
            </RouterLink>
        </div>

        <!-- Décorations -->
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
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import PublicNavbar from '@/components/layout/PublicNavbar.vue'
import AppFooter from '@/components/layout/AppFooter.vue'

const route = useRoute()

const status = computed(() => route.query.status as string || '')
const message = computed(() => route.query.message as string || '')
const code = computed(() => route.query.code as string || '')

const messageText = computed(() => {
    if (status.value === 'success') {
        return 'Votre email a bien été confirmé. Vous pouvez maintenant vous connecter à Roomies.'
    }

    switch (message.value || code.value) {
        case 'missing_id':
            return 'Le lien de confirmation est invalide (ID manquant).'
        case 'user_not_found_or_already_verified':
            return 'Ce lien n’est plus valide ou l’utilisateur a déjà été confirmé.'
        case 'expired_link':
            return 'Ce lien de confirmation a expiré. Veuillez refaire une demande.'
        case 'expired':
            return 'Ce lien de confirmation a expiré. Veuillez refaire une demande.'
        default:
            return 'Une erreur est survenue lors de la vérification de l’email.'
    }
})
</script>
