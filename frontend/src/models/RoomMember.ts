import type { UserPublic } from './User'

export interface RoomMember {
    id: string
    role: 'owner' | 'admin' | 'user'
    member: UserPublic
}
