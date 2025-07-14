import axios from '@/modules/axios'

interface CreateGroupPayload {
    name: string
    members: string[] // friendCodes
}

/**
 * Crée un nouveau une room (groupe) de discussion privée avec des amis.
 */
export async function createGroup(payload: CreateGroupPayload) {
    const res = await axios.post('/groups', payload)
    return res.data
}

/**
 * Récupère les discussions privées de l'utilisateur connecté.
 */
export async function fetchPrivateRooms() {
    const res = await axios.get('/groups/private/chat')
    return res.data
}

/**
 * Met à jour la visibilité d'une room privée pour l'utilisateur.
 */
export async function patchPrivateRoomVisibility(roomUserId: string, isVisible: boolean) {
    await axios.patch(`/groups/private/chat/${roomUserId}/visibility`, { isVisible },
        {
            headers: {
                'Content-Type': 'application/merge-patch+json'
            }
        }
    )
}

/**
 * TODO: A supprimer
 * Met à jour le last seen d'un groupe.
 * @param groupId L'identifiant du groupe à mettre à jour.
 */
export function updateRoomLastSeen(groupId: string) {
    return axios.patch(`/groups/${groupId}/last-seen`, {
        lastSeenAt: new Date().toISOString()
    },
        {
            headers: {
                'Content-Type': 'application/merge-patch+json'
            }
        })
}