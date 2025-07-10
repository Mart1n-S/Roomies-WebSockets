<template>
    <PublicNavbar />

    <div class="relative flex items-center justify-center min-h-screen px-4 py-24 text-white bg-roomies-gray5">
        <RegisterForm />

        <!-- Illustration principale en fond -->
        <img src="@/assets/images/hero-illustration-form.svg"
            class="absolute inset-0 z-0 object-cover w-full h-full pointer-events-none" alt="" />

        <!-- Décorations latérales -->
        <img src="@/assets/images/hero-illustration-left-2.svg"
            class="absolute bottom-0 left-0 z-0 w-1/3 max-w-xs pointer-events-none" alt="" />
        <img src="@/assets/images/hero-illustration-right-2.svg"
            class="absolute bottom-0 right-0 z-0 w-1/3 max-w-xs pointer-events-none" alt="" />
    </div>

    <AppFooter />
</template>

<script setup lang="ts">
import PublicNavbar from '@/components/layout/PublicNavbar.vue'
import AppFooter from '@/components/layout/AppFooter.vue'
import RegisterForm from '@/components/form/RegisterForm.vue'
</script>