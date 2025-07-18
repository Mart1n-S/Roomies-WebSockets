import { defineStore } from 'pinia'

/**
 * Store global pour suivre le statut en ligne des utilisateurs
 */
export const useUserStatusStore = defineStore('userStatus', {
    state: () => ({
        /**
         * Ensemble des friendCodes des utilisateurs actuellement en ligne.
         * Utilisation d'un Set pour des recherches rapides.
         */
        onlineUsers: new Set<string>()
    }),

    actions: {
        /**
         * Ajoute un utilisateur à la liste des utilisateurs en ligne.
         * @param code FriendCode de l'utilisateur
         */
        setUserOnline(code: string) {
            this.onlineUsers.add(code)
        },

        /**
         * Retire un utilisateur de la liste des utilisateurs en ligne.
         * @param code FriendCode de l'utilisateur
         */
        setUserOffline(code: string) {
            this.onlineUsers.delete(code)
        },

        /**
         * Vérifie si un utilisateur est en ligne.
         * @param code FriendCode à tester
         * @returns true si en ligne, false sinon
         */
        isOnline(code: string) {
            return this.onlineUsers.has(code)
        }
    }
})
