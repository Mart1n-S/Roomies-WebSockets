<template>
    <div class="flex items-end gap-2 p-4 rounded-b-lg bg-roomies-gray4">
        <textarea id="chat-message-input" name="chatMessage" v-model="message" @input="autoResize"
            @keydown.enter.exact.prevent="sendMessage" :maxlength="maxLength" placeholder="Écrivez un message..."
            class="flex-1 p-3 overflow-auto text-sm text-white rounded-md outline-none resize-none bg-roomies-gray3 max-h-40 scrollbar"
            rows="1" ref="textareaRef" autocomplete="on"></textarea>

        <button @click="sendMessage" :disabled="isSending || message.trim() === ''"
            class="flex items-center justify-center w-10 h-10 text-white transition rounded-full bg-roomies-blue hover:opacity-90 disabled:opacity-40">
            <i class="pi pi-send" />
        </button>
    </div>
</template>

<script setup lang="ts">
import { ref, nextTick } from 'vue'

// Valeur contrôlée du textarea (liaison v-model)
const message = ref('')

// Limite la longueur d'un message (anti-flood/UX)
const maxLength = 1000

// Indique si un message est en cours d’envoi (désactive le bouton/send)
const isSending = ref(false)

// Référence au textarea pour l’autoresize
const textareaRef = ref<HTMLTextAreaElement | null>(null)

/**
 * Ajuste dynamiquement la hauteur du textarea selon le contenu.
 * UX : évite d’avoir un scroll interne gênant pour la saisie de messages longs.
 */
const autoResize = () => {
    if (!textareaRef.value) return
    textareaRef.value.style.height = 'auto' // Reset avant mesure
    textareaRef.value.style.height = textareaRef.value.scrollHeight + 'px'
}

// Déclare l’événement custom 'send' (payload = string message)
const emit = defineEmits<{ send: [message: string] }>()

/**
 * Envoi du message :
 * - Bloque les doubles clics/envois vides
 * - Émet l’événement au parent avec le contenu
 * - Reset le champ et la hauteur
 */
const sendMessage = async () => {
    if (!message.value.trim()) return

    isSending.value = true

    emit('send', message.value.trim())

    message.value = '' // Réinitialise le champ

    // Attend que le DOM reflète le reset, puis replie le textarea
    await nextTick()
    if (textareaRef.value) {
        textareaRef.value.style.height = 'auto'
    }

    isSending.value = false
}
</script>
