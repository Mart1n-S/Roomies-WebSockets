import { defineStore } from 'pinia'
import type { UserPublic } from '@/models/User'

/**
 * Store Pinia pour gérer la liste des utilisateurs connectés au chat global.
 * Permet d’ajouter, supprimer, et remettre à zéro la liste des utilisateurs publics du global chat.
 */
export const useGlobalChatUsersStore = defineStore('globalChatUsers', {
    state: () => ({
        /** Liste des utilisateurs publics du chat global */
        users: [] as UserPublic[],
        /** Indicateur de chargement si besoin (utile pour loader côté UI) */
        isLoading: false
    }),
    actions: {
        /**
         * Remplace toute la liste d’utilisateurs par une nouvelle liste.
         * @param users Array<UserPublic>
         */
        setUsers(users: UserPublic[]) {
            this.users = users
        },

        /**
         * Ajoute un utilisateur si absent (évite les doublons par friendCode).
         * @param user UserPublic à ajouter
         */
        addUser(user: UserPublic) {
            if (!this.users.some(u => u.friendCode === user.friendCode)) {
                this.users.push(user)
            }
        },

        /**
         * Supprime un utilisateur de la liste par son friendCode.
         * @param friendCode string
         */
        removeUser(friendCode: string) {
            this.users = this.users.filter(u => u.friendCode !== friendCode)
        },

        /**
         * Réinitialise complètement la liste des utilisateurs.
         */
        clear() {
            this.users = []
        }
    }
})
