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

                sendWebSocketMessage({
                    type: 'notify_room_created',
                    payload: {
                        room: newGroup,
                        memberFriendCodes: payload.members
                    }
                })

                return newGroup
            } catch (err: any) {
                this.error = err.response?.data?.message || 'Erreur lors de la création du groupe'
                throw err
            } finally {
                this.loading = false
            }
        }
    },

    getters: {
        allRooms: (state) => state.rooms
    }
})
