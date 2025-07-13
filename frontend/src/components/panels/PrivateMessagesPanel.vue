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

        <div class="flex-1 space-y-1 overflow-y-auto scrollbar">
            <!-- Loader pendant le chargement -->
            <div v-if="isLoading" class="flex items-center justify-center h-32">
                <div class="w-8 h-8 border-4 rounded-full border-roomies-blue border-t-transparent animate-spin"></div>
            </div>

            <!-- Liste des conversations privées -->
            <template v-else>
                <template v-if="filteredRooms.length">
                    <div v-for="room in filteredRooms" :key="room.id"
                        class="flex items-center p-2 rounded cursor-pointer hover:bg-roomies-gray2">
                        <img :src="getOtherMember(room)?.member.avatar" class="w-8 h-8 mr-2 rounded-full" />
                        <span>{{ getOtherMember(room)?.member.pseudo || 'Discussion' }}</span>
                    </div>
                </template>
                <div v-else class="p-4 text-sm text-center text-gray-400">
                    Aucun ami
                </div>
            </template>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoomStore } from '@/stores/roomStore'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import { useAuthStore } from '@/stores/authStore'
import type { Room } from '@/models/Room'

const roomStore = useRoomStore()
const search = ref('')
const authStore = useAuthStore()

// Fetch des rooms privées au montage
onMounted(() => {
    roomStore.fetchPrivateRooms()
})

const isLoading = computed(() => roomStore.isLoading)

const filteredRooms = computed(() =>
    roomStore.privateRoomsList.filter(r =>
        r.name.toLowerCase().includes(search.value.toLowerCase())
    )
)

function getOtherMember(room: Room) {
    return room.members.find((m) =>
        m.member.friendCode !== authStore.user?.friendCode
    )
}

</script>
