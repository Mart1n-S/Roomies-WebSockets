// src/stores/authStore.ts
import { defineStore } from 'pinia'
import { login, register, getCurrentUser } from '@/services/authService'
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
