<template>
    <div v-if="!isReady"
        class="flex flex-col items-center gap-2 px-4 py-2 text-sm text-gray-300 rounded-lg shadow bg-roomies-gray3">
        <div class="w-8 h-8 border-4 rounded-full border-roomies-blue border-t-transparent animate-spin" />
        Chargement du chat global...
    </div>

    <div v-else class="flex flex-col h-full p-6 text-white">
        <div class="flex items-center justify-center px-4 py-2 rounded-t-lg bg-roomies-gray4">
            <i class="mr-2 text-xl pi pi-comments" />
            <h2 class="text-xl font-semibold">Chat</h2>
        </div>
        <div class="flex-1 p-4 h-3/6 bg-roomies-gray4 border-y-2 border-roomies-gray2">
            <div ref="scrollContainer" class="relative flex flex-col h-full p-6 space-y-4 scrollbar"
                @scroll.passive="onScroll">
                <div v-if="isLoading" class="absolute top-0 left-0 right-0 flex justify-center">
                    <div
                        class="flex flex-col items-center gap-2 px-4 py-2 text-sm text-gray-300 rounded-lg shadow bg-roomies-gray3">
                        <div
                            class="w-8 h-8 border-4 rounded-full border-roomies-blue border-t-transparent animate-spin" />
                        Chargement des messages...
                    </div>
                </div>

                <div v-for="(msgs, dateKey) in groupedMessages" :key="dateKey">
                    <div class="flex items-center justify-center py-2 text-sm text-gray-400">
                        <div class="flex-grow mx-2 border-t border-gray-600"></div>
                        <span>{{ dateKey }}</span>
                        <div class="flex-grow mx-2 border-t border-gray-600"></div>
                    </div>
                    <ChatMessage v-for="msg in msgs" :key="msg.id" :message="msg" />
                </div>
            </div>
        </div>
        <MessageInput @send="handleSendMessage" />
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick, onMounted, onBeforeUnmount } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useGlobalChatStore } from '@/stores/globalChatStore'
import { useWebSocketStore } from '@/stores/wsStore'
import ChatMessage from '@/components/chat/ChatMessage.vue'
import MessageInput from '@/components/chat/MessageInput.vue'
import { format } from 'date-fns'
import { fr } from 'date-fns/locale'
import type { ChatMessage as TypeChatMessage } from '@/models/Message'

const authStore = useAuthStore()
const globalChatStore = useGlobalChatStore()
const wsStore = useWebSocketStore()

const scrollContainer = ref<HTMLElement | null>(null)

const isReady = computed(() => authStore.userFetched && authStore.user)
const isLoading = computed(() => globalChatStore.isLoading)
const messages = computed(() => globalChatStore.messages)

const scrollToBottom = () => {
    nextTick(() => {
        if (scrollContainer.value) {
            scrollContainer.value.scrollTop = scrollContainer.value.scrollHeight
        }
    })
}

const groupedMessages = computed(() => {
    const groups: Record<string, TypeChatMessage[]> = {}
    for (const msg of messages.value) {
        const dateKey = format(new Date(msg.createdAt), 'dd MMMM yyyy', { locale: fr })
        if (!groups[dateKey]) groups[dateKey] = []
        groups[dateKey].push(msg)
    }
    return groups
})

watch(
    () => messages.value.length,
    () => scrollToBottom(),
    { immediate: true }
)

onMounted(async () => {
    if (isReady.value) {
        wsStore.send({ type: 'global_chat_join' })
        globalChatStore.join()
        scrollToBottom()
    }
})

onBeforeUnmount(() => {
    wsStore.send({ type: 'global_chat_leave' })
    globalChatStore.leave()
})

const loadMore = async () => {
    const container = scrollContainer.value
    const oldHeight = container?.scrollHeight || 0

    await globalChatStore.loadMoreMessages()

    nextTick(() => {
        if (container) {
            const newHeight = container.scrollHeight
            container.scrollTop = newHeight - oldHeight
        }
    })
}

const onScroll = () => {
    const container = scrollContainer.value
    if (
        container &&
        container.scrollTop <= 10 &&
        globalChatStore.hasMore &&
        !globalChatStore.isLoading
    ) {
        loadMore()
    }
}

function handleSendMessage(content: string) {
    wsStore.send({
        type: 'global_message',
        content
    })
}
</script>
