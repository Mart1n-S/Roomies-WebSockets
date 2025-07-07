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

/**
 * Envoie une demande de réinitialisation de mot de passe.
 * @param email Adresse email de l'utilisateur
 */
export async function requestPasswordReset(email: string) {
    const response = await axios.post('/request-password-reset', { email })
    return response.data
}

/**
 * Réinitialise le mot de passe de l'utilisateur via email + token.
 * @param email Adresse email
 * @param token Token de réinitialisation
 * @param password Nouveau mot de passe
 * @param confirmPassword Confirmation du mot de passe
 */
export async function resetPassword(
    email: string,
    token: string,
    password: string,
    confirmPassword: string
) {
    const response = await axios.post('/reset-password', {
        email,
        token,
        password,
        confirmPassword
    })
    return response.data
}

/**
 * Demande un nouvel email de confirmation si l’utilisateur n’a pas encore validé son compte.
 * @param email Adresse email
 */
export async function requestNewConfirmationEmail(email: string) {
    const response = await axios.post('/request-new-confirmation-email', { email })
    return response.data
}


