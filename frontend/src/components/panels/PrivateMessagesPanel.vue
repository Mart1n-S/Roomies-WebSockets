<template>
    <div class="flex flex-col h-full p-3 space-y-3 text-white">
        <BaseInput name="search" label="Rechercher un ami" v-model="search" autocomplete="off"
            placeholder="Tapez un pseudo..." type="text" />

        <BaseButton iconLeft class="justify-start">
            <template #icon-left><i class="pi pi-user" /></template>
            Amis
        </BaseButton>

        <BaseButton iconLeft class="justify-start">
            <template #icon-left><i class="pi pi-plus" /></template>
            Nouvelle conversation
        </BaseButton>

        <hr class="h-[1px] border-roomies-gray1 my-3" />
        <p class="mt-4 text-gray-300">Messages privés</p>

        <ItemList :items="filteredRooms" :is-loading="isLoading" :show-delete-button="true"
            :empty-message="search.length ? 'Aucun résultat, essayez avec un autre pseudo 🥸' : 'Aucune conversation'"
            @item-click="handleRoomClick" @item-action="hideRoom">
            <template #item-content="{ item: room }">
                <div class="flex items-center space-x-2">
                    <div class="relative w-8 h-8">
                        <img v-if="isRoom(room)" :src="getOtherMember(room).member.avatar" class="w-8 h-8 rounded-full"
                            :alt="getOtherMember(room).member.pseudo" />
                        <!-- Badge de statut -->
                        <span v-if="isRoom(room)"
                            class="absolute bottom-0 right-0 w-3 h-3 border-2 rounded-full border-roomies-gray1"
                            :class="getOtherMember(room).member.isOnline ? 'bg-green-500' : 'bg-gray-500'"></span>
                    </div>
                    <span v-if="isRoom(room)">
                        {{ getOtherMember(room).member.pseudo || 'Discussion' }}
                    </span>
                    <span v-else>Discussion</span>
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
import BaseButton from '@/components/base/BaseButton.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import ItemList from '@/components/UI/ItemList.vue'
import type { Room } from '@/models/Room'
import type { ListItem } from '@/models/ListItem'

const roomStore = useRoomStore()
const authStore = useAuthStore()
const search = ref('')

onMounted(() => {
    roomStore.fetchPrivateRooms()
})

const isLoading = computed(() => roomStore.isLoading)

const filteredRooms = computed(() =>
    roomStore.privateRoomsList.filter(room => {
        const currentUserRoomUser = room.members.find(m => m.member.friendCode === authStore.user?.friendCode)
        return currentUserRoomUser?.isVisible !== false &&
            room.name.toLowerCase().includes(search.value.toLowerCase())
    })
)

function isRoom(item: ListItem | Room): item is Room {
    return 'isGroup' in item && 'createdAt' in item && 'members' in item;
}

function getOtherMember(room: Room) {
    const userStatusStore = useUserStatusStore()
    const other = room.members.find(m => m.member.friendCode !== authStore.user?.friendCode)

    return other
        ? {
            ...other,
            member: {
                ...other.member,
                isOnline: userStatusStore.isOnline(other.member.friendCode)
            }
        }
        : {
            member: {
                pseudo: 'Discussion',
                avatar: '',
                friendCode: '',
                isOnline: false
            }
        }
}


function handleRoomClick(room: ListItem | Room) {
    if (isRoom(room)) {
        console.log('Room selected:', room);
    }
}

function hideRoom(room: ListItem | Room) {
    if (isRoom(room) && typeof room.id === 'string') {
        const myFriendCode = authStore.user?.friendCode;
        if (myFriendCode) {
            roomStore.setPrivateRoomVisibility(room.id, false, myFriendCode);
        }
    }
}

</script>
