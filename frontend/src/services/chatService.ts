import axios from '@/modules/axios'
import type { ChatMessage } from '@/models/Message'

export async function fetchMessages(roomId: string, page = 1, itemsPerPage = 40): Promise<ChatMessage[]> {
    const response = await axios.get('/messages', {
        params: {
            roomId,
            page,
            itemsPerPage
        },
        headers: {
            Accept: 'application/ld+json'
        }
    })

    return response.data['hydra:member'] as ChatMessage[]
}
