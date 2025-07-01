<template>
    <nav class="bg-roomies-gray4 text-white shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <!-- Logo -->
            <RouterLink to="/" class="flex items-center gap-3">
                <img src="@/assets/images/icons/ic_roomies_outline.svg" alt="Roomies Logo" class="w-8 h-8" />
                <span class="text-lg font-bold tracking-wide">Roomies</span>
            </RouterLink>

            <!-- Mobile menu button -->
            <button class="md:hidden text-white" @click="isOpen = !isOpen" aria-label="Toggle navigation">
                <svg v-if="!isOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Navigation Links (desktop) -->
            <div class="hidden md:flex gap-6 items-center text-sm font-medium">
                <RouterLink to="/" class="hover:underline transition" :class="{ 'underline': route.name === 'home' }">
                    Accueil
                </RouterLink>
                <RouterLink to="/register" class="hover:underline transition"
                    :class="{ 'underline': route.name === 'register' }">
                    Inscription
                </RouterLink>
                <RouterLink to="/login" class="hover:underline transition"
                    :class="{ 'underline': route.name === 'login' }">
                    Connexion
                </RouterLink>
            </div>
        </div>

        <!-- Navigation Links (mobile dropdown) -->
        <div v-if="isOpen" class="md:hidden px-4 pb-4 flex flex-col gap-4 text-sm font-medium text-center">
            <RouterLink to="/" class="hover:underline transition py-2" @click="isOpen = false"
                :class="{ 'underline': route.name === 'home' }">
                Accueil
            </RouterLink>
            <RouterLink to="/register" class="hover:underline transition py-2" @click="isOpen = false"
                :class="{ 'underline': route.name === 'register' }">
                Inscription
            </RouterLink>
            <RouterLink to="/login" class="hover:underline transition py-2" @click="isOpen = false"
                :class="{ 'underline': route.name === 'login' }">
                Connexion
            </RouterLink>
        </div>

    </nav>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRoute, RouterLink } from 'vue-router'

const route = useRoute()
const isOpen = ref(false)
</script>
