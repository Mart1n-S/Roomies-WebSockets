// src/stores/authStore.ts
import { defineStore } from 'pinia'
import { login, register, getCurrentUser, requestPasswordReset, resetPassword } from '@/services/authService'
import type { User } from '@/models/User'


export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null as User | null,
        loading: false,
        userFetched: false,
        error: ''
    }),

    actions: {
        async login(email: string, password: string) {
            try {
                this.loading = true
                await login(email, password)
                this.user = await getCurrentUser()
                this.userFetched = true
            } catch (err: any) {
                this.error = err.response?.data?.message || 'Erreur lors de la connexion.'
            } finally {
                this.loading = false
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
        async fetchUser() {
            try {
                this.loading = true
                this.user = await getCurrentUser()
            } catch {
                this.user = null
            } finally {
                this.loading = false
                this.userFetched = true
            }
        },

        /**
         * Demande un lien de réinitialisation de mot de passe.
         * @param email L’adresse email saisie
         */
        async requestPasswordReset(email: string) {
            this.resetError()
            try {
                this.loading = true
                await requestPasswordReset(email)
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
         */
        async resetPassword(email: string, token: string, password: string, confirmPassword: string) {
            this.resetError()
            try {
                this.loading = true
                await resetPassword(email, token, password, confirmPassword)
            } catch (err: any) {
                this.error = err.response?.data?.error || 'Erreur lors de la réinitialisation du mot de passe.'
                throw err
            } finally {
                this.loading = false
            }
        },


        /**
         * Déconnecte l'utilisateur.
         */
        logout() {
            this.user = null
            // TODO: A créer => endpoint /logout, et faire un appel ici
        },

        /**
         * Réinitialise l'erreur.
         * Utile pour nettoyer l'état avant de soumettre un formulaire.
         */
        resetError() {
            this.error = ''
        }
    }
})
