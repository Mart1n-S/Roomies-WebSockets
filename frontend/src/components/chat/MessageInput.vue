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

const message = ref('')
const maxLength = 1000
const isSending = ref(false)

const textareaRef = ref<HTMLTextAreaElement | null>(null)

const autoResize = () => {
    if (!textareaRef.value) return
    textareaRef.value.style.height = 'auto' // reset before measuring
    textareaRef.value.style.height = textareaRef.value.scrollHeight + 'px'
}

const emit = defineEmits<{ send: [message: string] }>()

const sendMessage = async () => {
    if (!message.value.trim()) return

    isSending.value = true

    emit('send', message.value.trim())

    message.value = ''

    await nextTick() // attendre que le DOM reflète le reset du modèle
    if (textareaRef.value) {
        textareaRef.value.style.height = 'auto' // replier à la taille d’origine
    }

    isSending.value = false
}
</script>
