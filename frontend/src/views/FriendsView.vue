<template>
    <div class="flex flex-col h-full p-4 space-y-4 text-white">
        <!-- Top bar -->
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold"><i class="pi pi-users" /> Amis</h2>
            <BaseButton @click="showAddModal = true">Ajouter</BaseButton>
        </div>

        <!-- Switch entre les vues -->
        <div class="flex items-center gap-2">
            <BaseButton :variant="currentView === 'friends' ? 'primary' : 'secondary'" @click="currentView = 'friends'">
                Amis</BaseButton>
            <BaseButton :variant="currentView === 'received' ? 'primary' : 'secondary'"
                @click="currentView = 'received'" class="relative">
                Demandes reçues
                <span v-if="unreadReceivedCount > 0"
                    class="absolute top-0 right-0 flex items-center justify-center w-5 h-5 -mt-1 -mr-1 text-xs font-bold text-white bg-red-500 rounded-full">
                    {{ unreadReceivedCount }}
                </span>
            </BaseButton>

            <BaseButton :variant="currentView === 'sent' ? 'primary' : 'secondary'" @click="currentView = 'sent'">
                Demandes envoyées</BaseButton>
        </div>

        <!-- Search input -->
        <BaseInput name="search-friends" label="Rechercher" v-model="search" autocomplete="off" placeholder="Rechercher"
            type="text" />

        <!-- Nombre d'éléments -->
        <p class="text-sm text-gray-400">{{ displayedList.length }} {{ currentLabel }}</p>

        <!-- Liste -->
        <ItemList :items="displayedList" :is-loading="friendshipStore.isLoading" itemKey="id"
            :empty-message="search.length ? 'Aucun résultat…' : emptyLabel">
            <!-- Affichage commun à tous les modes -->
            <template #item-content="{ item }">
                <div class="flex items-center space-x-3">
                    <div class="relative w-8 h-8">
                        <img :src="item.friend.avatar" alt="avatar" class="w-8 h-8 rounded-full" />

                        <!-- Affiche la pastille uniquement en vue 'friends' -->
                        <span v-if="currentView === 'friends'"
                            class="absolute bottom-0 right-0 w-3 h-3 border-2 rounded-full border-roomies-gray4"
                            :class="userStatusStore.isOnline(item.friend.friendCode) ? 'bg-green-500' : 'bg-gray-500'" />
                    </div>
                    <div>
                        <p class="font-medium">{{ item.friend.pseudo }}</p>

                        <!-- Affiche le texte En ligne / Hors ligne uniquement en vue 'friends' -->
                        <p v-if="currentView === 'friends'" class="text-xs font-medium"
                            :class="userStatusStore.isOnline(item.friend.friendCode) ? 'text-green-400' : 'text-gray-400'">
                            {{ userStatusStore.isOnline(item.friend.friendCode) ? 'En ligne' : 'Hors ligne' }}
                        </p>
                    </div>
                </div>
            </template>

            <!-- Action en fonction du mode -->
            <template #item-action="{ item }">
                <template v-if="currentView === 'friends'">
                    <button class="text-red-500 hover:text-red-600" @click.stop="openConfirmModal(item)">
                        <i class="pi pi-trash" />
                    </button>
                </template>

                <template v-else-if="currentView === 'received'">
                    <div class="flex gap-2">
                        <BaseButton variant="primary" size="sm" :loading="acceptingId === item.id"
                            @click="acceptRequest(item.id)">
                            Accepter
                        </BaseButton>
                        <BaseButton variant="danger" size="sm" @click="openRejectModal(item)">Refuser</BaseButton>
                    </div>
                </template>


                <template v-else-if="currentView === 'sent'">
                    <span class="text-sm italic text-orange-300">En attente</span>
                </template>
            </template>
        </ItemList>

        <ConfirmDeleteModal v-if="showConfirmModal" :title="`Supprimer ${friendToDelete?.friend.pseudo} ?`"
            message="Êtes-vous sûr de vouloir supprimer cet ami ? Cette action est irréversible."
            :onConfirm="confirmDeleteFriend" @close="showConfirmModal = false" />

        <ConfirmDeleteModal v-if="showRejectModal" title="Refuser cette demande ?"
            message="Souhaites-tu vraiment refuser cette demande d’ami ?" :onConfirm="confirmRejectRequest"
            @close="showRejectModal = false" />

        <AddFriendModalForm v-if="showAddModal" @close="showAddModal = false" />

    </div>
</template>


