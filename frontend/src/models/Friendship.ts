export interface Friendship {
    id: string
    friend: {
        pseudo: string
        avatar: string
        friendCode: string
    }
    status: 'pending' | 'friend'
    createdAt: string
}