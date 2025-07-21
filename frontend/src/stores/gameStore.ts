import { defineStore } from 'pinia'
import { ref } from 'vue'
import { useWebSocketStore } from '@/stores/wsStore'
import type { RoomCard } from '@/models/RoomCard'
import type { UserPublic } from '@/models/User'
import type { ChatMessage } from '@/models/Message'

/**
 * Store Pinia pour la gestion des rooms de jeu, du lobby, des scores et du chat.
 * Gère plusieurs jeux (Morpion, Puissance 4).
 */
export const useGameStore = defineStore('game', () => {
    // === ÉTAT DU LOBBY ET DES ROOMS ===
    // Liste des rooms/lobbies (lobby = salle d'attente multijoueur)
    const rooms = ref<RoomCard[]>([])
    // Indique si une opération réseau est en cours (chargement des rooms, création…)
    const isLoading = ref(false)

    // === ÉTAT DE LA ROOM COURANTE ===
    // Liste des joueurs dans la room en cours (pour l'affichage des joueurs et leur tour)
    const currentRoomPlayers = ref<UserPublic[]>([])
    // Identifiant de la room actuellement ouverte/jouée
    const currentRoomId = ref<string | null>(null)

    // === SCORES (PAR JEU) ===
    const morpionScores = ref<{ [friendCode: string]: number }>({})
    const puissance4Scores = ref<{ [friendCode: string]: number }>({})

    // === CHAT PAR ROOM ===
    // Messages de chat, classés par roomId (clé dynamique : { roomId: [...messages] })
    const chatMessagesByRoom = ref<Record<string, ChatMessage[]>>({})

    // === ÉTAT DU JEU MORPION ===
    // État réactif du plateau, joueur courant, gagnant etc.
    const morpionGameState = ref({
        board: Array(9).fill(null) as (string | null)[],
        currentPlayerIndex: 0,
        winnerIndex: null as number | null,
        draw: false,
        status: 'playing' as 'playing' | 'win' | 'draw'
    })

    // === ÉTAT DU JEU PUISSANCE 4 ===
    const puissance4GameState = ref({
        board: Array(6).fill(null).map(() => Array(7).fill(null)) as (string | null)[][],
        currentPlayerIndex: 0,
        winnerIndex: null as number | null,
        draw: false,
        status: 'playing' as 'playing' | 'win' | 'draw'
    })

    // Accès au store WebSocket
    const wsStore = useWebSocketStore()

    // === ACTIONS DU LOBBY (rooms) ===

    /**
     * Remplace la liste des rooms par une nouvelle (mise à jour du lobby)
     * Garde le count des spectateurs déjà connus pour éviter le reset de l'affichage.
     */
    const setRooms = (list: RoomCard[]) => {
        rooms.value = list.map(newRoom => {
            const existingRoom = rooms.value.find(r => r.id === newRoom.id)
            return {
                ...newRoom,
                viewersCount: existingRoom?.viewersCount ?? 0
            }
        })
    }

    /**
     * Ajoute une room au début de la liste (nouvelle partie créée)
     */
    const addRoom = (room: RoomCard) => {
        rooms.value = [{
            ...room,
            viewersCount: room.viewersCount ?? 0
        }, ...rooms.value]
    }

    /**
     * Demande au backend (WebSocket) la liste des rooms disponibles.
     */
    const fetchRooms = () => {
        isLoading.value = true
        wsStore.send({ type: 'game_room_list' })
    }

    /**
     * Crée une nouvelle room de jeu côté backend (WebSocket).
     */
    const createRoom = (payload: { name: string; game: string }) => {
        isLoading.value = true
        wsStore.send({ type: 'game_room_create', payload })
    }

    // === ROOM COURANTE ===
    const setCurrentRoomPlayers = (players: UserPublic[], roomId: string) => {
        currentRoomPlayers.value = players
        currentRoomId.value = roomId
    }

    /**
     * Ajoute un joueur dans la room courante s'il n'y est pas déjà.
     */
    const addPlayerToCurrentRoom = (player: UserPublic) => {
        if (!currentRoomPlayers.value.find(p => p.friendCode === player.friendCode)) {
            currentRoomPlayers.value.push(player)
        }
    }

    /**
     * Retire un joueur de la room courante (ex : il quitte ou est kick)
     * Reset aussi l’état du jeu Morpion si besoin.
     */
    const removePlayerFromCurrentRoom = (friendCode: string) => {
        currentRoomPlayers.value = currentRoomPlayers.value.filter(p => p.friendCode !== friendCode)
        resetMorpionGameState()
        resetPuissance4GameState()
    }

    /**
     * Réinitialise tout l’état lié à la room courante (quand on quitte la partie)
     */
    const resetCurrentRoom = () => {
        currentRoomId.value = null
        currentRoomPlayers.value = []
        resetMorpionGameState()
        resetPuissance4GameState()
        resetScores()
        resetPuissance4Scores()
    }

    // === MORPION ===
    const resetMorpionGameState = () => {
        morpionGameState.value = {
            board: Array(9).fill(null),
            currentPlayerIndex: 0,
            winnerIndex: null,
            draw: false,
            status: 'playing'
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

    /**
     * Met à jour l’état du morpion avec les infos reçues du backend.
     * Gère la détection du gagnant pour incrémenter le score.
     */
    const setMorpionGameState = (payload: any) => {
        morpionGameState.value.board = payload.board ?? Array(9).fill(null)
        morpionGameState.value.currentPlayerIndex = payload.currentPlayerIndex ?? 0
        morpionGameState.value.winnerIndex = payload.winnerIndex ?? null
        morpionGameState.value.draw = !!payload.draw
        morpionGameState.value.status = payload.status || (
            payload.winner !== null ? 'win' : (payload.draw ? 'draw' : 'playing')
        )

        // Si un joueur gagne et que ce n'est pas un match nul, on incrémente son score
        if (
            payload.winnerIndex !== undefined &&
            payload.winnerIndex !== null &&
            !payload.draw
        ) {
            const winner = currentRoomPlayers.value[payload.winnerIndex]
            if (winner) {
                morpionScores.value[winner.friendCode] = (morpionScores.value[winner.friendCode] || 0) + 1
            }
        }
    }
    const resetScores = () => {
        morpionScores.value = {}
        if (currentRoomId.value) {
            delete chatMessagesByRoom.value[currentRoomId.value]
        }
    }

    // === PUISSANCE 4 ===
    const resetPuissance4GameState = () => {
        puissance4GameState.value = {
            board: Array(6).fill(null).map(() => Array(7).fill(null)),
            currentPlayerIndex: 0,
            winnerIndex: null,
            draw: false,
            status: 'playing'
        }
    }
    const playPuissance4Move = (col: number) => {
        if (!currentRoomId.value) return
        wsStore.send({
            type: 'puissance4_play',
            roomId: currentRoomId.value,
            col
        })
    }
    const setPuissance4GameState = (payload: any) => {
        puissance4GameState.value.board = payload.board ?? Array(6).fill(null).map(() => Array(7).fill(null))
        puissance4GameState.value.currentPlayerIndex = payload.currentPlayerIndex ?? 0
        puissance4GameState.value.winnerIndex = payload.winnerIndex ?? null
        puissance4GameState.value.draw = !!payload.draw
        puissance4GameState.value.status = payload.status || (
            payload.winner !== null ? 'win' : (payload.draw ? 'draw' : 'playing')
        )

        if (
            payload.winnerIndex !== undefined &&
            payload.winnerIndex !== null &&
            !payload.draw
        ) {
            const winner = currentRoomPlayers.value[payload.winnerIndex]
            if (winner) {
                puissance4Scores.value[winner.friendCode] = (puissance4Scores.value[winner.friendCode] || 0) + 1
            }
        }
    }
    const resetPuissance4Scores = () => {
        puissance4Scores.value = {}
        if (currentRoomId.value) {
            delete chatMessagesByRoom.value[currentRoomId.value]
        }
    }

    // === SPECTATEURS ===
    const incrementViewerCount = (roomId: string) => {
        const idx = rooms.value.findIndex(r => r.id === roomId)
        if (idx !== -1) {
            rooms.value[idx].viewersCount = (rooms.value[idx].viewersCount || 0) + 1
        }
    }

    // === CHAT PAR ROOM ===

    /**
     * Ajoute un message de chat dans le tableau associé à la room (créé le tableau si besoin)
     */
    const addChatMessage = (message: ChatMessage) => {
        const roomId = message.roomId
        if (!chatMessagesByRoom.value[roomId]) {
            chatMessagesByRoom.value[roomId] = []
        }
        chatMessagesByRoom.value[roomId].push(message)
    }

    /**
     * Retire une room du lobby (ex : partie supprimée), nettoie aussi le chat local.
     */
    const removeRoom = (roomId: string) => {
        rooms.value = rooms.value.filter(r => String(r.id) !== String(roomId))
        delete chatMessagesByRoom.value[roomId]
    }

    // Ce qu'on expose à l'extérieur du store (accessible via useGameStore())
    return {
        // Lobby
        rooms, isLoading, setRooms, addRoom, fetchRooms, createRoom, removeRoom,
        // Room courante
        currentRoomPlayers, currentRoomId, setCurrentRoomPlayers, addPlayerToCurrentRoom, removePlayerFromCurrentRoom, resetCurrentRoom, incrementViewerCount,
        // Morpion
        morpionGameState, setMorpionGameState, playMorpionMove, morpionScores, resetScores, resetMorpionGameState,
        // Puissance 4
        puissance4GameState, setPuissance4GameState, playPuissance4Move, puissance4Scores, resetPuissance4Scores, resetPuissance4GameState,
        // Chat
        chatMessagesByRoom, addChatMessage
    }
})