<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import ItemList from '@/components/UI/ItemList.vue'
import ConfirmDeleteModal from '@/components/base/ConfirmDeleteModal.vue'
import AddFriendModalForm from '@/components/form/AddFriendModalForm.vue'
import { useFriendshipStore } from '@/stores/friendshipStore'
import { useUserStatusStore } from '@/stores/userStatusStore'
import { useWebSocketStore } from '@/stores/wsStore'
import { useRoomStore } from '@/stores/roomStore'
import type { Friendship } from '@/models/Friendship'
import { useToast } from 'vue-toastification'

const friendshipStore = useFriendshipStore()
const userStatusStore = useUserStatusStore()
const wsStore = useWebSocketStore()
const toast = useToast()

const search = ref('')
const showAddModal = ref(false)
const showConfirmModal = ref(false)
const friendToDelete = ref<Friendship | null>(null)
const showRejectModal = ref(false)
const requestToReject = ref<Friendship | null>(null)
const acceptingId = ref<string | null>(null)
const unreadReceivedCount = ref(0)


// Vue active : amis | reçues | envoyées
const currentView = ref<'friends' | 'received' | 'sent'>('friends')

onMounted(async () => {
    await friendshipStore.fetchFriendships()
    await friendshipStore.fetchReceivedRequests()
    await friendshipStore.fetchSentRequests()

    // Maintenant la donnée est bien à jour
    unreadReceivedCount.value = friendshipStore.pendingReceived.length
})

watch(currentView, (newView) => {
    if (newView === 'received') {
        unreadReceivedCount.value = 0
    }
})

const displayedList = computed(() => {
    const query = search.value.toLowerCase()
    switch (currentView.value) {
        case 'received':
            return friendshipStore.receivedRequests.filter(f =>
                f.friend.pseudo.toLowerCase().includes(query)
            )
        case 'sent':
            return friendshipStore.sentRequests.filter(f =>
                f.friend.pseudo.toLowerCase().includes(query)
            )
        default:
            return friendshipStore.friendships.filter(f =>
                f.friend.pseudo.toLowerCase().includes(query)
            )
    }
})

const currentLabel = computed(() => {
    switch (currentView.value) {
        case 'received': return 'demandes reçues'
        case 'sent': return 'demandes envoyées'
        default: return 'amis'
    }
})

const emptyLabel = computed(() => {
    switch (currentView.value) {
        case 'received': return 'Aucune demande reçue'
        case 'sent': return 'Aucune demande envoyée'
        default: return 'Aucun ami disponible'
    }
})

// Suppression d'ami
function openConfirmModal(friend: Friendship) {
    friendToDelete.value = friend
    showConfirmModal.value = true
}

async function confirmDeleteFriend() {
    if (!friendToDelete.value) return

    const id = friendToDelete.value.id
    const friendCode = friendToDelete.value.friend.friendCode

    // Fermeture de la modal immédiatement
    showConfirmModal.value = false

    // Suppression optimiste
    friendshipStore.friendships = friendshipStore.friendships.filter(f => f.id !== id)
    useRoomStore().removePrivateRoomByFriendCode(friendCode)

    try {
        await friendshipStore.deleteFriendship(id)
    } catch (err) {
        console.error('Erreur suppression :', err)
        toast.error('Impossible de supprimer cet ami. Réessaie plus tard.')

        // Rechargement complet si erreur pour rétablir un état cohérent
        await friendshipStore.fetchFriendships()
        await useRoomStore().fetchPrivateRooms()
    } finally {
        friendToDelete.value = null
    }
}

// Acceptation demande reçue
async function acceptRequest(id: string) {
    acceptingId.value = id

    // Optimistic UI : on masque le bouton / chargeur
    friendshipStore.receivedRequests = friendshipStore.receivedRequests.filter(f => f.id !== id)

    try {
        wsStore.send({
            type: 'patch_friendship',
            payload: {
                friendshipId: id,
                action: 'accepter'
            }
        })
    } catch (e) {
        toast.error('Erreur lors de l’acceptation.')
    } finally {
        acceptingId.value = null
    }
}


// Refus demande reçue
function openRejectModal(request: Friendship) {
    requestToReject.value = request
    showRejectModal.value = true
}

async function confirmRejectRequest() {
    if (!requestToReject.value) return

    // Optimistic update : on retire localement
    const id = requestToReject.value.id
    friendshipStore.receivedRequests = friendshipStore.receivedRequests.filter(r => r.id !== id)
    showRejectModal.value = false

    try {
        wsStore.send({
            type: 'patch_friendship',
            payload: {
                friendshipId: id,
                action: 'refuser'
            }
        })
    } catch (e) {
        toast.error("Erreur lors du refus de la demande.")
    } finally {
        requestToReject.value = null
    }
}

</script>