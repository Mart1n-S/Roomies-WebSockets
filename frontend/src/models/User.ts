export interface User {
    id: number
    email: string
    pseudo: string
    friendCode: string
    avatar: string
    pushNotificationsEnabled?: boolean
}

export interface UserPublic {
    pseudo: string
    avatar: string
    friendCode: string
    isOnline?: boolean
}