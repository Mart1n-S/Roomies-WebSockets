<template>
    <div class="h-full p-6 text-white">
        <h1 class="mb-1 text-2xl font-bold">Jeux en ligne</h1>
        <p class="mb-6 text-gray-400">Rejoins ou crée une partie avec tes amis en temps réel !</p>

        <!-- Barre de filtres et création -->
        <div class="flex flex-col-reverse items-stretch justify-between gap-4 mb-8 sm:flex-row sm:items-end">
            <!-- Filtres -->
            <div class="flex flex-col w-full gap-3 sm:flex-row sm:w-auto sm:flex-1 sm:max-w-2xl">
                <BaseInput name="search-game" label="Rechercher une partie" v-model="search" autocomplete="off"
                    placeholder="Rechercher une room..." type="text" class="flex-1 min-w-[200px]"
                    input-class="py-2.5" />

                <div class="flex flex-col">
                    <label for="game-filter" class="mb-1 text-sm text-gray-300">Filtrer</label>
                    <select id="game-filter" name="game-filter" v-model="filter"
                        class="h-[42px] px-3 py-2.5 text-sm text-white transition-all duration-200 border rounded-md bg-roomies-gray2 border-roomies-gray3 hover:border-roomies-blue focus:border-roomies-blue focus:ring-1 focus:ring-roomies-blue/30 focus:outline-none">
                        <option value="all">Toutes les parties</option>
                        <option value="joinable">Parties rejoignables (1/2)</option>
                        <option value="empty">Parties vides (0/2)</option>
                        <option value="mine">Mes parties</option>
                    </select>
                </div>
                <!-- Sélecteur de jeu -->
                <div class="flex flex-col">
                    <label for="select-game-type" class="mb-1 text-sm text-gray-300">Jeu</label>
                    <select id="select-game-type" v-model="gameSelected"
                        class="h-[42px] px-3 py-2.5 text-sm text-white transition-all duration-200 border rounded-md bg-roomies-gray2 border-roomies-gray3 hover:border-roomies-blue focus:border-roomies-blue focus:ring-1 focus:ring-roomies-blue/30 focus:outline-none">
                        <option value="">Tous les jeux</option>
                        <option value="morpion">Morpion</option>
                        <option value="puissance4">Puissance 4</option>
                    </select>
                </div>
            </div>

            <!-- Bouton de création -->
            <BaseButton @click="showModal = true" class="self-stretch sm:self-end h-[42px]" variant="primary">
                <template #icon-left>
                    <i class="pi pi-plus" />
                </template>
                Créer une partie
            </BaseButton>
        </div>

        <!-- Loading -->
        <div v-if="gameStore.isLoading" class="text-center text-gray-400">
            Chargement des parties...
        </div>

        <!-- Affichage des rooms filtrées -->
        <div v-else-if="filteredRooms.length > 0" class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
            <GameCard v-for="room in filteredRooms" :key="room.id" :room="room" @join="joinRoom" @watch="watchRoom" />
        </div>

        <!-- Aucun résultat -->
        <div v-else class="py-12 italic text-center text-gray-400">
            Aucune partie trouvée 😶‍🌫️
        </div>

        <!-- Modal de création -->
        <GameCreateModalForm v-if="showModal" @close="showModal = false" />
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useGameStore } from '@/stores/gameStore'
import { useWebSocketStore } from '@/stores/wsStore'
import GameCard from '@/components/games/GameCard.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import GameCreateModalForm from '@/components/form/GameCreateModalForm.vue'
import type { RoomCard } from '@/models/RoomCard'
import { useAuthStore } from '@/stores/authStore'

const showModal = ref(false)
const search = ref('')
const filter = ref<'all' | 'joinable' | 'empty' | 'mine'>('all')
const gameSelected = ref('')

const gameStore = useGameStore()
const wsStore = useWebSocketStore()
const authStore = useAuthStore()

onMounted(() => {
    gameStore.fetchRooms()
})

// Appliquer recherche + filtre + filtre jeu
const filteredRooms = computed(() => {
    const query = search.value.trim().toLowerCase()
    const myFriendCode = authStore.user?.friendCode

    return gameStore.rooms.filter(room => {
        const matchName = room.name.toLowerCase().includes(query)

        let matchFilter = false

        if (filter.value === 'all') {
            matchFilter = true
        } else if (filter.value === 'joinable') {
            matchFilter = room.playersCount === 1
        } else if (filter.value === 'empty') {
            matchFilter = room.playersCount === 0
        } else if (filter.value === 'mine') {
            matchFilter = !!myFriendCode && room.creator.friendCode === myFriendCode
        }

        // Nouveau : filtre sur le type de jeu
        const matchGame = !gameSelected.value || room.game === gameSelected.value

        return matchName && matchFilter && matchGame
    })
})

function joinRoom(room: any) {
    wsStore.send({
        type: 'game_room_join',
        roomId: room.id
    })
}

function watchRoom(room: RoomCard) {
    wsStore.send({
        type: 'game_room_watch',
        roomId: room.id
    })
}
</script>
