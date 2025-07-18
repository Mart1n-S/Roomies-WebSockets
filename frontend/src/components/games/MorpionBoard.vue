<template>
    <div class="flex flex-col items-center justify-center w-full">
        <!-- Infos parties / statut -->
        <div class="mb-4 text-center min-h-[2.5rem]">
            <!-- Si on attend un joueur -->
            <template v-if="waitingForPlayer">
                <span class="flex items-center justify-center gap-2 text-lg font-bold text-gray-400">
                    <i class="mr-1 pi pi-clock"></i>
                    En attente d’un autre joueur…
                </span>
            </template>
            <!-- Sinon, gestion normale -->
            <template v-else-if="state.winnerIndex !== null || state.draw">
                <span v-if="state.draw"
                    class="flex items-center justify-center gap-2 text-lg font-bold text-yellow-400">
                    Match nul ! <span>🤝</span>
                </span>
                <span v-else class="flex items-center justify-center gap-2 text-lg font-bold text-green-400">
                    Victoire :
                    <span v-if="winnerInfo">
                        <img :src="winnerInfo.avatar"
                            class="inline-block w-6 h-6 mx-1 border-2 border-green-400 rounded-full" />
                        {{ winnerInfo.pseudo }}
                    </span>
                    <span v-else>Joueur {{ winnerPlayer }}</span>
                    🎉
                </span>
            </template>
            <template v-else>
                <span v-if="isMyTurn" class="font-semibold text-blue-400">C’est ton tour !</span>
                <span v-else-if="isPlayer" class="text-white">Tour de l’autre joueur…</span>
                <span v-else class="text-white">Partie en cours</span>
            </template>
        </div>

        <!-- Plateau Morpion (désactivé si on attend un joueur) -->
        <div class="grid w-56 h-56 grid-cols-3 gap-2 select-none">
            <button v-for="(cell, idx) in state.board" :key="idx"
                :disabled="players.length < 2 || !isMyTurn || !!cell || !!state.winnerIndex || state.draw"
                class="flex items-center justify-center w-16 h-16 text-4xl font-extrabold transition-all duration-100 ease-in-out border-2 rounded-lg border-roomies-gray3 bg-roomies-gray2 hover:bg-roomies-blue/70 focus:outline-none"
                :class="{
                    'cursor-not-allowed opacity-70': players.length < 2 || cell || !isMyTurn || state.winnerIndex !== null || state.draw,
                    'hover:bg-blue-900/80': !cell && isMyTurn && state.winnerIndex === null && !state.draw && players.length >= 2,
                }" @click="() => $emit('play', idx)" :aria-label="'Cellule ' + (idx + 1)">
                <span v-if="cell === 'X'" class="text-blue-400 drop-shadow-[0_2px_0_rgba(0,0,0,0.2)]">X</span>
                <span v-else-if="cell === 'O'" class="text-pink-400 drop-shadow-[0_2px_0_rgba(0,0,0,0.2)]">O</span>
            </button>
        </div>

        <!-- Bouton rejouer -->
        <div v-if="(state.status === 'win' || state.draw) && players.length >= 2 && isPlayer" class="mt-4">
            <BaseButton @click="$emit('replay')" :disabled="waitingRestart" variant="primary" class="min-w-[120px]">
                <template #icon-left>
                    <i class="pi pi-refresh" :class="{ 'animate-spin': waitingRestart }" />
                </template>
                {{ waitingRestart ? 'En attente...' : 'Rejouer' }}
            </BaseButton>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import BaseButton from '@/components/base/BaseButton.vue'

const props = defineProps<{
    state: {
        board: (string | null)[]
        currentPlayerIndex: number
        winnerIndex: number | null
        draw: boolean
        status: 'playing' | 'win' | 'draw'
    },
    players: Array<{
        pseudo: string
        avatar: string
        friendCode: string
    }>
    myIndex: number
    waitingRestart: boolean
}>()

const waitingForPlayer = computed(() => props.players.length < 2)

// Suis-je joueur ?
const isPlayer = computed(() => props.myIndex === 0 || props.myIndex === 1)
// Est-ce mon tour ?
const isMyTurn = computed(() =>
    !waitingForPlayer.value &&
    props.myIndex === props.state.currentPlayerIndex &&
    props.state.winnerIndex === null &&
    !props.state.draw
)
// Info du gagnant si victoire
const winnerInfo = computed(() => {
    if (
        props.state.winnerIndex !== null &&
        Array.isArray(props.players) &&
        props.players[props.state.winnerIndex]
    ) {
        return props.players[props.state.winnerIndex]
    }
    return null
})
const winnerPlayer = computed(() =>
    props.state.winnerIndex !== null ? props.state.winnerIndex + 1 : '?'
)
</script>
