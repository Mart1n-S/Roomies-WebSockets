<template>
    <div class="relative flex flex-col items-center gap-2 p-4 shadow bg-roomies-gray3 rounded-xl">
        <img :src="gameImage" alt="Jeu" class="w-40 h-40 rounded-lg" />

        <h3 class="text-lg font-bold text-center text-white break-all">{{ room.name }}</h3>
        <p class="text-sm text-blue-400">{{ gameLabel }}</p>

        <div class="flex items-center gap-2 mt-2">
            <img :src="room.creator.avatar" class="rounded-full w-7 h-7" />
            <span class="text-sm text-gray-300">{{ room.creator.pseudo }}</span>
        </div>

        <!-- Nombre de joueurs avec couleur dynamique -->
        <div class="mt-2 text-xs font-semibold" :class="room.playersCount >= 2 ? 'text-red-400' : 'text-green-400'">
            <i class="mr-1 pi pi-users" />
            {{ room.playersCount ?? 0 }} / 2
        </div>

        <!-- Nombre de spectateurs en violet -->
        <div class="mt-1 text-xs font-semibold text-purple-400">
            <i class="mr-1 pi pi-eye" />
            {{ room.viewersCount }} spectateur{{ room.viewersCount > 1 ? 's' : '' }}
        </div>

        <BaseButton class="w-full mt-2" :disabled="room.playersCount >= 2" @click="$emit('join', room)">
            Rejoindre
        </BaseButton>

        <BaseButton class="w-full mt-2" variant="secondary" @click="$emit('watch', room)">
            Regarder en spectateur
        </BaseButton>

        <!-- Bouton Supprimer si propriétaire -->
        <BaseButton v-if="isOwner" variant="danger" class="w-full mt-2" @click.stop="showDeleteModal = true">
            <template #icon-left>
                <i class="pi pi-trash" />
            </template>
            Supprimer la partie
        </BaseButton>

        <!-- Modal confirmation -->
        <ConfirmDeleteModal v-if="showDeleteModal" title="Supprimer la partie"
            message="Supprimer cette partie ? Cette action est irréversible." :onConfirm="deleteRoom"
            @close="showDeleteModal = false" />
    </div>
</template>

<script setup lang="ts">
import type { RoomCard } from '@/models/RoomCard'
import BaseButton from '@/components/base/BaseButton.vue'
import ConfirmDeleteModal from '@/components/base/ConfirmDeleteModal.vue'
import { computed, ref } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useWebSocketStore } from '@/stores/wsStore'

import morpionImg from '@/assets/images/morpion.svg'
import puissance4Img from '@/assets/images/puissance4.svg'

const props = defineProps<{ room: RoomCard }>()
const emit = defineEmits(['join', 'watch'])

const authStore = useAuthStore()
const wsStore = useWebSocketStore()

const showDeleteModal = ref(false)

const gameLabel = computed(() =>
    props.room.game === 'morpion'
        ? 'Morpion'
        : props.room.game === 'puissance4'
            ? 'Puissance 4'
            : props.room.game
)

const gameImage = computed(() =>
    props.room.game === 'morpion'
        ? morpionImg
        : props.room.game === 'puissance4'
            ? puissance4Img
            : morpionImg // fallback
)

const isOwner = computed(() =>
    authStore.user?.friendCode && props.room.creator.friendCode === authStore.user.friendCode
)

// Envoie la suppression
function deleteRoom() {
    wsStore.send({
        type: 'game_room_delete',
        payload: { roomId: props.room.id }
    })
    showDeleteModal.value = false
}
</script>
