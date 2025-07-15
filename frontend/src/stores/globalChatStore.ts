import { defineStore } from 'pinia'
import type { ChatMessage } from '@/models/Message'

export const useGlobalChatStore = defineStore('globalChat', {
    state: () => ({
        messages: [] as ChatMessage[],
        isLoading: false
    }),
    actions: {
        /**
         * Quand l'utilisateur rejoint le chat global
         */
        join() {
            this.messages = []
            this.isLoading = false
            // Ici tu pourrais notifier le backend/ws que tu as rejoint le global chat
        },

        /**
         * Ajoute un message reçu en live
         */
        addMessage(message: ChatMessage) {
            this.messages.push(message)
        },

        /**
         * Quand il quitte le chat global
         */
        leave() {
            this.messages = []
        }
    }
})
