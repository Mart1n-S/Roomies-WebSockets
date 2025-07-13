import { defineStore } from 'pinia'
import {
    getFriendships,
    getReceivedFriendRequests,
    getSentFriendRequests
} from '@/services/friendshipService'

import type { Friendship } from '@/models/Friendship'

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
        }
    }
})
