export interface User {
    id: number
    email: string
    pseudo: string
    friendCode: string
    avatarUrl: string | null
}

export interface UserPublic {
    pseudo: string
    avatar: string
    friendCode: string
}