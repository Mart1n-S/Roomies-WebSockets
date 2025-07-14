<template>
    <div class="flex flex-col h-full p-3 space-y-3 text-white">
        <BaseInput name="search" label="Rechercher un ami" v-model="search" autocomplete="off"
            placeholder="Tapez un pseudo..." type="text" />

        <BaseButton iconLeft class="justify-start" @click="router.push({ name: 'friends.list' })">
            <template #icon-left><i class="pi pi-users" /></template>
            Amis
        </BaseButton>

        <BaseButton iconLeft class="justify-start" @click="showModal = true">
            <template #icon-left><i class="pi pi-plus" /></template>
            Nouvelle conversation
        </BaseButton>

        <CreatePrivateChatModalForm v-if="showModal" @close="showModal = false" />

        <hr class="h-[1px] border-roomies-gray1 my-3" />
        <p class="mt-4 text-gray-300">Messages privés</p>

        <ItemList :items="filteredRooms" :is-loading="isLoading" :active-id="activeRoomId" :show-delete-button="true"
            :empty-message="search.length ? 'Aucun résultat, essayez avec un autre pseudo 🥸' : 'Aucune conversation'"
            @item-click="handleRoomClick" @item-action="hideRoom">
            <template #item-content="{ item: room }">
                <div class="flex items-center space-x-2">
                    <div class="relative w-8 h-8">
                        <img :src="getOtherMember(room).avatar" class="w-8 h-8 rounded-full"
                            :alt="getOtherMember(room).pseudo" />

                        <span class="absolute bottom-0 right-0 w-3 h-3 border-2 rounded-full border-roomies-gray1"
                            :class="getOtherMember(room).isOnline ? 'bg-green-500' : 'bg-gray-500'" />

                        <span v-if="getUnreadCount(room.id) > 0"
                            class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1 flex items-center justify-center text-xs font-semibold text-white border-2 border-roomies-gray1 bg-roomies-blue rounded-full shadow-md">
                            {{ getUnreadCount(room.id) }}
                        </span>
                    </div>

                    <span>{{ getOtherMember(room).pseudo }}</span>
                </div>
            </template>
        </ItemList>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoomStore } from '@/stores/roomStore'
import { useAuthStore } from '@/stores/authStore'
import { useUserStatusStore } from '@/stores/userStatusStore'
import { useChatStore } from '@/stores/chatStore'
import { useRouter, useRoute } from 'vue-router'

import BaseButton from '@/components/base/BaseButton.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import ItemList from '@/components/UI/ItemList.vue'
import CreatePrivateChatModalForm from '@/components/form/CreatePrivateChatModalForm.vue'
import type { Room } from '@/models/Room'

const router = useRouter()
const route = useRoute()
const roomStore = useRoomStore()
const authStore = useAuthStore()
const chatStore = useChatStore()
const userStatusStore = useUserStatusStore()

const search = ref('')
const showModal = ref(false)

const activeRoomId = computed<string | number | undefined>(() => {
    const id = route.name === 'private.chat' ? route.params.roomId : undefined
    return typeof id === 'string' || typeof id === 'number' ? id : undefined
})


onMounted(() => {
    roomStore.fetchPrivateRooms()
    chatStore.updateUnreadCounts(roomStore.privateRoomsList, authStore.user!.friendCode)
})

const isLoading = computed(() => roomStore.isLoading)

const filteredRooms = computed(() =>
    roomStore.privateRoomsList.filter(room => {
        const me = room.members.find(m => m.member.friendCode === authStore.user?.friendCode)
        return me?.isVisible !== false && room.name.toLowerCase().includes(search.value.toLowerCase())
    })
)

function getUnreadCount(roomId: string): number {
    return chatStore.unreadCounts[roomId] || 0
}

function getOtherMember(room: Room) {
    const other = room.members.find(m => m.member.friendCode !== authStore.user?.friendCode)
    const isOnline = other ? userStatusStore.isOnline(other.member.friendCode) : false

    return {
        pseudo: other?.member.pseudo || 'Discussion',
        avatar: other?.member.avatar || '',
        isOnline,
        friendCode: other?.member.friendCode || ''
    }
}

function handleRoomClick(room: Room) {
    router.push({ name: 'private.chat', params: { roomId: room.id } })
}

function hideRoom(room: Room) {
    const myCode = authStore.user?.friendCode
    if (myCode) {
        roomStore.setPrivateRoomVisibility(room.id, false, myCode)
        router.push({ name: 'home.private' })
    }
}
</script>
