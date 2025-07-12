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
