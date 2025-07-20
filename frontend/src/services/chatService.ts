import axios from '@/modules/axios'
import type { ChatMessage } from '@/models/Message'

// Récupère la liste paginée des messages pour une room donnée.
// Retourne un tableau de ChatMessage.
export async function fetchMessages(roomId: string, page = 1, itemsPerPage = 40): Promise<ChatMessage[]> {
    const response = await axios.get('/messages', {
        params: {
            roomId,         // Identifiant de la room (obligatoire)
            page,           // Pagination : numéro de page (défaut 1)
            itemsPerPage    // Pagination : nombre d’éléments par page (défaut 40)
        },
        headers: {
            Accept: 'application/ld+json' // Format Hydra attendu par API Platform
        }
    })

    // Retourne le tableau des messages, formaté par API Platform sous 'hydra:member'
    return response.data['hydra:member'] as ChatMessage[]
}
