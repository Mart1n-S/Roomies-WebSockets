<template>
    <div class="flex flex-col h-full p-3 space-y-3 text-white">
        <p class="my-4 text-lg font-bold"><i class="pi pi-globe" /> Chat global</p>
        <BaseInput name="search-global" label="Rechercher un membre" v-model="search" autocomplete="off"
            placeholder="Tapez un pseudo..." type="text" />

        <div class="mb-1 text-sm text-gray-400">
            {{ filteredUsers.length }} personne{{ filteredUsers.length > 1 ? 's' : '' }} dans le chat
        </div>

        <ItemList :items="filteredUsers" :is-loading="isLoading" itemKey="friendCode"
            :empty-message="search.length ? 'Aucun résultat...' : 'Aucun utilisateur actif'">
            <template #item-content="{ item: user }">
                <div class="flex items-center px-2 py-1 space-x-2 transition rounded-lg cursor-pointer hover:bg-roomies-gray2/60"
                    @click="openProfile(user)">
                    <div class="relative w-8 h-8">
                        <img :src="user.avatar" class="w-8 h-8 rounded-full" :alt="user.pseudo" />
                        <span
                            class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 rounded-full border-roomies-gray1"
                            title="En ligne" />
                    </div>
                    <span>{{ user.pseudo }}</span>
                </div>
            </template>
        </ItemList>

        <!-- Profil Public Modal -->
        <ProfilPublicModalForm v-if="isProfileOpen" :user="selectedUser" @close="closeProfile"
            @add-friend="handleAddFriend" />
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useGlobalChatUsersStore } from '@/stores/globalChatUsersStore'
import BaseInput from '@/components/base/BaseInput.vue'
import ItemList from '@/components/UI/ItemList.vue'
import ProfilPublicModalForm from '@/components/form/ProfilPublicModalForm.vue'
import { useWebSocketStore } from '@/stores/wsStore'


const globalChatUsersStore = useGlobalChatUsersStore()
const wsStore = useWebSocketStore()
const search = ref('')
const selectedUser = ref(null as null | { pseudo: string, avatar: string, friendCode: string })

const isProfileOpen = computed(() => !!selectedUser.value)
const isLoading = computed(() => globalChatUsersStore.isLoading)
const filteredUsers = computed(() =>
    globalChatUsersStore.users.filter(u =>
        u.pseudo.toLowerCase().includes(search.value.toLowerCase())
    )
)

function openProfile(user: { pseudo: string, avatar: string, friendCode: string }) {
    selectedUser.value = user
}
function closeProfile() {
    selectedUser.value = null
}

function handleAddFriend(friendCode: string) {
    wsStore.send({
        type: 'friend_request',
        payload: {
            friendCode
        }
    })
}

</script>
