<template>
    <div class="flex flex-col h-full border shadow-lg bg-roomies-gray4 rounded-xl border-roomies-gray2">
        <!-- Header -->
        <div class="flex items-center gap-2 px-4 py-3 border-b border-roomies-gray2">
            <i class="text-lg pi pi-comments" />
            <span class="text-lg font-semibold">Chat de la partie</span>
        </div>

        <!-- Messages -->
        <div ref="scrollContainer" class="flex-1 px-4 py-3 space-y-3 overflow-y-auto scrollbar">
            <div v-if="messages.length === 0" class="py-6 italic text-center text-white">
                Aucun message pour le moment.
            </div>
            <ChatMessage v-for="msg in messages" :key="msg.id" :message="msg" />
        </div>

        <!-- Input -->
        <MessageInput @send="handleSendMessage" />
    </div>
</template>

<script setup lang="ts">
import { ref, watch, nextTick, computed } from 'vue'
import { useWebSocketStore } from '@/stores/wsStore'
import { useGameStore } from '@/stores/gameStore'

import MessageInput from '@/components/chat/MessageInput.vue'
import ChatMessage from '@/components/chat/ChatMessage.vue'

const props = defineProps<{
    roomId: string
}>()

const gameStore = useGameStore()
const wsStore = useWebSocketStore()

// Messages de la room courante (stockés dans gameStore)
const messages = computed(() => gameStore.chatMessagesByRoom[props.roomId] || [])

const scrollContainer = ref<HTMLElement | null>(null)

const scrollToBottom = async () => {
    await nextTick()
    if (scrollContainer.value) {
        scrollContainer.value.scrollTop = scrollContainer.value.scrollHeight
    }
}

// Scroll à chaque nouveau message
watch(messages, () => scrollToBottom())

// Envoi du message au WebSocket
const handleSendMessage = (content: string) => {
    wsStore.send({
        type: 'game_chat_message',
        roomId: props.roomId,
        content,
    })
}
</script>