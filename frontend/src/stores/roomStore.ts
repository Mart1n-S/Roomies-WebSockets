import { defineStore } from 'pinia'
import { createGroup } from '@/services/roomService'
import type { Room } from '@/models/Room'
import { sendWebSocketMessage } from '@/services/websocket'

export const useRoomStore = defineStore('room', {
    state: () => ({
        rooms: [] as Room[],
        loading: false,
        error: ''
    }),

    actions: {
        setRooms(newRooms: Room[]) {
            this.rooms = newRooms
        },

        addRoom(room: Room) {
            this.rooms.push(room)
        },

        resetError() {
            this.error = ''
        },

        async createGroup(payload: { name: string; members: string[] }): Promise<Room> {
            this.resetError()
            this.loading = true
            try {
                const newGroup = await createGroup(payload)
                this.addRoom(newGroup)
                return newGroup
            } catch (err: any) {
                this.error = err.response?.data?.message || 'Erreur lors de la création du groupe'
                throw err
            } finally {
                this.loading = false
            }
        },

        /**
         * Crée un groupe et notifie les membres connectés via WebSocket
         */
        async createGroupAndNotify(payload: { name: string; members: string[] }): Promise<Room> {
            const createdRoom = await this.createGroup(payload)

            sendWebSocketMessage({
                type: 'notify_room_created',
                payload: {
                    room: createdRoom,
                    memberFriendCodes: payload.members
                }
            })

            return createdRoom
        }
    },

    getters: {
        allRooms: (state) => state.rooms
    }
})
