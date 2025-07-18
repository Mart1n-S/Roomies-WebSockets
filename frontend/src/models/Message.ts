import type { UserPublic } from './User'

export interface ChatMessage {
    id: string
    content: string | null
    createdAt: string
    sender: UserPublic
    roomId: string
    type: 'Text'
}
