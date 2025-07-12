<template>
    <aside class="flex flex-col items-center justify-between w-16 h-full py-4 bg-roomies-gray4">
        <!-- Partie haute -->
        <div class="flex flex-col items-center space-y-3">
            <!-- Dashboard -->
            <BaseButton iconLeft variant="secondary" noIconSpace class="!w-12 !h-12 !p-0 justify-center"
                :class="dashboardClass" title="Tableau de bord" @click="navigateTo('/dashboard')">
                <template #icon-left>
                    <i class="text-lg pi pi-home" />
                </template>
            </BaseButton>
        </div>

        <div class="w-8 h-[1px] bg-roomies-gray1 my-3"></div>
        <!-- Partie scrollable : rooms -->
        <div class="flex-1 w-full overflow-y-auto touch-pan-y md:overflow-hidden md:hover:overflow-y-auto">
            <div class="flex flex-col items-center space-y-3">
                <BaseButton v-for="room in rooms" :key="room.id" variant="secondary"
                    class="!w-12 !h-12 !p-0 text-sm font-bold justify-center" :class="roomClass(room.id)"
                    :title="room.label" @click="navigateTo(`/room/${room.id}`)">
                    {{ room.label }}
                </BaseButton>
            </div>
        </div>

        <div class="w-8 h-[1px] bg-roomies-gray1 my-3"></div>
        <!-- Partie basse -->
        <div class="flex flex-col items-center space-y-3">
            <!-- Ajouter une room -->
            <BaseButton iconLeft variant="secondary" noIconSpace
                class="!w-12 !h-12 !p-0 !rounded-2xl hover:!rounded-xl justify-center" title="Ajouter une room"
                @click="createRoom">
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
        </div>
    </aside>
</template>

<script setup lang="ts">
import { useRouter, useRoute } from 'vue-router'
import { computed } from 'vue'
import BaseButton from '@/components/base/BaseButton.vue'

const router = useRouter()
const route = useRoute()

const rooms = Array.from({ length: 25 }, (_, i) => ({
    id: i + 1,
    label: ['E4', 'C-C', 'SdT', 'zzz', 'aa'][i % 5] + i
}))

const navigateTo = (path: string) => {
    if (route.path !== path) {
        router.push(path)
    }
}

const dashboardClass = computed(() =>
    route.path === '/dashboard'
        ? '!bg-roomies-blue !rounded-xl'
        : '!rounded-2xl hover:!rounded-xl'
)

const settingsClass = computed(() =>
    route.path.startsWith('/settings')
        ? '!bg-roomies-blue !rounded-xl'
        : '!rounded-2xl hover:!rounded-xl'
)

const roomClass = (roomId: number) => {
    return route.path === `/room/${roomId}`
        ? '!bg-roomies-blue !rounded-xl'
        : '!rounded-2xl hover:!rounded-xl'
}

const createRoom = () => {
    console.log('Créer une nouvelle room...')
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