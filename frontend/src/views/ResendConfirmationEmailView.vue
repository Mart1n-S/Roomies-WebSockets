<template>
    <PublicNavbar />

    <div class="relative flex items-center justify-center min-h-screen px-4 text-white bg-roomies-gray5">

        <!-- Formulaire de renvoi -->
        <RequestNewConfirmationEmailForm class="z-10" />

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
import PublicNavbar from '@/components/layout/PublicNavbar.vue'
import AppFooter from '@/components/layout/AppFooter.vue'
import RequestNewConfirmationEmailForm from '@/components/form/RequestNewConfirmationEmailForm.vue'
</script>
