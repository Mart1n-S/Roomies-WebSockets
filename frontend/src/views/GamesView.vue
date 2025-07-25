<template>
    <div class="flex flex-col h-full p-6 text-white">
        <!-- Header stylé pour les joueurs de la partie -->
        <div class="mb-6">
            <div
                class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-b shadow-lg rounded-t-xl bg-roomies-gray2 border-roomies-gray3">
                <div class="flex items-center gap-2">
                    <i class="mr-2 text-lg text-blue-400 pi pi-users"></i>
                    <span class="text-lg font-bold tracking-wide text-white">Joueurs dans la partie</span>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Nombre de spectateurs -->
                    <div class="flex items-center gap-1 text-sm text-purple-400">
                        <i class="pi pi-eye"></i>
                        {{ viewersCount }} spectateur{{ viewersCount > 1 ? 's' : '' }}
                    </div>

                    <!-- Bouton Quitter -->
                    <BaseButton size="sm" variant="danger" iconLeft @click="quitRoom">
                        <template #icon-left>
                            <i class="pi pi-sign-out"></i>
                        </template>
                        Quitter
                    </BaseButton>
                </div>
            </div>

            <ul class="px-4 py-2 bg-roomies-gray3 rounded-b-xl">
                <li v-for="(player, idx) in players" :key="player.friendCode"
                    class="flex items-center gap-3 py-1 mb-2 border-b last:border-b-0 border-roomies-gray1">
                    <div class="relative">
                        <img :src="player.avatar"
                            class="w-10 h-10 transition-opacity border-2 rounded-full cursor-pointer hover:opacity-80"
                            :class="idx === 0 ? 'border-blue-400' : 'border-pink-400'" @click="openProfile(player)" />
                        <span class="absolute -bottom-1 -right-1 rounded-full px-1.5 py-0.5 text-xs font-bold"
                            :class="idx === 0 ? 'bg-blue-400 text-white' : 'bg-pink-400 text-white'">
                            {{ idx === 0 ? 'X' : 'O' }}
                        </span>
                    </div>
                    <span class="font-bold text-white transition-colors cursor-pointer hover:text-blue-300"
                        @click="openProfile(player)">
                        {{ player.pseudo }}
                    </span>
                    <span v-if="scores[player.friendCode] !== undefined"
                        class="flex items-center ml-auto text-xs font-bold text-yellow-300">
                        <i class="mr-1 text-yellow-300 pi pi-trophy"></i>
                        {{ scores[player.friendCode] }}
                    </span>
                </li>
            </ul>
        </div>

        <!-- Section principale avec Morpion + Chat -->
        <div class="flex h-full gap-6">
            <!-- Jeu Morpion -->
            <div class="flex flex-col flex-1 p-4 shadow-lg bg-roomies-gray4 rounded-xl min-h-[500px]">
                <div class="flex flex-col items-center justify-center flex-1">
                    <MorpionBoard v-if="morpion" :state="morpion" :players="players" :my-index="myIndex"
                        :waiting-restart="waitingRestart" @replay="replay" @play="handlePlay" />
                    <div v-else class="text-gray-400">En attente des joueurs…</div>
                </div>
            </div>

            <!-- Chat de la partie -->
            <div class="w-96">
                <GameChat v-if="roomId && myIndex !== -1" :room-id="roomId" />
                <div v-else
                    class="p-4 text-sm text-center text-gray-400 border rounded-lg border-roomies-gray2 bg-roomies-gray3">
                    Le chat est réservé aux joueurs de la partie. 🫣
                </div>
            </div>

        </div>

        <!-- Profil Public Modal -->
        <ProfilPublicModalForm v-if="selectedUser" :user="selectedUser" @close="closeProfile"
            @add-friend="handleAddFriend" />
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useGameStore } from '@/stores/gameStore'
import { useWebSocketStore } from '@/stores/wsStore'
import { useAuthStore } from '@/stores/authStore'
import MorpionBoard from '@/components/games/MorpionBoard.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import GameChat from '@/components/games/GameChat.vue'
import ProfilPublicModalForm from '@/components/form/ProfilPublicModalForm.vue'

const route = useRoute()
const router = useRouter()
const roomId = String(route.params.roomId)
const gameStore = useGameStore()
const wsStore = useWebSocketStore()
const authStore = useAuthStore()

const selectedUser = ref(null as null | { pseudo: string, avatar: string, friendCode: string })

const players = computed(() => gameStore.currentRoomPlayers)
const morpion = computed(() => gameStore.morpionGameState)
const scores = computed(() => gameStore.morpionScores)
const myFriendCode = computed(() => authStore.user?.friendCode || null)
const myIndex = computed(() => players.value.findIndex(p => p.friendCode === myFriendCode.value))
const waitingRestart = computed(() => wsStore.morpionWaitingRestart)
const viewersCount = computed(() => {
    const room = gameStore.rooms.find(r => r.id === roomId)
    return room?.viewersCount ?? 0
})


function openProfile(user: { pseudo: string, avatar: string, friendCode: string }) {
    selectedUser.value = user
}

function closeProfile() {
    selectedUser.value = null
}

function handleAddFriend(friendCode: string) {
    wsStore.send({
        type: 'friend_request',
        payload: { friendCode }
    })
    closeProfile()
}

function handlePlay(idx: number) {
    gameStore.playMorpionMove(idx)
}

function replay() {
    wsStore.send({ type: 'morpion_restart', roomId })
}

let hasQuit = false
function quitRoomCore() {
    if (hasQuit) return
    wsStore.send({ type: 'game_room_quit', roomId })
    gameStore.resetCurrentRoom()
    gameStore.resetScores()
    hasQuit = true
}

function quitRoom() {
    quitRoomCore()
    router.push('/games')
}

onMounted(() => {
    if (gameStore.currentRoomId !== roomId) {
        wsStore.send({ type: 'game_room_join', roomId })
    }
    gameStore.resetScores()
})

onUnmounted(() => {
    quitRoomCore()
})
</script>