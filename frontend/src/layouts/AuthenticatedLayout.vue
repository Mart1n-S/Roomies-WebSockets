<template>
    <div class="flex h-screen text-white bg-roomies-gray5">
        <Sidebar />
        <main class="flex-1 overflow-y-auto">
            <RouterView />
        </main>
        <LoadingScreen v-if="showLoader" />
    </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore'
import { ref, watchEffect } from 'vue'
import Sidebar from '@/components/Sidebar.vue'
import LoadingScreen from '@/components/LoadingScreen.vue'

const auth = useAuthStore()
const showLoader = ref(true)

watchEffect(() => {
    showLoader.value = !auth.userFetched && !auth.user
})
</script>