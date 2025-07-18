<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center px-3 bg-black bg-opacity-60" @click.self="close">
        <div class="w-full max-w-md p-6 shadow-lg bg-roomies-gray4 rounded-xl">
            <h2 class="mb-4 text-xl font-semibold text-white">Nouvelle conversation</h2>

            <!-- Champ de recherche -->
            <BaseInput name="search-private-chat" label="Rechercher un ami" v-model="search" autocomplete="off"
                placeholder="Tapez un psesudo..." type="text" />

            <div class="pr-1 mt-4 overflow-y-auto max-h-80 scrollbar">
                <!-- Liste des amis -->
                <ItemList :items="filteredFriends" :is-loading="friendshipStore.isLoading"
                    :empty-message="search.length ? 'Aucun résultat 🥲' : 'Aucun ami disponible'" itemKey="friendCode"
                    @item-click="(friend) => startConversation(friend.friendCode)">
                    <template #item-content="{ item: friend }">
                        <div class="flex items-center space-x-3">
                            <img :src="friend.avatar" alt="avatar" class="w-8 h-8 rounded-full" />
                            <span>{{ friend.pseudo }}</span>
                        </div>
                    </template>
                </ItemList>
            </div>

            <BaseButton class="w-full mt-6" variant="danger" @click="close">Fermer</BaseButton>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useFriendshipStore } from '@/stores/friendshipStore'
import { useRoomStore } from '@/stores/roomStore'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import ItemList from '@/components/UI/ItemList.vue'
import { useToast } from 'vue-toastification'

const emit = defineEmits(['close'])
const router = useRouter()
const friendshipStore = useFriendshipStore()
const roomStore = useRoomStore()
const authStore = useAuthStore()
const toast = useToast()

const search = ref('')

onMounted(() => {
    if (friendshipStore.friendUsers.length === 0) {
        friendshipStore.fetchFriendships()
    }
})

const filteredFriends = computed(() => {
    return friendshipStore.friendUsers.filter(friend =>
        friend.pseudo.toLowerCase().includes(search.value.toLowerCase())
    )
})

async function startConversation(friendCode: string) {
    const myFriendCode = authStore.user?.friendCode
    if (!myFriendCode) {
        toast.error('Utilisateur non connecté')
        return
    }

    const existingRoom = roomStore.privateRooms.find(room => {
        if (!room.isGroup && room.members.length === 2) {
            const friendMatch = room.members.find(m => m.member.friendCode === friendCode)
            const meMatch = room.members.find(m => m.member.friendCode === myFriendCode)
            return friendMatch && meMatch
        }
        return false
    })

    if (existingRoom) {
        const myMembership = existingRoom.members.find(m => m.member.friendCode === myFriendCode)

        //  Optimistic update : on change localement
        if (myMembership?.isVisible === false) {
            myMembership.isVisible = true // immédiat dans le store

            // Appel réseau en arrière-plan (sans await)
            roomStore.setPrivateRoomVisibility(existingRoom.id, true, myFriendCode)
                .catch(() => {
                    // rollback si nécessaire ou afficher un toast
                    myMembership.isVisible = false
                    toast.error("Erreur lors de la réactivation de la conversation.")
                })
        }

        // avigation immédiate
        router.push({ name: 'private.chat', params: { roomId: existingRoom.id } })
    }

    emit('close')
}


function close() {
    emit('close')
}
</script>
