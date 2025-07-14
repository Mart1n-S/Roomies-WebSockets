<template>
    <div class="flex flex-col h-full p-4 space-y-4 text-white">
        <!-- Top bar -->
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Amis ·</h2>
            <BaseButton @click="showAddModal = true">Ajouter</BaseButton>
        </div>

        <!-- Search input -->
        <BaseInput name="search-friends" label="Rechercher" v-model="search" autocomplete="off" placeholder="Rechercher"
            type="text" />

        <!-- Nombre d'amis -->
        <p class="text-sm text-gray-400">{{ filteredFriends.length }} amis</p>

        <!-- Liste des amis (statique pour cette étape) -->
        <ItemList :items="filteredFriends" :is-loading="friendshipStore.isLoading" itemKey="id"
            :empty-message="search.length ? 'Aucun résultat…' : 'Aucun ami disponible'">

            <template #item-content="{ item: friend }">
                <div class="flex items-center space-x-3">
                    <!-- <img :src="friend.friend.avatar" alt="avatar" class="w-8 h-8 rounded-full" /> -->
                    <div class="relative w-8 h-8">
                        <img :src="friend.friend.avatar" alt="avatar" class="w-8 h-8 rounded-full" />
                        <span class="absolute bottom-0 right-0 w-3 h-3 border-2 rounded-full border-roomies-gray4"
                            :class="userStatusStore.isOnline(friend.friend.friendCode) ? 'bg-green-500' : 'bg-gray-500'" />
                    </div>
                    <div>
                        <p class="font-medium">{{ friend.friend.pseudo }}</p>
                        <p class="text-xs font-medium"
                            :class="userStatusStore.isOnline(friend.friend.friendCode) ? 'text-green-400' : 'text-gray-400'">
                            {{ userStatusStore.isOnline(friend.friend.friendCode) ? 'En ligne' : 'Hors ligne' }}
                        </p>

                    </div>
                </div>
            </template>

            <template #item-action="{ item: friend }">
                <button class="text-red-500 hover:text-red-600" @click.stop="openConfirmModal(friend)">
                    <i class="pi pi-trash" />
                </button>
            </template>
        </ItemList>

        <ConfirmDeleteModal v-if="showConfirmModal" :title="`Supprimer ${friendToDelete?.friend.pseudo} ?`"
            message="Êtes-vous sûr de vouloir supprimer cet ami ? Cette action est irréversible."
            :onConfirm="confirmDeleteFriend" @close="showConfirmModal = false" />


    </div>


</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import ItemList from '@/components/UI/ItemList.vue'
import ConfirmDeleteModal from '@/components/base/ConfirmDeleteModal.vue'
import { useFriendshipStore } from '@/stores/friendshipStore'
import { useUserStatusStore } from '@/stores/userStatusStore'
import type { Friendship } from '@/models/Friendship'
import { useToast } from 'vue-toastification'

const friendshipStore = useFriendshipStore()
const userStatusStore = useUserStatusStore()

const search = ref('')
const showAddModal = ref(false)
const toast = useToast()

const showConfirmModal = ref(false)
const friendToDelete = ref<Friendship | null>(null)

onMounted(() => {
    if (friendshipStore.friendUsers.length === 0) {
        friendshipStore.fetchFriendships()
    }
})

const filteredFriends = computed(() => {
    return friendshipStore.friendships.filter(f =>
        f.friend.pseudo.toLowerCase().includes(search.value.toLowerCase())
    )
})

function openConfirmModal(friend: Friendship) {
    console.log('Ouverture de la modale de confirmation pour', friend)
    friendToDelete.value = friend
    showConfirmModal.value = true
}

async function confirmDeleteFriend() {
    if (!friendToDelete.value) return

    const friendPseudo = friendToDelete.value.friend.pseudo

    await friendshipStore.deleteFriendship(friendToDelete.value.id)
        .then(() => {
            toast.success(`« ${friendPseudo} » a bien été supprimé de tes amis.`)
        })
        .catch((err) => {
            console.error('Erreur suppression :', err)
            toast.error('Impossible de supprimer cet ami. Réessaie plus tard.')
        })
        .finally(() => {
            friendToDelete.value = null
        })
}

</script>