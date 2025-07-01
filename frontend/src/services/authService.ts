import axios from '@/modules/axios'

/**
 * Envoie une requête de connexion à l'API.
 * @param email Email de l'utilisateur
 * @param password Mot de passe
 * @returns Token JWT et infos utilisateur
 */
export async function login(email: string, password: string) {
    const response = await axios.post('/login', { email, password })
    return response.data
}

/**
 * Envoie une requête d'inscription à l'API.
 * @param formData Données multipart (avatar, email, password, pseudo…)
 * @returns Données utilisateur
 */
export async function register(formData: FormData) {
    const response = await axios.post('/user', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    })
    return response.data
}

/**
 * Récupère l'utilisateur connecté à partir du token.
 * @returns Données utilisateur
 */
export async function getCurrentUser() {
    const response = await axios.get('/user')
    return response.data
}
