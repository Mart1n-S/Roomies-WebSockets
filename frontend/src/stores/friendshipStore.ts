import { defineStore } from 'pinia'
import {
    getFriendships,
    getReceivedFriendRequests,
    getSentFriendRequests,
    deleteFriendship,
    acceptFriendRequest,
    rejectFriendRequest
} from '@/services/friendshipService'
import type { Friendship } from '@/models/Friendship'
import { useRoomStore } from './roomStore'
import { useWebSocketStore } from './wsStore'

export const useFriendshipStore = defineStore('friendship', {
    state: () => ({
        friendships: [] as Friendship[],
        receivedRequests: [] as Friendship[],
        sentRequests: [] as Friendship[],
        isLoading: false,
        error: null as string | null,
        lastUpdated: null as number | null
    }),

    getters: {
        /**
         * Retourne les amitiés confirmées.
         */
        acceptedFriends: (state) =>
            state.friendships.filter(f => f.status === 'friend'),

        /**
         * Retourne les demandes d’amis reçues en attente.
         */
        pendingReceived: (state) =>
            state.receivedRequests.filter(f => f.status === 'pending'),

        /**
         * Retourne les demandes d’amis envoyées en attente.
         */
        pendingSent: (state) =>
            state.sentRequests.filter(f => f.status === 'pending'),

        /**
         * Retourne uniquement les utilisateurs amis.
         */
        friendUsers: (state) =>
            state.friendships
                .filter(f => f.status === 'friend')
                .map(f => f.friend)
    },

    actions: {
        /**
         * Récupère les amitiés confirmées (status = 'friend').
         * Met également à jour le timestamp de dernière récupération.
         */
        async fetchFriendships() {
            if (this.isLoading) return

            this.isLoading = true
            this.error = null
            try {
                const data = await getFriendships()
                this.friendships = data
                this.lastUpdated = Date.now() // Met à jour le timestamp
            } catch (e: any) {
                console.error('Erreur lors du chargement des amitiés :', e)
                this.error = e.response?.data?.message || e.message || 'Erreur inconnue'
                throw e // Propage l'erreur pour la gestion dans les composants
            } finally {
                this.isLoading = false
            }
        },

        /**
         * Charge les demandes d'amis reçues (où je suis recipient, status = pending)
         */
        async fetchReceivedRequests() {
            try {
                this.receivedRequests = await getReceivedFriendRequests()
            } catch (e: any) {
                console.error('Erreur lors du chargement des demandes reçues :', e)
                this.error = e.response?.data?.message || e.message || 'Erreur inconnue'
            }
        },

        /**
         * Charge les demandes d'amis envoyées (où je suis applicant, status = pending)
         */
        async fetchSentRequests() {
            try {
                this.sentRequests = await getSentFriendRequests()
            } catch (e: any) {
                console.error('Erreur lors du chargement des demandes envoyées :', e)
                this.error = e.response?.data?.message || e.message || 'Erreur inconnue'
            }
        },

        /**
         * Accepte une demande d'ami.
         */
        async acceptFriendRequest(id: string) {
            try {
                const response = await acceptFriendRequest(id) // contient friendship + room

                // 1. Met à jour les friendships
                this.friendships.push(response)

                // 2. Retire la requête de la liste des reçues
                this.receivedRequests = this.receivedRequests.filter(f => f.id !== id)

                // 3. Met à jour les rooms directement
                const roomStore = useRoomStore()
                roomStore.privateRooms.push(response.room)

                return response
            } catch (e: any) {
                console.error('Erreur lors de l’acceptation de la demande :', e)
                this.error = e.response?.data?.message || e.message || 'Erreur inconnue'
                throw e
            }
        },

        async acceptFriendRequestViaWs(id: string) {
            const wsStore = useWebSocketStore()

            wsStore.send({
                type: 'accept_friend_request',
                friendshipId: id
            })

            // Optimistic update : on met à jour localement
            this.receivedRequests = this.receivedRequests.filter(r => r.id !== id)
        },

        /**
         * Refuse une demande d'ami.
         */
        async rejectFriendRequest(id: string) {
            try {
                await rejectFriendRequest(id)
                await this.fetchReceivedRequests()
            } catch (e: any) {
                console.error('Erreur lors du refus de la demande :', e)
                this.error = e.response?.data?.message || e.message || 'Erreur inconnue'
                throw e
            }
        },

        /**
        * Supprime une amitié et met à jour la liste locale.
        */
        async deleteFriendship(friendshipId: string) {
            this.isLoading = true
            const roomStore = useRoomStore()

            try {
                // 1. Appel à l'API
                await deleteFriendship(friendshipId)

                // 2. Trouver la relation supprimée AVANT de la retirer
                const deletedFriendship = this.friendships.find(f => f.id === friendshipId)
                const friendCode = deletedFriendship?.friend.friendCode

                // 3. Supprimer l’amitié localement
                this.friendships = this.friendships.filter(f => f.id !== friendshipId)

                // 4. Supprimer aussi la room privée liée à ce friendCode
                if (friendCode) {
                    roomStore.privateRooms = roomStore.privateRooms.filter(room =>
                        !room.members.some(member => member.member.friendCode === friendCode)
                    )
                }

            } catch (e: any) {
                console.error('Erreur lors de la suppression de l’amitié :', e)
                this.error = e.response?.data?.message || e.message || 'Erreur inconnue'
                throw e
            } finally {
                this.isLoading = false
            }
        }

    }
})
