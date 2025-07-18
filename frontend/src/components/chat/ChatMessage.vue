<template>
    <div class="flex items-start mb-4 space-x-3">
        <img :src="message.sender.avatar" alt="Avatar" class="w-10 h-10 rounded-full" />
        <div>
            <div class="flex items-center space-x-2">
                <span class="font-semibold text-white">{{ message.sender.pseudo }}</span>
                <span class="text-xs text-gray-400">{{ formattedDateTime }}</span>
            </div>
            <p class="text-sm text-white whitespace-pre-line">{{ message.content }}</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { ChatMessage } from '@/models/Message'

// Déstructuration explicite de la prop
const { message } = defineProps<{ message: ChatMessage }>()

const formattedDateTime = computed(() => {
    const date = new Date(message.createdAt)
    return date.toLocaleString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).replace(',', ' -')
})
</script>
