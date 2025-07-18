import { defineStore } from 'pinia'
import type { UserPublic } from '@/models/User'

export const useGlobalChatUsersStore = defineStore('globalChatUsers', {
    state: () => ({
        users: [] as UserPublic[],
        isLoading: false
    }),
    actions: {
        setUsers(users: UserPublic[]) {
            this.users = users
        },
        addUser(user: UserPublic) {
            // Évite les doublons
            if (!this.users.some(u => u.friendCode === user.friendCode)) {
                this.users.push(user)
            }
        },
        removeUser(friendCode: string) {
            this.users = this.users.filter(u => u.friendCode !== friendCode)
        },
        clear() {
            this.users = []
        }
    }
})
