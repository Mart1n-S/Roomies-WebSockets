<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center px-3 bg-black bg-opacity-60" @click.self="close">
        <div class="w-full max-w-md p-6 text-white shadow-lg bg-roomies-gray4 rounded-xl">
            <h2 class="mb-2 text-xl font-semibold">Actions sur le membre</h2>
            <div class="flex items-center mb-4 space-x-3">
                <img :src="member.member.avatar" class="rounded-full w-14 h-14" :alt="member.member.pseudo" />
                <div>
                    <div class="text-lg font-semibold">{{ member.member.pseudo }}</div>
                    <div class="text-sm text-gray-400">Code ami : {{ member.member.friendCode }}</div>
                </div>
            </div>
            <form @submit.prevent="sendRequest" class="space-y-4">
                <BaseInput name="friendCode" label="Code ami" v-model="friendCode" placeholder="Ex: RM1234"
                    autocomplete="off" :error="error" @valid="handleFriendCodeValidation" />

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center">
                    <BaseButton variant="danger" @click="close" class="flex-1 text-center">Fermer</BaseButton>
                    <BaseButton type="submit" :loading="isLoading" class="flex-1 text-center">Envoyer</BaseButton>
                </div>
            </form>

            <!-- Ajoute l'erreur si besoin -->
            <div v-if="error" class="mt-2 text-xs text-center text-red-400">{{ error }}</div>

            <!-- Bouton Supprimer du serveur -->
            <BaseButton v-if="showKickButton" variant="danger" class="w-full mt-5" @click="onKick">
                Supprimer du serveur
            </BaseButton>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import { useWebSocketStore } from '@/stores/wsStore'
import { useAuthStore } from '@/stores/authStore'
import type { RoomMember } from '@/models/RoomMember'

const props = defineProps<{
    member: RoomMember
    currentUserRole: 'owner' | 'admin' | 'user'
    room: { members: RoomMember[] }
}>()

const emit = defineEmits<{
    (e: 'close'): void
    (e: 'kick'): void
}>()

const wsStore = useWebSocketStore()
const authStore = useAuthStore()

const friendCode = ref(props.member.member.friendCode)
const error = ref('')
const isLoading = ref(false)

// On vérifie si le membre sélectionné est l'utilisateur connecté
const isMe = ref(authStore.user?.friendCode === props.member.member.friendCode)

watch(() => props.member.member.friendCode, (newVal) => {
    friendCode.value = newVal
})

// ---- LOGIQUE DU BOUTON SUPPRIMER ----
const showKickButton = ref(false)
watch(
    () => [props.currentUserRole, props.member.role, isMe.value],
    () => {
        if (isMe.value) {
            showKickButton.value = false
        } else if (props.currentUserRole === 'owner') {
            showKickButton.value = props.member.role !== 'owner'
        } else if (props.currentUserRole === 'admin') {
            showKickButton.value = props.member.role === 'user'
        } else {
            showKickButton.value = false
        }
    },
    { immediate: true }
)

// Validation en temps réel du code ami
watch(() => friendCode.value, (newCode) => {
    if (newCode.length === 20) {
        error.value = ''
    }
})

// Gestion des retours WebSocket (demande d’ami)
watch(() => wsStore.lastMessage, (msg) => {
    if (!msg) return
    if (msg.type === 'friend_request_error' && msg.payload?.friendCode === friendCode.value) {
        error.value = msg.message || 'Erreur lors de l’envoi de la demande.'
        isLoading.value = false
    }
    if (msg.type === 'friend_request_success' && msg.payload?.friendCode === friendCode.value) {
        // on ferme directement la modale comme dans AddFriendModalForm
        close()
    }
})

function handleFriendCodeValidation(isValid: boolean) {
    if (isValid) {
        error.value = ''
    } else if (friendCode.value.length > 0) {
        error.value = 'Le code ami doit contenir exactement 20 caractères.'
    }
}

function validateForm() {
    let isValid = true
    error.value = ''
    if (friendCode.value.length !== 20) {
        error.value = 'Le code ami doit contenir exactement 20 caractères.'
        isValid = false
    }
    return isValid
}

function close() {
    emit('close')
}

async function sendRequest() {
    if (!validateForm()) return

    isLoading.value = true

    wsStore.send({
        type: 'friend_request',
        payload: {
            friendCode: friendCode.value.trim()
        }
    })
}

function onKick() {
    emit('kick')
}
</script>
