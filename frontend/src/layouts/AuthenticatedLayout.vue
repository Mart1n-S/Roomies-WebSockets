<template>
    <div class="flex h-screen text-white bg-roomies-gray5">
        <Sidebar @open-create-modal="openCreateModal" />
        <main class="flex-1 overflow-y-auto">
            <RouterView />
        </main>
        <LoadingScreen v-if="showLoader" />
        <CreateGroupModalForm v-if="showCreateModal" @close="showCreateModal = false" />
    </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore'
import { ref, watchEffect } from 'vue'
import Sidebar from '@/components/Sidebar.vue'
import LoadingScreen from '@/components/LoadingScreen.vue'
import CreateGroupModalForm from '@/components/form/CreateGroupModalForm.vue'

const auth = useAuthStore()
const showLoader = ref(true)
const showCreateModal = ref(false)

watchEffect(() => {
    showLoader.value = !auth.userFetched && !auth.user
})

// Fonction à passer à la sidebar
const openCreateModal = () => {
    showCreateModal.value = true
}
</script>
