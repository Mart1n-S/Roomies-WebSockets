import axios from 'axios'
import type { AxiosRequestConfig, AxiosError } from 'axios'
import { router } from '@/router'
import { useAuthStore } from '@/stores/authStore'

const axiosInstance = axios.create({
    baseURL: import.meta.env.VITE_API_URL,
    withCredentials: true, // IMPORTANT pour envoyer les cookies JWT
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
})

/**
 * isRefreshing :
 * Drapeau pour éviter les boucles infinies de rafraîchissement de token.
 * Sert à s'assurer qu'une seule requête de refresh est en cours à la fois.
 * 
 * failedQueue :
 * File d'attente des callbacks à exécuter une fois le token rafraîchi.
 * Chaque callback correspond à une requête qui a échoué à cause d'un token expiré,
 * et sera rejouée après le rafraîchissement réussi.
 */
let isRefreshing = false
let failedQueue: (() => void)[] = []

/**
 * Vide la file d'attente des requêtes en échec et les rejoue.
 * Appelé après un refresh réussi.
 */
const processQueue = () => {
    failedQueue.forEach(cb => cb())
    failedQueue = []
}

/**
 * Intercepteur global des réponses Axios.
 * Intercepte toutes les erreurs 401 dues à un JWT expiré,
 * tente de rafraîchir le token via /token/refresh,
 * puis rejoue automatiquement la requête initiale si le refresh réussit.
 * Si le refresh échoue, l'utilisateur est déconnecté et redirigé vers la page de connexion.
 */
axiosInstance.interceptors.response.use(
    response => response, // si tout va bien
    async (error: AxiosError) => {
        const originalRequest = error.config as AxiosRequestConfig & { _retry?: boolean }

        const silentAuthMode = originalRequest.headers?.['X-Silent-Auth']


        // Requête silencieuse à ignorer totalement
        if (silentAuthMode === 'ignore') {
            return Promise.reject(error)
        }

        // Vérifie si c'est bien une erreur 401 ET si on n'a pas déjà tenté le refresh
        if (
            error.response?.status === 401 &&
            !originalRequest._retry &&
            !originalRequest.url?.includes('/token/refresh')
        ) {
            originalRequest._retry = true

            // Si un refresh est déjà en cours, on attend qu’il se termine
            return new Promise((resolve, reject) => {
                failedQueue.push(() => {
                    resolve(axiosInstance(originalRequest)) // on rejoue la requête
                })

                if (!isRefreshing) {
                    isRefreshing = true
                    axiosInstance
                        .post('/token/refresh')
                        .then(() => {
                            isRefreshing = false
                            processQueue()
                        })
                        .catch(() => {
                            isRefreshing = false
                            failedQueue = []

                            const auth = useAuthStore()
                            auth.resetLocalAuthState()

                            router.push('/login')
                            reject(error)
                        })
                }
            })
        }

        return Promise.reject(error)
    }
)
export default axiosInstance
