<template>
    <div class="flex flex-col items-center justify-center w-full">
        <!-- Statut -->
        <div class="mb-4 text-center min-h-[2.5rem]">
            <template v-if="waitingForPlayer">
                <span class="flex items-center justify-center gap-2 text-lg font-bold text-gray-400">
                    <i class="mr-1 pi pi-clock"></i>
                    En attente d’un autre joueur…
                </span>
            </template>
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

        <!-- Plateau Puissance 4 (header EN BAS) -->
        <div class="grid grid-cols-7 gap-1">
            <!-- Grille haut vers bas (ligne 0 en haut JS = ligne 0 en haut visuel) -->
            <div v-for="row in 6" :key="'row-' + row" class="contents">
                <div v-for="col in 7" :key="'cell-' + row + '-' + col"
                    class="flex items-center justify-center w-10 h-10 transition-all border-2 rounded-full border-roomies-gray3"
                    :class="{
                        'bg-roomies-gray2': props.state.board[row - 1][col - 1] === null,
                        'bg-red-500': props.state.board[row - 1][col - 1] === 'R',
                        'bg-yellow-400': props.state.board[row - 1][col - 1] === 'Y',
                        'opacity-60': props.state.board[row - 1][col - 1] !== null
                    }">
                </div>
            </div>
            <!-- Flèches pour jouer EN BAS -->
            <div v-for="col in 7" :key="'header-' + col"
                class="flex items-center justify-center w-10 h-6 text-xs font-bold text-white rounded-b cursor-pointer bg-roomies-blue/80"
                :class="{ 'opacity-50 cursor-not-allowed': !isMyTurn || !isPlayer || state.winnerIndex !== null || state.draw || columnFull(col - 1) }"
                @click="handleColumnClick(col - 1)">
                ▲
            </div>
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
        board: (string | null)[][]
        currentPlayerIndex: number
        winnerIndex: number | null
        draw: boolean
        status: 'playing' | 'win' | 'draw'
    },
    players: Array<{ pseudo: string; avatar: string; friendCode: string }>
    myIndex: number
    waitingRestart: boolean
}>()

const emit = defineEmits<{
    (e: 'play', col: number): void
    (e: 'replay'): void
}>()

const waitingForPlayer = computed(() => props.players.length < 2)
const isPlayer = computed(() => props.myIndex === 0 || props.myIndex === 1)
const isMyTurn = computed(() =>
    !waitingForPlayer.value &&
    props.myIndex === props.state.currentPlayerIndex &&
    props.state.winnerIndex === null &&
    !props.state.draw
)
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
function columnFull(col: number) {
    return props.state.board[0][col] !== null
}
function handleColumnClick(col: number) {
    if (!isMyTurn.value || columnFull(col) || !isPlayer.value) return
    emit('play', col)
}
</script>
