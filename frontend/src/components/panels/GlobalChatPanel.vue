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
                <div class="flex items-center space-x-2">
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
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useGlobalChatUsersStore } from '@/stores/globalChatUsersStore'

import BaseInput from '@/components/base/BaseInput.vue'
import ItemList from '@/components/UI/ItemList.vue'

const globalChatUsersStore = useGlobalChatUsersStore()
const search = ref('')

const isLoading = computed(() => globalChatUsersStore.isLoading)
const filteredUsers = computed(() =>
    globalChatUsersStore.users.filter(u =>
        u.pseudo.toLowerCase().includes(search.value.toLowerCase())
    )
)
</script>
