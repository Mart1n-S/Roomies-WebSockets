import { defineStore } from 'pinia'
import { createGroup, fetchPrivateRooms, patchPrivateRoomVisibility } from '@/services/roomService'
import type { Room } from '@/models/Room'
import { sendWebSocketMessage } from '@/services/websocket'
import { useToast } from 'vue-toastification'
import { useAuthStore } from './authStore'
import { useWebSocketStore } from './wsStore'
import { router } from '@/router'

export const useRoomStore = defineStore('room', {
    state: () => ({
        rooms: [] as Room[],
        privateRooms: [] as Room[],
        loading: false,
        error: ''
    }),

    actions: {
        /**
         * Remplace la liste des salons de groupe.
         * @param newRooms Nouvelle liste de salons
         */
        setRooms(newRooms: Room[]) {
            this.rooms = newRooms
        },

        /**
         * Remplace la liste des salons privés.
         * @param rooms Nouvelle liste de salons privés
         */
        setPrivateRooms(rooms: Room[]) {
            this.privateRooms = rooms
        },

        /**
         * Ajoute un salon à la liste des salons de groupe.
         * @param room Salon à ajouter
         */
        addRoom(room: Room) {
            this.rooms.push(room)
        },

        /**
         * Réinitialise le message d'erreur.
         * Utile avant une nouvelle action réseau.
         */
        resetError() {
            this.error = ''
        },

        /**
         * Supprime un salon de groupe de la liste.
         */
        removeRoom(roomId: string) {
            this.rooms = this.rooms.filter(room => room.id !== roomId)
        },


        /**
         * Crée un nouveau groupe avec un nom et une liste de membres.
         * Émet également un message WebSocket pour notifier les membres.
         * @param payload Objet contenant le nom du groupe et les membres
         * @returns Le groupe nouvellement créé
         */
        async createGroup(payload: { name: string; members: string[] }): Promise<Room> {
            this.resetError()
            this.loading = true
            try {
                const newGroup = await createGroup(payload)
                this.addRoom(newGroup)

                sendWebSocketMessage({
                    type: 'notify_room_created',
                    payload: {
                        room: newGroup,
                        memberFriendCodes: payload.members
                    }
                })

                return newGroup
            } catch (err: any) {
                this.error = err.response?.data?.message || 'Erreur lors de la création du groupe'
                throw err
            } finally {
                this.loading = false
            }
        },

        /**
         * Récupère les salons privés associés à l'utilisateur.
         */
        async fetchPrivateRooms(): Promise<void> {
            this.resetError()
            this.loading = true
            try {
                const response = await fetchPrivateRooms()
                this.setPrivateRooms(response)
            } catch (err: any) {
                this.error = 'Impossible de charger les discussions privées'
                throw err
            } finally {
                this.loading = false
            }
        },

        /**
         * Ajoute des membres à un groupe via WebSocket.
         * Envoie un message pour chaque membre à ajouter.
         * @param roomId ID du salon de groupe
         * @param friendCodes Liste des codes amis des membres à ajouter
         */
        async addMembersToRoom(roomId: string, friendCodes: string[]): Promise<void> {
            this.resetError()
            this.loading = true

            try {
                for (const friendCode of friendCodes) {
                    sendWebSocketMessage({
                        type: 'group_add_member',
                        roomId,
                        friendCode
                    })
                }
            } catch (err: any) {
                this.error = err.response?.data?.message || 'Erreur lors de l’ajout de membres.'
                throw err
            } finally {
                this.loading = false
            }
        },

        /**
         * Envoie un message WebSocket pour quitter un groupe
         */
        async leaveGroup(roomId: string): Promise<void> {
            const wsStore = useWebSocketStore()

            wsStore.send({
                type: 'group_leave',
                roomId
            })

            router.push('/dashboard')
        },

        /**
         * Envoie un message WebSocket pour supprimer un groupe
         */
        async deleteGroup(roomId: string): Promise<void> {
            const wsStore = useWebSocketStore()

            wsStore.send({
                type: 'group_delete',
                roomId
            })

            router.push('/dashboard')
        },

        /**
         * Met à jour les paramètres d'un groupe (nom, rôles).
         * Envoie un message WebSocket pour notifier les membres.
         * @param roomId ID du salon à mettre à jour
         * @param payload Contient le nouveau nom et les rôles
         */
        async updateGroupSettings(roomId: string, payload: { name: string; roles: { friendCode: string; role: string }[] }): Promise<void> {
            const wsStore = useWebSocketStore()

            wsStore.send({
                type: 'group_update_settings',
                roomId,
                name: payload.name,
                roles: payload.roles
            })
        },

        /**
         * Exclut un membre du groupe (WebSocket)
         * @param roomId ID du salon
         * @param friendCode Code ami du membre à exclure
         */
        async kickMember(roomId: string, friendCode: string): Promise<void> {
            this.resetError?.()
            this.loading = true

            try {
                const wsStore = useWebSocketStore()
                wsStore.send({
                    type: 'group_kick_member',
                    roomId,
                    friendCode
                })
            } catch (err: any) {
                this.error = err?.response?.data?.message || 'Erreur lors de l’exclusion du membre.'
                throw err
            } finally {
                this.loading = false
            }
        },


        /**
         * Modifie la visibilité d’un salon privé pour l’utilisateur courant.
         * La mise à jour est immédiate côté front (optimiste), 
         * puis confirmée par un appel réseau en arrière-plan.
         * Si l’appel échoue, la valeur est restaurée.
         *
         * @param roomId ID du salon à modifier
         * @param isVisible Nouvelle visibilité du salon
         * @param myFriendCode Code ami de l'utilisateur actuel
         */
        async setPrivateRoomVisibility(roomId: string, isVisible: boolean, myFriendCode: string): Promise<void> {
            const toast = useToast()
            const room = this.privateRooms.find(r => r.id === roomId)
            const myMembership = room?.members.find(m => m.member.friendCode === myFriendCode)

            if (!room || !myMembership) {
                toast.error('Salon ou membre introuvable.')
                return
            }

            // Sauvegarde de la valeur actuelle pour rollback si erreur
            const previousVisibility = myMembership.isVisible

            // MAJ immédiate dans le state pour effet visuel instantané
            myMembership.isVisible = isVisible

            try {
                // Appel en arrière-plan avec Content-Type correct
                await patchPrivateRoomVisibility(roomId, isVisible)
            } catch (err: any) {
                // Rollback de la visibilité en cas d’échec
                myMembership.isVisible = previousVisibility
                this.error = err.response?.data?.message || 'Erreur lors de la modification de la visibilité'
                toast.error(this.error)
            }
        },
        /**
         * Met à jour le last seen d'un salon via WebSocket.
         * @param roomId L'identifiant du salon à mettre à jour.
         */
        async updateLastSeen(roomId: string): Promise<void> {
            const authStore = useAuthStore()
            const wsStore = useWebSocketStore()

            try {
                // 1. Envoie une requête WebSocket au backend pour mettre à jour lastSeen
                wsStore.send({
                    type: 'update_last_seen',
                    roomId: roomId
                })

                // 2. Met à jour immédiatement la valeur locale du lastSeenAt (optimiste)
                const room = this.privateRooms.find(r => r.id === roomId)
                const myFriendCode = authStore.user?.friendCode
                const myMembership = room?.members.find(m => m.member.friendCode === myFriendCode)

                if (myMembership) {
                    myMembership.lastSeenAt = new Date().toISOString()
                }

            } catch (error) {
                console.error('Erreur lors de la mise à jour de lastSeen (WebSocket) :', error)
            }
        },
        /**
         * Supprime un salon privé en fonction de son ID.
         * @param roomId L'identifiant du salon à supprimer.
         */
        removePrivateRoomByFriendCode(friendCode: string) {
            this.privateRooms = this.privateRooms.filter(
                room =>
                    room.isGroup || // garde les groupes
                    !room.members.some(member => member.member.friendCode === friendCode)
            )
        },

        /**
         * Méthode pour supprimer un salon privé par son ID.
         * Utile pour les salons privés où l'utilisateur n'est plus membre.
         * @param roomId 
         */
        removePrivateRoom(roomId: string) {
            this.privateRooms = this.privateRooms.filter(r => r.id !== roomId)
        }

    },

    getters: {
        /**
        * Retourne la liste des salons de groupe.
        */
        allRooms: (state) => state.rooms,

        /**
         * Retourne la liste des salons privés.
         */
        privateRoomsList: (state) => state.privateRooms,

        /**
         * Indique si une opération est en cours.
         */
        isLoading: (state) => state.loading
    }
})
