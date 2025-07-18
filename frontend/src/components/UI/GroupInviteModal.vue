<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center px-3 bg-black bg-opacity-60" @click.self="close">
        <div class="w-full max-w-md p-6 shadow-lg bg-roomies-gray4 rounded-xl">
            <form @submit.prevent="submitForm" class="space-y-4">
                <!-- Titre -->
                <h2 class="text-xl font-semibold text-white">Inviter des amies</h2>

                <!-- Champ de recherche -->
                <BaseInput name="search" label="Rechercher un ami" v-model="search" autocomplete="off"
                    placeholder="Tapez un pseudo..." type="text" />

                <!-- Composant FriendSelectionList -->
                <FriendSelectionList :friends="friends" :isLoading="friendshipStore.isLoading"
                    :selectedFriends="form.members" :searchQuery="search"
                    @update:selectedFriends="(newValue) => form.members = newValue" />

                <!-- Message d'erreur global -->
                <p v-if="errors.members" class="text-sm text-red-500">{{ errors.members }}</p>

                <!-- Boutons de création et fermeture -->
                <div class="flex flex-col-reverse gap-3 mt-4 sm:flex-row sm:items-center">
                    <BaseButton @click="emit('close')" variant="danger" class="flex-1 text-center">
                        Fermer
                    </BaseButton>
                    <BaseButton type="submit" :loading="roomStore.loading" class="flex-1 text-center">
                        Inviter
                    </BaseButton>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useFriendshipStore } from '@/stores/friendshipStore'
import { useRoomStore } from '@/stores/roomStore'
import { useToast } from 'vue-toastification'

import BaseInput from '@/components/base/BaseInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import FriendSelectionList from '@/components/form/inputs/FriendSelectionList.vue'

const props = defineProps<{
    roomId: string
}>()

const emit = defineEmits(['close'])

const friendshipStore = useFriendshipStore()
const roomStore = useRoomStore()
const toast = useToast()

const form = ref({
    members: [] as string[]
})

const search = ref('')
const errors = ref<{ members?: string }>({})

// Récupère les membres actuels de la room
const room = computed(() => roomStore.allRooms.find(r => r.id === props.roomId))
const existingMemberCodes = computed(() => room.value?.members.map(m => m.member.friendCode) || [])

// Liste des amis disponibles (exclut ceux déjà dans le groupe)
const friends = computed(() => {
    return friendshipStore.friendUsers.filter(
        friend => !existingMemberCodes.value.includes(friend.friendCode)
    )
})

function validateForm() {
    errors.value = {}
    if (form.value.members.length === 0) {
        errors.value.members = 'Veuillez sélectionner au moins un ami.'
        return false
    }
    return true
}

async function submitForm() {
    if (!validateForm()) return

    try {
        await roomStore.addMembersToRoom(props.roomId, form.value.members)
        emit('close')
        form.value.members = []
    } catch (e: any) {
        console.error(e)
        toast.error('Erreur lors de l’invitation.')
    }
}


function close() {
    emit('close')
}
</script>
