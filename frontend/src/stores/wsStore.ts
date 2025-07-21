import { defineStore } from 'pinia'
import {
    connectWebSocket,
    sendWebSocketMessage,
    addWebSocketListener,
    removeWebSocketListener
} from '@/services/websocket'
import { useRoomStore } from './roomStore'
import { useToast } from 'vue-toastification'
import { useUserStatusStore } from './userStatusStore'
import { useChatStore } from './chatStore'
import { useFriendshipStore } from './friendshipStore'
import { useAuthStore } from './authStore'
import { useGlobalChatStore } from './globalChatStore'
import { useGlobalChatUsersStore } from './globalChatUsersStore'
import { useGameStore } from './gameStore'
import { router } from '@/router'


export const useWebSocketStore = defineStore('ws', {
    state: () => ({
        isConnected: false,
        lastMessage: null as any,
        socketInstance: null as WebSocket | null,
        morpionWaitingRestart: false as boolean,
        puissance4WaitingRestart: false as boolean
    }),

    actions: {
        /**
         * Initialise la connexion WebSocket et s'abonne aux messages
         */
        async init() {
            const socket = await connectWebSocket()
            if (!socket) return

            this.socketInstance = socket
            this.isConnected = true
            addWebSocketListener(this.onMessage)
        },

        /**
         * Callback appelé à chaque message WebSocket
         */
        onMessage(data: any) {
            this.lastMessage = data

            /**
             * Traite les différents types de messages reçus
             * et met à jour les stores correspondants.
             */
            if (data.type === 'authenticated') {
                this.isConnected = true
            }

            /**
             * Initialisation des groupes
             */
            if (data.type === 'init_groups') {
                const roomStore = useRoomStore()
                roomStore.setRooms(data.data)
            }

            /**
             * Initialisation des rooms privées
             */
            if (data.type === 'init_private_rooms') {
                const roomStore = useRoomStore()
                const chatStore = useChatStore()

                roomStore.setPrivateRooms(data.data)
                chatStore.setUnreadCountsFromServer(data.data)
            }

            /**
             * Mise à jour du nombre de messages non lus dans une room
             */
            if (data.type === 'room_unread_updated') {
                const chatStore = useChatStore()
                chatStore.setUnreadCount(data.roomId, data.unreadCount)
            }

            /**
             * Notification de création de room
             */
            if (data.type === 'room_created') {
                const roomStore = useRoomStore()
                roomStore.addRoom(data.room)

                const toast = useToast()
                const roomName = data.room?.name ?? 'un groupe'
                toast.info(`Tu as été ajouté(e) au groupe "${roomName}"`)
            }

            /**
             * Mise à jour du statut en ligne d'un utilisateur
             */
            if (data.type === 'user-status') {
                const userStatusStore = useUserStatusStore()

                if (data.online) {
                    userStatusStore.setUserOnline(data.friendCode)
                } else {
                    userStatusStore.setUserOffline(data.friendCode)
                }
            }

            /**
             * Mise à jour du statut en ligne de plusieurs utilisateurs
             */
            if (data.type === 'bulk-status') {
                const userStatusStore = useUserStatusStore()
                data.onlineFriends.forEach((code: string) => userStatusStore.setUserOnline(code))
            }

            /**
             * Mise à jour des messages dans une room
             */
            if (data.type === 'message') {
                const chatStore = useChatStore()
                chatStore.appendMessages(data.roomId, [data.message])
            }

            /**
             * Ajout d'un nouveau message dans le chat
             */
            if (data.type === 'new_message') {
                const chatStore = useChatStore()
                chatStore.appendMessages(data.message.roomId, [data.message])

                const activeRoomId = window?.location?.pathname?.split('/').pop()
                if (activeRoomId !== data.message.roomId) {
                    chatStore.incrementUnreadCount(data.message.roomId)
                }
            }

            /**
             * Mise à jour des amitiés
             */
            if (data.type === 'friendship_updated') {
                const roomStore = useRoomStore()
                const friendshipStore = useFriendshipStore()
                const userStatusStore = useUserStatusStore()
                const wsStore = useWebSocketStore()
                const authStore = useAuthStore()

                const currentUserCode = authStore.user?.friendCode
                const rawFriendship = data.friendship

                // Corrige le champ .friend si c’est le user courant
                if (rawFriendship.friend.friendCode === currentUserCode) {
                    const otherMember = data.room.members.find(
                        (m: { member: { friendCode: string } }) => m.member.friendCode !== currentUserCode
                    )
                    if (otherMember) {
                        rawFriendship.friend = otherMember.member
                    }
                }

                // 1) Ajoute la relation corrigée
                friendshipStore.friendships.push(rawFriendship)

                // 2) Supprime la demande envoyée si présente
                friendshipStore.sentRequests = friendshipStore.sentRequests.filter(
                    f => f.id !== rawFriendship.id
                )

                // 3) Ajoute la room privée
                roomStore.privateRooms.push(data.room)

                // 4) Rafraîchit le statut en ligne de l’ami
                const friendCode = rawFriendship.friend.friendCode
                if (!userStatusStore.isOnline(friendCode)) {
                    wsStore.send({
                        type: 'request_status',
                        payload: {
                            friendCodes: [friendCode]
                        }
                    })
                }
            }

            /**
             * Suppression d'une amitié ou d'une demande
             */
            if (data.type === 'friendship_deleted') {
                const friendshipStore = useFriendshipStore()

                // Retirer la demande envoyée si elle existe
                friendshipStore.sentRequests = friendshipStore.sentRequests.filter(
                    f => f.id !== data.friendshipId
                )

                // Facultatif : sécurité pour retirer aussi côté reçu si jamais
                friendshipStore.receivedRequests = friendshipStore.receivedRequests.filter(
                    f => f.id !== data.friendshipId
                )
            }

            /**
             * Notification de succès d'envoi de demande d'ami
             */
            if (data.type === 'friend_request_success') {
                const friendshipStore = useFriendshipStore()
                friendshipStore.sentRequests.push(data.data)

                const toast = useToast()
                toast.success('Demande d’ami envoyée avec succès !')
            }

            /**
             * Notification de réception d'une demande d'ami
             */
            if (data.type === 'friend_request_received') {
                const friendshipStore = useFriendshipStore()
                friendshipStore.receivedRequests.push(data.data)

                const toast = useToast()
                toast.info(`${data.data.friend.pseudo} vous a envoyé une demande d’ami`)
            }

            /**
             * Réception de la liste complète et à jour des utilisateurs actuellement connectés
             * au chat global (envoyé lors de la connexion initiale ou d'un refresh massif).
             */
            if (data.type === 'global_active_users') {
                // data.users = liste complète à jour !
                const globalChatUsersStore = useGlobalChatUsersStore()
                globalChatUsersStore.setUsers(data.users)
            }


            /**
             * Un nouvel utilisateur vient de rejoindre le chat global (événement temps réel).
             */
            if (data.type === 'global_user_joined') {
                // data.user = nouvel utilisateur connecté au chat global
                const globalChatUsersStore = useGlobalChatUsersStore()
                globalChatUsersStore.addUser(data.user)
            }

            /**
             * Mise à jour des utilisateurs actifs dans le chat global
             */
            if (data.type === 'global_user_left') {
                // data.friendCode = utilisateur qui vient de partir
                const globalChatUsersStore = useGlobalChatUsersStore()
                globalChatUsersStore.removeUser(data.friendCode)
            }

            /**
             * Ajout d'un message dans le chat global
             */
            if (data.type === 'global_message') {
                const globalChatStore = useGlobalChatStore()
                globalChatStore.addMessage(data.message)
            }

            /**
             * Réception de la liste des rooms de jeu
             */
            if (data.type === 'game_room_list') {
                const gameStore = useGameStore()
                gameStore.setRooms(data.rooms)
                gameStore.isLoading = false
            }

            /**
             * Notification de création d'une nouvelle room de jeu
             */
            if (data.type === 'game_room_created') {
                const gameStore = useGameStore()
                gameStore.addRoom(data.room)
                gameStore.isLoading = false
            }

            /**
             * Notification d'erreur lors de la création d'une room de jeu
             */
            if (data.type === 'game_room_error') {
                const toast = useToast()
                toast.error(data.message)
                const gameStore = useGameStore()
                gameStore.isLoading = false
            }

            if (data.type === 'game_room_joined') {
                // data.players = array of UserReadDTO, data.roomId
                const gameStore = useGameStore()
                gameStore.setCurrentRoomPlayers(data.players, String(data.roomId))
                // Navigation (dans le store ou bien dans la vue)
                router.push(`/room/${data.roomId}`)
            }

            if (data.type === 'game_room_player_joined') {
                const gameStore = useGameStore()

                gameStore.addPlayerToCurrentRoom(data.player)

                if (data.wasViewer) {
                    gameStore.incrementViewerCount(String(data.roomId))
                }
            }


            if (data.type === 'game_room_viewing') {
                const gameStore = useGameStore()

                // On initialise les joueurs visibles dans la room même si on est viewer
                gameStore.setCurrentRoomPlayers(data.players, String(data.roomId))

                // On incrémente le nombre de spectateurs (facultatif si déjà fait dans le join)
                gameStore.incrementViewerCount(String(data.roomId))

                // Redirige vers la vue de la room
                router.push(`/room/${data.roomId}`)

                gameStore.setMorpionGameState(data.morpion)
            }


            // Update du nombre de joueurs sur la room card
            if (data.type === 'game_room_players_update') {
                const gameStore = useGameStore()
                const idx = gameStore.rooms.findIndex(r => r.id == String(data.roomId))
                if (idx !== -1) {
                    gameStore.rooms[idx].playersCount = data.playersCount
                    gameStore.rooms[idx].viewersCount = data.viewerCount
                }
            }

            if (data.type === 'morpion_update') {
                // Dès qu’une nouvelle partie démarre (status: 'playing'), on reset l’attente
                if (data.status === 'playing') {
                    this.morpionWaitingRestart = false
                }
                // On maj la state du jeu (board etc)
                const gameStore = useGameStore()
                gameStore.setMorpionGameState(data)
            }
            if (data.type === 'morpion_restart_wait') {
                // L’autre joueur n’a pas encore accepté de rejouer
                this.morpionWaitingRestart = true
            }
            if (data.type === 'morpion_play_error') {
                const toast = useToast()
                toast.error(data.message || 'Coup non valide.')
            }

            /**
            * Un joueur a quitté la room de jeu (broadcasté à tous les joueurs dans la room)
            */
            if (data.type === 'game_room_player_left') {
                const gameStore = useGameStore()
                const authStore = useAuthStore()

                // Ne rien faire de spécial si c’est un viewer
                if (data.wasViewer) {
                    return
                }

                if (data.friendCode === authStore.user?.friendCode) {
                    gameStore.resetCurrentRoom()
                    router.push('/games')
                    gameStore.resetScores()
                } else {
                    gameStore.removePlayerFromCurrentRoom(data.friendCode)
                    gameStore.resetScores()
                }
            }


            /**
            * Mise à jour du nombre de joueurs et état de la room après un join/quit (affiché sur les cards)
            */
            if (data.type === 'game_room_players_update') {
                const gameStore = useGameStore();
                const idx = gameStore.rooms.findIndex(r => r.id == String(data.roomId));
                if (idx !== -1) {
                    gameStore.rooms[idx].playersCount = data.playersCount;
                    // Met aussi à jour la room (optionnel, si tu affiches plus d'infos sur la card)
                    gameStore.rooms[idx] = {
                        ...gameStore.rooms[idx],
                        ...data.room
                    };
                }
            }

            /**
             * Nouveau message de chat reçu dans une room de jeu
             */
            if (data.type === 'game_chat_message') {
                const gameStore = useGameStore()
                gameStore.addChatMessage(data.message)
            }

            /**
             * Suppression d'une room de jeu
             */
            if (data.type === 'game_room_deleted') {
                const gameStore = useGameStore()
                const toast = useToast()

                // Solution 1: Utiliser la valeur directement depuis le store
                const currentRoomId = gameStore.currentRoomId

                gameStore.removeRoom(data.roomId)

                // Comparaison des IDs en tant que strings
                if (String(currentRoomId) === String(data.roomId)) {
                    gameStore.resetCurrentRoom()
                    router.push('/games')
                    toast.info('La partie a été supprimée.')
                }
            }

            if (data.type === 'group_updated') {
                const roomStore = useRoomStore()
                const updatedRoom = data.room
                const index = roomStore.rooms.findIndex(r => r.id === updatedRoom.id)

                if (index !== -1) {
                    roomStore.rooms[index] = updatedRoom
                } else {
                    roomStore.rooms.push(updatedRoom)
                }
            }

            // ... Dans le handler onmessage :
            if (data.type === 'puissance4_update') {
                if (data.status === 'playing') {
                    this.puissance4WaitingRestart = false
                }
                const gameStore = useGameStore()
                gameStore.setPuissance4GameState(data)
            }
            if (data.type === 'puissance4_restart_wait') {
                this.puissance4WaitingRestart = true
            }
            if (data.type === 'puissance4_play_error') {
                const toast = useToast()
                toast.error(data.message || 'Coup non valide.')
            }


            if (data.type === 'group_deleted') {
                const roomStore = useRoomStore()
                const chatStore = useChatStore()
                const toast = useToast()

                const roomId = data.roomId
                const currentRoomId = router.currentRoute.value.params.roomId

                roomStore.removeRoom(roomId)
                chatStore.clearRoomData(roomId)

                if (currentRoomId === roomId) {
                    router.push('/dashboard')
                    toast.info('Le groupe a été supprimé.')
                }
            }

            if (data.type === 'group_kicked') {
                const roomStore = useRoomStore()
                const chatStore = useChatStore()
                const toast = useToast()

                const roomId = data.roomId
                const currentRoomId = router.currentRoute.value.params.roomId

                // Retire la room de la sidebar
                roomStore.removeRoom(roomId)
                // Vide le chat de la room
                chatStore.clearRoomData(roomId)

                if (currentRoomId === roomId) {
                    router.push('/dashboard')
                    toast.info('Tu as été retiré du serveur.')
                } else {
                    toast.info('Tu as été retiré d\'un serveur.')
                }
            }


            /**
             * Gestion des erreurs WebSocket
             */
            if (data.type === 'error') {
                const toast = useToast()
                const roomStore = useRoomStore()

                console.warn('Erreur WebSocket reçue :', data.message)

                if (
                    typeof data.message === 'string' &&
                    data.message.includes('Tu ne fais pas partie de cette room')
                ) {
                    // Extraire l’ID de la room si c’est possible via l’URL
                    const currentRoomId = window?.location?.pathname?.split('/').pop()
                    if (currentRoomId) {
                        roomStore.removePrivateRoom(currentRoomId)
                        toast.info('Cette discussion n’est plus disponible.')
                        router.push('/dashboard')
                    }
                } else {
                    toast.error(data.message || 'Erreur inattendue.')
                }
            }
        },

        /**
         * Envoie un message WebSocket via le service
         */
        send(msg: any) {
            sendWebSocketMessage(msg)
        },

        /**
         * Ferme proprement la connexion WebSocket
         */
        disconnect() {
            if (this.socketInstance?.readyState === WebSocket.OPEN) {
                this.socketInstance.close()
            }

            removeWebSocketListener(this.onMessage)
            this.socketInstance = null
            this.isConnected = false
            this.lastMessage = null
        }
    }
})
