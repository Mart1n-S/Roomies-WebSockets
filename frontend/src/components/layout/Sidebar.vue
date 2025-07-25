<template>
    <aside class="flex flex-col items-center justify-between w-16 h-full py-4 bg-roomies-gray4"
        data-testid="sidebar-main">
        <!-- Partie haute -->
        <div class="flex flex-col items-center space-y-3">
            <!-- Dashboard -->
            <BaseButton iconLeft variant="secondary" noIconSpace class="!w-12 !h-12 !p-0 justify-center"
                :class="dashboardClass" title="Tableau de bord" @click="navigateTo('/dashboard')">
                <template #icon-left>
                    <i class="text-lg pi pi-discord" />
                </template>
            </BaseButton>

            <!-- Chat global -->
            <BaseButton iconLeft variant="secondary" noIconSpace class="!w-12 !h-12 !p-0 justify-center"
                :class="globalChatClass" title="Chat global" @click="navigateTo('/global/chat')">
                <template #icon-left>
                    <i class="text-lg pi pi-globe" />
                </template>
            </BaseButton>

            <BaseButton iconLeft variant="secondary" noIconSpace class="!w-12 !h-12 !p-0 justify-center"
                :class="gamesClass" title="Jeux en ligne" @click="navigateTo('/games')">
                <template #icon-left>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 16.5H2v5h20v-5Z"></path>
                        <path d="M19 13h-6v3.5h6V13Z"></path>
                        <path d="M9 7a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                        <path d="m8 7-3.5 9.5"></path>
                    </svg>
                </template>
            </BaseButton>
        </div>

        <hr class="w-8 h-[1px] border-roomies-gray1 my-3" />

        <!-- Partie scrollable : rooms -->
        <div class="flex-1 w-full overflow-y-auto touch-pan-y md:overflow-hidden md:hover:overflow-y-auto">
            <div class="flex flex-col items-center space-y-3">
                <BaseButton v-for="room in rooms" :key="room.id" variant="secondary"
                    class="!w-12 !h-12 !p-0 text-sm font-bold justify-center" :class="roomClass(room.id)"
                    :title="room.name" @click="navigateTo(`/serveur/chat/${room.id}`)">
                    {{ getRoomLabel(room.name) }}
                </BaseButton>
            </div>
        </div>

        <hr class="w-8 h-[1px] border-roomies-gray1 my-3" />

        <!-- Partie basse -->
        <div class="flex flex-col items-center space-y-3">
            <!-- Ajouter une room -->
            <BaseButton iconLeft variant="secondary" noIconSpace
                class="!w-12 !h-12 !p-0 !rounded-2xl hover:!rounded-xl justify-center" title="Ajouter une room"
                @click="$emit('open-create-modal')">
                <template #icon-left>
                    <i class="text-lg pi pi-plus" />
                </template>
            </BaseButton>

            <!-- Paramètres -->
            <BaseButton iconLeft variant="secondary" noIconSpace class="!w-12 !h-12 !p-0 justify-center"
                :class="settingsClass" title="Paramètres" @click="navigateTo('/settings')">
                <template #icon-left>
                    <i class="text-lg pi pi-cog" />
                </template>
            </BaseButton>

            <!-- Déconnexion -->
            <BaseButton iconLeft variant="danger" noIconSpace class="!w-12 !h-12 !p-0 justify-center"
                title="Déconnexion" @click="handleLogout">
                <template #icon-left>
                    <i class="text-lg pi pi-sign-out" />
                </template>
            </BaseButton>

        </div>
    </aside>
</template>

<script setup lang="ts">
import { useRouter, useRoute } from 'vue-router'
import { computed } from 'vue'
import BaseButton from '@/components/base/BaseButton.vue'
import { storeToRefs } from 'pinia'
import { useRoomStore } from '@/stores/roomStore'
import { useAuthStore } from '@/stores/authStore'


const router = useRouter()
const route = useRoute()

const roomStore = useRoomStore()
const { allRooms: rooms } = storeToRefs(roomStore)
const authStore = useAuthStore()

const navigateTo = (path: string) => {
    if (route.path !== path) {
        router.push(path)
    }
}

defineEmits(['open-create-modal'])

const dashboardClass = computed(() =>
    route.path.startsWith('/dashboard')
        ? '!bg-roomies-blue !rounded-xl'
        : '!rounded-2xl hover:!rounded-xl'
)

const settingsClass = computed(() =>
    route.path.startsWith('/settings')
        ? '!bg-roomies-blue !rounded-xl'
        : '!rounded-2xl hover:!rounded-xl'
)

const globalChatClass = computed(() =>
    route.path === '/global/chat'
        ? '!bg-roomies-blue !rounded-xl'
        : '!rounded-2xl hover:!rounded-xl'
)

const gamesClass = computed(() =>
    route.path === '/games'
        ? '!bg-roomies-blue !rounded-xl'
        : '!rounded-2xl hover:!rounded-xl'
)

const roomClass = (roomId: string) => {
    return route.path === `/serveur/chat/${roomId}`
        ? '!bg-roomies-blue !rounded-xl'
        : '!rounded-2xl hover:!rounded-xl'
}

/**
 * Renvoie une abréviation de 3 lettres pour un nom de groupe.
 * - Si plusieurs mots : prend la 1re lettre de chaque mot (max 3)
 * - Si un seul mot : prend les 3 premières lettres
 */
function getRoomLabel(name: string): string {
    const words = name.trim().split(/\s+/)

    if (words.length === 1) {
        return words[0].substring(0, 3).toUpperCase()
    }

    return words
        .slice(0, 3) // max 3 mots
        .map(word => word[0]?.toUpperCase() ?? '')
        .join('')
}

async function handleLogout() {
    try {
        await authStore.logout()
    } catch (err) {
        console.warn('Erreur lors du logout', err)
    }
}
</script>

<style scoped>
/* Version Desktop - Souris */
@media (pointer: fine) {
    .md\:overflow-hidden:hover {
        overflow-y: auto;
        scrollbar-gutter: stable;
    }

    .md\:overflow-hidden:hover::-webkit-scrollbar {
        width: 2px;
    }

    .md\:overflow-hidden:hover::-webkit-scrollbar-thumb {
        background-color: #6b7280;
        border-radius: 2px;
    }

    .md\:overflow-hidden:hover::-webkit-scrollbar-track {
        background-color: transparent;
    }
}

/* Version Mobile - Tactile */
@media (pointer: coarse) {
    .overflow-y-auto {
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        /* Firefox */
        touch-action: pan-y;
    }

    .overflow-y-auto::-webkit-scrollbar {
        display: none;
        /* Chrome/Safari */
    }
}
</style>