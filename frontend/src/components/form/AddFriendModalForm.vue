<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center px-3 bg-black bg-opacity-60" @click.self="close">
        <div class="w-full max-w-md p-6 text-white shadow-lg bg-roomies-gray4 rounded-xl">
            <h2 class="mb-2 text-xl font-semibold">Ajouter des amis</h2>
            <p class="mb-4 text-sm text-gray-300">
                Tu peux ajouter des amis grâce à leur code ami.
            </p>

            <form @submit.prevent="sendRequest" class="space-y-4">
                <BaseInput name="friendCode" label="Code ami" v-model="friendCode" placeholder="Ex: RM1234"
                    autocomplete="off" :error="error" @valid="handleFriendCodeValidation" />

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center">
                    <BaseButton variant="danger" @click="close" class="flex-1 text-center">Fermer</BaseButton>
                    <BaseButton type="submit" :loading="isLoading" class="flex-1 text-center">Envoyer</BaseButton>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import { useWebSocketStore } from '@/stores/wsStore'

const emit = defineEmits(['close'])

const friendCode = ref('')
const error = ref('')
const isLoading = ref(false)
const wsStore = useWebSocketStore()

onMounted(() => {
    wsStore.$onAction(({ name, args, after }) => {
        // Pour éviter conflits avec d'autres wsStore.send
        if (name === 'onMessage') {
            after((res) => {
                if (wsStore.lastMessage?.type === 'friend_request_error') {
                    error.value = wsStore.lastMessage.message || 'Erreur inconnue.'
                    isLoading.value = false
                }

                if (wsStore.lastMessage?.type === 'friend_request_success') {
                    close()
                }
            })
        }
    })
})

// Validation en temps réel du code ami
watch(() => friendCode.value, (newCode) => {
    if (newCode.length === 20) {
        error.value = ''
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
</script>