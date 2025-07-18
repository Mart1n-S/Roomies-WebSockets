// stores/chatStore.ts

import { defineStore } from 'pinia'
import type { ChatMessage } from '@/models/Message'
import { fetchMessages } from '@/services/chatService'
import type { Room } from '@/models/Room'

export const useChatStore = defineStore('chat', {
    state: () => ({
        /**
         * Messages par room ID (Map roomId -> messages[])
         */
        messagesByRoom: {} as Record<string, ChatMessage[]>,

        /**
         * État de chargement global (peut être étendu par room si besoin)
         */
        isLoading: false,
        /**
         * Pagination pour chaque room (Map roomId -> currentPage)
         * Peut être utilisé pour charger des messages plus anciens
         */
        currentPage: {} as Record<string, number>,
        hasMore: {} as Record<string, boolean>,

        /**
         * Compteur de messages non lus par room (Map roomId -> unreadCount)
         * Peut être utilisé pour afficher des notifications ou badges
         */
        unreadCounts: {} as Record<string, number>,

    }),

    getters: {
        /**
         * Récupère les messages d’une room spécifique
         */
        getMessagesForRoom: (state) => (roomId: string): ChatMessage[] => {
            return state.messagesByRoom[roomId] || []
        }
    },

    actions: {
        /**
         * Charge les messages initiaux pour une room (page 1)
         */
        async loadInitialMessages(roomId: string) {
            this.isLoading = true
            try {
                // Réinitialisation complète avant chargement
                delete this.messagesByRoom[roomId]
                delete this.currentPage[roomId]
                delete this.hasMore[roomId]

                const messages = await fetchMessages(roomId)
                this.setMessages(roomId, messages)

                this.currentPage[roomId] = 1
                this.hasMore[roomId] = messages.length > 0
            } catch (error) {
                console.error(`Erreur lors du chargement des messages pour la room ${roomId} :`, error)
            } finally {
                this.isLoading = false
            }
        },

        /**
         * Charge plus de messages pour une room (page suivante)
         * Utilisé pour la pagination (ex: chargement des anciens messages)
         */
        async loadMoreMessages(roomId: string) {
            if (this.isLoading || !this.hasMore[roomId]) return

            this.isLoading = true
            try {
                const nextPage = (this.currentPage[roomId] || 1) + 1
                const messages = await fetchMessages(roomId, nextPage)

                this.prependMessages(roomId, messages.reverse())

                this.currentPage[roomId] = nextPage
                this.hasMore[roomId] = messages.length > 0
            } catch (error) {
                console.error(`Erreur lors du chargement des messages (page suivante)`, error)
            } finally {
                this.isLoading = false
            }
        },

        /**
         * Remplace tous les messages pour une room
         */
        setMessages(roomId: string, messages: ChatMessage[]) {
            const unique = new Map<string, ChatMessage>()
            messages.reverse().forEach((msg) => unique.set(msg.id, msg))
            this.messagesByRoom[roomId] = Array.from(unique.values())
        },

        /**
         * Ajoute un message à la fin (nouveau message reçu/envoyé)
         */
        appendMessages(roomId: string, newMessages: ChatMessage[]) {
            if (!this.messagesByRoom[roomId]) this.messagesByRoom[roomId] = []

            const existingIds = new Set(this.messagesByRoom[roomId].map(msg => msg.id))

            const filtered = newMessages.reverse().filter(msg => !existingIds.has(msg.id))
            this.messagesByRoom[roomId].push(...filtered)
        },

        /**
         * Ajoute des messages plus anciens au début (ex: chargement de messages plus anciens)
         */
        prependMessages(roomId: string, olderMessages: ChatMessage[]) {
            if (!this.messagesByRoom[roomId]) this.messagesByRoom[roomId] = []

            const existingIds = new Set(this.messagesByRoom[roomId].map(msg => msg.id))

            const filtered = olderMessages.filter(msg => !existingIds.has(msg.id))
            this.messagesByRoom[roomId] = [...filtered, ...this.messagesByRoom[roomId]]
        },

        /**
         * Met à jour les compteurs de messages non lus pour chaque room
         * @param rooms 
         * @param myFriendCode 
         */
        updateUnreadCounts(rooms: Room[], myFriendCode: string) {
            this.unreadCounts = {}

            rooms.forEach(room => {
                const roomId = room.id
                const myRoomUser = room.members.find(m => m.member.friendCode === myFriendCode)
                const lastSeen = myRoomUser?.lastSeenAt

                if (!lastSeen) {
                    // Tout est non lu
                    this.unreadCounts[roomId] = this.messagesByRoom[roomId]?.length || 0
                    return
                }

                const unread = (this.messagesByRoom[roomId] || []).filter(m =>
                    new Date(m.createdAt) > new Date(lastSeen)
                ).length

                this.unreadCounts[roomId] = unread
            })
        },

        /**
         * Incrémente le compteur de messages non lus pour une room
         * Utilisé lors de la réception de nouveaux messages
         */
        incrementUnreadCount(roomId: string) {
            this.unreadCounts[roomId] = (this.unreadCounts[roomId] || 0) + 1
        },

        /**
         * Met à jour les compteurs de messages non lus depuis le serveur
         * @param rooms 
         */
        setUnreadCountsFromServer(rooms: { id: string; unreadCount: number }[]) {
            this.unreadCounts = {}

            for (const room of rooms) {
                this.unreadCounts[room.id] = room.unreadCount
            }
        },

        setUnreadCount(roomId: string, count: number) {
            this.unreadCounts[roomId] = count
        },

        /**
         * Supprime les messages d’une room (ex : déconnexion)
         */
        clearMessages(roomId: string) {
            delete this.messagesByRoom[roomId]
        },

        clearRoomData(roomId: string) {
            delete this.messagesByRoom[roomId]
            delete this.currentPage[roomId]
            delete this.hasMore[roomId]
            delete this.unreadCounts[roomId]
        }

    }
})
