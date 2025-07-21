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
import { onMounted, ref, watch } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useFriendshipStore } from '@/stores/friendshipStore'
import { useContextPanelStore } from '@/stores/contextPanelStore'
import { useRoute } from 'vue-router'

import Sidebar from '@/components/layout/Sidebar.vue'
import LoadingScreen from '@/components/UI/LoadingScreen.vue'
import CreateGroupModalForm from '@/components/form/CreateGroupModalForm.vue'
import ContextPanel from '@/components/layout/ContextPanel.vue'

// Importe le service push
import { askPermission, subscribeUserToPush, sendSubscriptionToBackEnd } from '@/services/pushService'

const auth = useAuthStore()
const friendshipStore = useFriendshipStore()
const contextPanel = useContextPanelStore()
const route = useRoute()

const showLoader = ref(true)
const showCreateModal = ref(false)
const alreadyCheckedPush = ref(false)

watch(
    () => auth.user,
    async (user) => {
        // Stop si pas d'user OU si déjà traité dans cette session
        if (!user || alreadyCheckedPush.value) return;

        await friendshipStore.initFriendshipStore();

        if (user.pushNotificationsEnabled === undefined || user.pushNotificationsEnabled === false) {
            try {
                const permission = await askPermission();
                if (permission === 'granted') {
                    const sub = await subscribeUserToPush();
                    const updatedUser = await sendSubscriptionToBackEnd(sub, true);
                    auth.user = updatedUser;
                } else {
                    const updatedUser = await sendSubscriptionToBackEnd(null, false);
                    auth.user = updatedUser;
                }
            } catch (e) {
                console.log('Notification push refusée ou erreur', e);
                const updatedUser = await sendSubscriptionToBackEnd(null, false);
                auth.user = updatedUser;
            }
        }
        alreadyCheckedPush.value = true; // Ne traite plus dans cette session
    },
    { immediate: true }
);

watch(
    () => auth.appReady,
    (ready) => {
        showLoader.value = !ready
    },
    { immediate: true }
)

// Affiche le panneau contextuel selon la route
function updateContextPanel() {
    if (route.path === '/global/chat') {
        contextPanel.showGlobalChatPanel()
    } else if (
        route.path === '/dashboard' ||
        route.path.startsWith('/dashboard/chat') ||
        route.path.startsWith('/dashboard/friends')
    ) {
        contextPanel.showPrivateMessagesPanel()
    } else if (route.path.startsWith('/serveur')) {
        contextPanel.showServeurPanel()
    } else {
        contextPanel.clearPanel()
    }
}

// Toujours synchro : au premier chargement ET à chaque navigation
onMounted(updateContextPanel)
watch(() => route.path, updateContextPanel)

const openCreateModal = () => {
    showCreateModal.value = true
}
</script>
