import type { Friendship } from './Friendship'
import type { Room } from './Room'

export interface FriendshipWithRoom extends Friendship {
    room: Room
}
