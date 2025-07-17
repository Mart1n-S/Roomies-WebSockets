export interface RoomCard {
    id: string;
    name: string;
    game: string;
    creator: {
        pseudo: string
        avatar: string
        friendCode: string
    };
    createdAt: string;
    playersCount: number;
    viewersCount: number;
}