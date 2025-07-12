import { defineStore } from 'pinia'
import {
    connectWebSocket,
    sendWebSocketMessage,
    addWebSocketListener,
    removeWebSocketListener
} from '@/services/websocket'
import { useRoomStore } from './roomStore'


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
