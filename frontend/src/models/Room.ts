import type { RoomMember } from './RoomMember'

export interface Room {
    id: string
    name: string
    isGroup: boolean
    createdAt: string
    members: RoomMember[]
}
