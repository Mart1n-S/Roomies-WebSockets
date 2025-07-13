<template>
    <div class="flex h-screen text-white bg-roomies-gray5">
        <Sidebar @open-create-modal="openCreateModal" />
        <ContextPanel />
        <main class="flex-1 overflow-y-auto">
            <RouterView />
        </main>
        <LoadingScreen v-if="showLoader" />
        <CreateGroupModalForm v-if="showCreateModal" @close="showCreateModal = false" />
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watchEffect } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useContextPanelStore } from '@/stores/contextPanelStore'
import { useRoute } from 'vue-router'

import Sidebar from '@/components/layout/Sidebar.vue'
import LoadingScreen from '@/components/LoadingScreen.vue'
import CreateGroupModalForm from '@/components/form/CreateGroupModalForm.vue'
import ContextPanel from '@/components/layout/ContextPanel.vue'


const auth = useAuthStore()
const contextPanel = useContextPanelStore()
const route = useRoute()

const showLoader = ref(true)
const showCreateModal = ref(false)

watchEffect(() => {
    showLoader.value = !auth.userFetched && !auth.user
})

// Appelé quand on clique sur le bouton "+"
const openCreateModal = () => {
    showCreateModal.value = true
}

onMounted(() => {
    if (route.path === '/dashboard') {
        contextPanel.showPrivateMessagesPanel()
    }
})
</script>
