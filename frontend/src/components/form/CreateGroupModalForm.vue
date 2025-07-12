<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center px-3 bg-black bg-opacity-60" @click.self="close">
        <div class="w-full max-w-md p-6 shadow-lg bg-roomies-gray4 rounded-xl">
            <form @submit.prevent="submitForm" class="space-y-4">
                <!-- Titre -->
                <h2 class="text-xl font-semibold text-white">Créer un groupe</h2>

                <!-- Champ nom du groupe -->
                <BaseInput name="name" label="Nom du groupe" v-model="form.name" autocomplete="off"
                    placeholder="Ex: Serveur de jeux" :error="errors.name" @valid="handleNameValidation" />

                <!-- Champ de recherche -->
                <BaseInput name="search" label="Rechercher un ami" v-model="search" autocomplete="off"
                    placeholder="Tapez un pseudo..." type="text" />

                <!-- Liste des amis -->
                <div class="pr-2 space-y-3 overflow-y-auto max-h-48 scrollbar">
                    <div v-if="friendshipStore.isLoading" class="flex items-center justify-center h-32">
                        <div
                            class="w-8 h-8 border-4 rounded-full border-roomies-blue border-t-transparent animate-spin">
                        </div>
                    </div>

                    <template v-else>
                        <div v-if="filteredFriends.length > 0" class="space-y-2">
                            <label v-for="friend in filteredFriends" :key="friend.friendCode"
                                :for="`friend-checkbox-${friend.friendCode}`"
                                class="flex items-center justify-between p-3 transition-colors rounded-lg cursor-pointer bg-roomies-gray2 hover:bg-roomies-gray1">

                                <!-- Partie gauche : avatar + pseudo -->
                                <div class="flex items-center space-x-3">
                                    <img :src="friend.avatar" alt="avatar" class="object-cover w-8 h-8 rounded-full" />
                                    <span class="font-medium text-white">{{ friend.pseudo }}</span>
                                </div>

                                <!-- Checkbox stylisée -->
                                <div class="relative">
                                    <input type="checkbox" :id="`friend-checkbox-${friend.friendCode}`"
                                        :name="`friend-${friend.friendCode}`" :value="friend.friendCode"
                                        v-model="form.members" class="absolute w-0 h-0 opacity-0" />
                                    <div class="flex items-center justify-center w-6 h-6 transition-all border-2 rounded-md border-roomies-blue"
                                        :class="{ 'bg-roomies-blue': form.members.includes(friend.friendCode) }">
                                        <svg v-if="form.members.includes(friend.friendCode)" class="w-4 h-4 text-white"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div v-else class="p-4 text-center text-white">
                            <p>Aucun ami trouvé</p>
                            <p v-if="search.length > 0" class="text-sm">Essayez avec un autre pseudo</p>
                        </div>
                    </template>
                </div>


                <!-- Message d'erreur global -->
                <p v-if="errors.members" class="text-sm text-red-500">{{ errors.members }}</p>

                <!-- Boutons de création et fermeture -->
                <div class="flex flex-col-reverse gap-3 mt-4 sm:flex-row sm:items-center">
                    <BaseButton @click="emit('close')" variant="danger" class="flex-1 text-center">
                        Fermer
                    </BaseButton>
                    <BaseButton type="submit" :loading="roomStore.loading" class="flex-1 text-center">
                        Créer le groupe
                    </BaseButton>
                </div>

            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watchEffect, watch } from 'vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import { useFriendshipStore } from '@/stores/friendshipStore'
import { useRoomStore } from '@/stores/roomStore'
import { useToast } from 'vue-toastification'
import { sendWebSocketMessage } from '@/services/websocket'

const emit = defineEmits(['close'])
const friendshipStore = useFriendshipStore()
const toast = useToast()
const roomStore = useRoomStore()

// État du formulaire
const form = ref({
    name: '',
    members: [] as string[]
})

const search = ref('')
const errors = ref<{ name?: string; members?: string }>({})

// Chargement conditionnel des amis
onMounted(() => {
    if (shouldRefreshFriends.value) {
        friendshipStore.fetchFriendships()
    }
})

// Vérifie si on doit rafraîchir la liste
const shouldRefreshFriends = computed(() => {
    return friendshipStore.friendships.length === 0 ||
        (friendshipStore.lastUpdated &&
            (Date.now() - friendshipStore.lastUpdated) > 300000)
})

// Watch pour rafraîchir si nécessaire
watchEffect(() => {
    if (shouldRefreshFriends.value && !friendshipStore.isLoading) {
        friendshipStore.fetchFriendships()
    }
})

// Liste des amis (via le getter du store)
const friends = computed(() => friendshipStore.friendUsers)

// Liste filtrée par pseudo
const filteredFriends = computed(() =>
    friends.value.filter(friend =>
        friend.pseudo.toLowerCase().includes(search.value.toLowerCase())
    )
)

// Validation en temps réel du nom
watch(() => form.value.name, (newName) => {
    if (newName.length >= 3 && newName.length <= 30) {
        errors.value.name = ''
    }
})

// Validation en temps réel des membres
watch(() => form.value.members, (newMembers) => {
    if (newMembers.length >= 2) {
        errors.value.members = ''
    }
}, { deep: true })

function handleNameValidation(isValid: boolean) {
    if (isValid) {
        errors.value.name = ''
    } else if (form.value.name.length > 0) {
        errors.value.name = 'Le nom doit contenir entre 3 et 30 caractères.'
    }
}

// Validation avant soumission
function validateForm() {
    let isValid = true
    errors.value = {}

    if (form.value.name.length < 3 || form.value.name.length > 30) {
        errors.value.name = 'Le nom doit contenir entre 3 et 30 caractères.'
        isValid = false
    }

    if (form.value.members.length < 2) {
        errors.value.members = 'Sélectionnez au moins 2 amis.'
        isValid = false
    }

    return isValid
}

// Soumission
async function submitForm() {
    if (!validateForm()) return

    try {
        await roomStore.createGroupAndNotify(form.value)
        toast.success('Groupe créé avec succès !')
        close()
    } catch (e: any) {
        console.error('Erreur:', e)
        errors.value = {}

        if (e.response?.data?.violations) {
            e.response.data.violations.forEach((violation: any) => {
                if (violation.propertyPath === 'name') {
                    errors.value.name = violation.message
                } else if (violation.propertyPath === 'members') {
                    errors.value.members = violation.message
                }
            })
        } else {
            toast.error(e.response?.data?.message || 'Erreur lors de la création du groupe')
        }
    }
}

// Fermer la modal
function close() {
    emit('close')
}
</script>

<style scoped>
.scrollbar {
    scrollbar-gutter: stable;
    overflow-y: auto;
}

.scrollbar::-webkit-scrollbar {
    width: 2px;
}

.scrollbar::-webkit-scrollbar-thumb {
    background-color: #6b7280;
    /* gray-500 */
    border-radius: 2px;
}

.scrollbar::-webkit-scrollbar-track {
    background-color: transparent;
}

/* Pour Firefox */
.scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #6b7280 transparent;
}
</style>