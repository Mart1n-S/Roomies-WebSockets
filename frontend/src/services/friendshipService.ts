import axios from '@/modules/axios'
import type { Friendship } from '@/models/Friendship'
import type { FriendshipWithRoom } from '@/models/FriendshipWithRoom'

/**
 * Récupère toutes les amitiés de l'utilisateur (amis confirmés + demandes en attente).
 */
export async function getFriendships(): Promise<Friendship[]> {
    const res = await axios.get<Friendship[]>('/friendships')
    return res.data
}

/**
 * Récupère les demandes d'amis **reçues** (tu es le destinataire).
 */
export async function getReceivedFriendRequests(): Promise<Friendship[]> {
    const res = await axios.get<Friendship[]>('/friendships?filter=received')
    return res.data
}

/**
 * Récupère les demandes d'amis **envoyées** (tu es l’émetteur).
 */
export async function getSentFriendRequests(): Promise<Friendship[]> {
    const res = await axios.get<Friendship[]>('/friendships?filter=sent')
    return res.data
}

/**
 * Supprime une amitié existante.
 */
export async function deleteFriendship(friendshipId: string): Promise<void> {
    await axios.delete(`/friendships/${friendshipId}`)
}

/**
 * Accepte une demande d’ami.
 */
export async function acceptFriendRequest(id: string): Promise<FriendshipWithRoom> {
    const response = await axios.patch(`/friendships/${id}`, { action: 'accepter' },
        {
            headers: {
                'Content-Type': 'application/merge-patch+json'
            }
        })
    return response.data
}

/**
 * Refuse une demande d’ami.
 */
export async function rejectFriendRequest(id: string): Promise<void> {
    await axios.patch(`/friendships/${id}`, { action: 'refuser' },
        {
            headers: {
                'Content-Type': 'application/merge-patch+json'
            }
        }
    )
}

