import { defineStore } from 'pinia'
import { ref } from 'vue'
import { useWebSocketStore } from '@/stores/wsStore'
import type { RoomCard } from '@/models/RoomCard'
import type { UserPublic } from '@/models/User'
import type { ChatMessage } from '@/models/Message'

export const useGameStore = defineStore('game', () => {
    // Rooms classiques (liste, lobby)
    const rooms = ref<RoomCard[]>([])
    const isLoading = ref(false)

    // Pour la room courante
    const currentRoomPlayers = ref<UserPublic[]>([])
    const currentRoomId = ref<string | null>(null)
    const morpionScores = ref<{ [friendCode: string]: number }>({})

    // Chat par room (réactif)
    const chatMessagesByRoom = ref<Record<string, ChatMessage[]>>({})

    // --- Nouvel état pour le Morpion ---
    const morpionGameState = ref({
        board: Array(9).fill(null) as (string | null)[],
        currentPlayerIndex: 0,
        winnerIndex: null as number | null,
        draw: false,
        status: 'playing' as 'playing' | 'win' | 'draw'
    })

    // -----
    const wsStore = useWebSocketStore()

    // --- Gestion lobby
    const setRooms = (list: RoomCard[]) => {
        rooms.value = list.map(newRoom => {
            const existingRoom = rooms.value.find(r => r.id === newRoom.id)
            return {
                ...newRoom,
                viewersCount: existingRoom?.viewersCount ?? 0
            }
        })
    }


    const addRoom = (room: RoomCard) => {
        rooms.value = [{
            ...room,
            viewersCount: room.viewersCount ?? 0
        }, ...rooms.value]
    }


    const fetchRooms = () => {
        isLoading.value = true
        wsStore.send({ type: 'game_room_list' })
    }

    const createRoom = (payload: { name: string; game: string }) => {
        isLoading.value = true
        wsStore.send({ type: 'game_room_create', payload })
    }

    // --- Room courante ---
    const setCurrentRoomPlayers = (players: UserPublic[], roomId: string) => {
        currentRoomPlayers.value = players
        currentRoomId.value = roomId
    }

    const addPlayerToCurrentRoom = (player: UserPublic) => {
        if (!currentRoomPlayers.value.find(p => p.friendCode === player.friendCode)) {
            currentRoomPlayers.value.push(player)
        }
    }

    const removePlayerFromCurrentRoom = (friendCode: string) => {
        currentRoomPlayers.value = currentRoomPlayers.value.filter(p => p.friendCode !== friendCode)
        resetMorpionGameState()
    }

    const resetCurrentRoom = () => {
        currentRoomId.value = null
        currentRoomPlayers.value = []
        resetMorpionGameState()
        resetScores()
    }

    const resetMorpionGameState = () => {
        morpionGameState.value = {
            board: Array(9).fill(null),
            currentPlayerIndex: 0,
            winnerIndex: null,
            draw: false,
            status: 'playing'
        }
    }

    const resetScores = () => {
        morpionScores.value = {}
        if (currentRoomId.value) {
            delete chatMessagesByRoom.value[currentRoomId.value]
        }
    }

    const playMorpionMove = (position: number) => {
        if (!currentRoomId.value) return
        wsStore.send({
            type: 'morpion_play',
            roomId: currentRoomId.value,
            position
        })
    }

    const setMorpionGameState = (payload: any) => {
        morpionGameState.value.board = payload.board ?? Array(9).fill(null)
        morpionGameState.value.currentPlayerIndex = payload.currentPlayerIndex ?? 0
        morpionGameState.value.winnerIndex = payload.winnerIndex ?? null
        morpionGameState.value.draw = !!payload.draw
        morpionGameState.value.status = payload.status || (
            payload.winner !== null ? 'win' : (payload.draw ? 'draw' : 'playing')
        )

        if (
            payload.winnerIndex !== undefined &&
            payload.winnerIndex !== null &&
            !payload.draw
        ) {
            // Récupère le joueur gagnant dans currentRoomPlayers (attention à l’index !)
            const winner = currentRoomPlayers.value[payload.winnerIndex]
            if (winner) {
                morpionScores.value[winner.friendCode] = (morpionScores.value[winner.friendCode] || 0) + 1
            }
        }
    }

    const incrementViewerCount = (roomId: string) => {
        const idx = rooms.value.findIndex(r => r.id === roomId)
        if (idx !== -1) {
            rooms.value[idx].viewersCount = (rooms.value[idx].viewersCount || 0) + 1
        }
    }

    // Ajouter un message dans la bonne room
    const addChatMessage = (message: ChatMessage) => {
        const roomId = message.roomId
        if (!chatMessagesByRoom.value[roomId]) {
            chatMessagesByRoom.value[roomId] = []
        }
        chatMessagesByRoom.value[roomId].push(message)
    }

    const removeRoom = (roomId: string) => {
        rooms.value = rooms.value.filter(r => String(r.id) !== String(roomId))
        delete chatMessagesByRoom.value[roomId]
    }


    return {
        // Lobby
        rooms, isLoading, setRooms, addRoom, fetchRooms, createRoom, removeRoom,
        // Room courante
        currentRoomPlayers, currentRoomId, setCurrentRoomPlayers, addPlayerToCurrentRoom, removePlayerFromCurrentRoom, resetCurrentRoom, incrementViewerCount,
        // Morpion
        morpionGameState, setMorpionGameState, playMorpionMove, morpionScores, resetScores, resetMorpionGameState,
        // Chat
        chatMessagesByRoom, addChatMessage
    }
})
