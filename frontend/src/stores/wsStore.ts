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

export const useWebSocketStore = defineStore('ws', {
    state: () => ({
        isConnected: false,
        lastMessage: null as any,
        socketInstance: null as WebSocket | null
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

            if (data.type === 'authenticated') {
                this.isConnected = true
            }

            if (data.type === 'init_groups') {
                const roomStore = useRoomStore()
                roomStore.setRooms(data.data)
            }

            if (data.type === 'init_private_rooms') {
                const roomStore = useRoomStore()
                const chatStore = useChatStore()

                roomStore.setPrivateRooms(data.data)
                chatStore.setUnreadCountsFromServer(data.data)
            }

            if (data.type === 'room_unread_updated') {
                const chatStore = useChatStore()
                chatStore.setUnreadCount(data.roomId, data.unreadCount)
            }


            if (data.type === 'room_created') {
                const roomStore = useRoomStore()
                roomStore.addRoom(data.room)

                const toast = useToast()
                const roomName = data.room?.name ?? 'un groupe'
                toast.info(`Tu as été ajouté(e) au groupe "${roomName}"`)
            }

            if (data.type === 'user-status') {
                const userStatusStore = useUserStatusStore()

                if (data.online) {
                    userStatusStore.setUserOnline(data.friendCode)
                } else {
                    userStatusStore.setUserOffline(data.friendCode)
                }
            }

            if (data.type === 'bulk-status') {
                const userStatusStore = useUserStatusStore()
                data.onlineFriends.forEach((code: string) => userStatusStore.setUserOnline(code))
            }

            if (data.type === 'message') {
                const chatStore = useChatStore()
                chatStore.appendMessages(data.roomId, [data.message])
            }

            if (data.type === 'new_message') {
                const chatStore = useChatStore()
                chatStore.appendMessages(data.message.roomId, [data.message])

                const activeRoomId = window?.location?.pathname?.split('/').pop()
                if (activeRoomId !== data.message.roomId) {
                    chatStore.incrementUnreadCount(data.message.roomId)
                }
            }

            if (data.type === 'friendship_updated') {
                const roomStore = useRoomStore()
                const friendshipStore = useFriendshipStore()
                const userStatusStore = useUserStatusStore()
                const wsStore = useWebSocketStore()
                const authStore = useAuthStore()

                const currentUserCode = authStore.user?.friendCode
                const rawFriendship = data.friendship

                // 🛠 Corrige le champ .friend si c’est toi
                if (rawFriendship.friend.friendCode === currentUserCode) {
                    const otherMember = data.room.members.find(
                        m => m.member.friendCode !== currentUserCode
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

            if (data.type === 'friend_request_success') {
                const friendshipStore = useFriendshipStore()
                friendshipStore.sentRequests.push(data.data)

                const toast = useToast()
                toast.success('Demande d’ami envoyée avec succès !')
            }

            if (data.type === 'friend_request_received') {
                const friendshipStore = useFriendshipStore()
                friendshipStore.receivedRequests.push(data.data)

                const toast = useToast()
                toast.info(`${data.data.friend.pseudo} vous a envoyé une demande d’ami`)
            }

            if (data.type === 'error') {
                console.warn('Erreur WebSocket reçue :', data.message)
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
