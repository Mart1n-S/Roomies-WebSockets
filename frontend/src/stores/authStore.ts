// src/stores/authStore.ts
import { defineStore } from 'pinia'
import { login, register, getCurrentUser, requestPasswordReset, resetPassword, requestNewConfirmationEmail, updateProfile, logout } from '@/services/authService'
import type { User } from '@/models/User'
import { router } from '@/router'
import { useWebSocketStore } from '@/stores/wsStore'
import { disconnectWebSocket } from '@/services/websocket'


export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null as User | null,
        loading: false,
        userFetched: false,
        error: '',
        appReady: false,
    }),

    actions: {
        /**
         * Connecte l'utilisateur avec email et mot de passe.
         * @param email Email de l'utilisateur
         * @param password Mot de passe de l'utilisateur
         */
        async login(email: string, password: string) {
            this.resetError()
            try {
                this.loading = true
                await login(email, password)
                this.user = await getCurrentUser()
                this.userFetched = true

                const wsStore = useWebSocketStore()
                await wsStore.init()
            } catch (err: any) {
                this.error = err.response?.data?.message || 'Erreur lors de la connexion.'
            } finally {
                this.loading = false
                this.appReady = true
            }
        },

        /**
         * Enregistre un nouvel utilisateur.
         */
        async registerUser(formData: FormData) {
            this.resetError()
            try {
                this.loading = true
                await register(formData)
            } catch (err: any) {
                this.error = err.response?.data?.message || 'Erreur lors de l\'inscription.'
                throw err
            } finally {
                this.loading = false
            }
        },

        /**
         * Récupère les infos de l'utilisateur connecté.
         */
        async fetchUser(refreshable = false) {
            try {
                this.loading = true
                const mode = refreshable ? 'refreshable' : 'ignore'
                this.user = await getCurrentUser(mode)
                const wsStore = useWebSocketStore()
                await wsStore.init()
            } catch {
                this.user = null
            } finally {
                this.loading = false
                this.userFetched = true
                this.appReady = true
            }
        },

        /**
         * Demande un lien de réinitialisation de mot de passe.
         * @param email L’adresse email saisie
         * @returns Promise<any>
         */
        async requestPasswordReset(email: string) {
            this.resetError()
            try {
                this.loading = true
                return await requestPasswordReset(email)
            } catch (err: any) {
                this.error = err.response?.data?.error || 'Erreur lors de la demande de réinitialisation.'
                throw err
            } finally {
                this.loading = false
            }
        },

        /**
         * Réinitialise le mot de passe avec un token de réinitialisation.
         * @param email Email de l'utilisateur
         * @param token Token reçu par email
         * @param password Nouveau mot de passe
         * @param confirmPassword Confirmation
         * @returns Promise<any>
         */
        async resetPassword(email: string, token: string, password: string, confirmPassword: string) {
            this.resetError()
            try {
                this.loading = true
                return await resetPassword(email, token, password, confirmPassword)
            } catch (err: any) {
                this.error = err.response?.data?.error || 'Erreur lors de la réinitialisation du mot de passe.'
                throw err
            } finally {
                this.loading = false
            }
        },

        /**
         * Demande un nouvel email de confirmation.
         * @param email Email de l’utilisateur
         * @returns Promise<any>
         */
        async requestNewConfirmationEmail(email: string) {
            this.resetError()
            try {
                this.loading = true
                return await requestNewConfirmationEmail(email)
            } catch (err: any) {
                this.error = err.response?.data?.error || 'Erreur lors de l’envoi de l’email.'
                throw err
            } finally {
                this.loading = false
            }
        },

        /**
         * Met à jour le profil utilisateur (pseudo, avatar, mot de passe).
         * @param formData Données multipart (pseudo, avatar, currentPassword, newPassword…)
         * @returns Promise<any>
         */
        async updateProfile(formData: FormData) {
            this.resetError()
            try {
                this.loading = true
                // On récupère la réponse (user mis à jour) !
                const updatedUser = await updateProfile(formData)
                this.user = updatedUser
            } catch (err: any) {
                this.error = err.response?.data?.message || 'Erreur lors de la mise à jour du profil.'
                throw err
            } finally {
                this.loading = false
            }
        },

        /**
         * Déconnecte l'utilisateur.
         */
        async logout() {
            try {
                this.loading = true
                await logout()
            } catch (err) {
                console.warn('Erreur lors du logout', err)
            } finally {
                disconnectWebSocket()

                this.user = null
                this.userFetched = false
                this.appReady = false
                this.error = ''
                this.loading = false
                router.push('/')
            }
        },

        /**
         * Réinitialise l'erreur.
         * Utile pour nettoyer l'état avant de soumettre un formulaire.
         */
        resetError() {
            this.error = ''
        },

        /**
         * Réinitialise uniquement l'état local de l'authentification,
         * sans appeler l'API de déconnexion.
         * 
         * On ne met pas this.userFetched = false sinon cela va créer une boucle infinie
         */
        resetLocalAuthState() {
            this.user = null
            this.error = ''
            this.loading = false
        }
    }
})
