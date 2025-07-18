<template>
    <div v-if="!isReady"
        class="flex flex-col items-center gap-2 px-4 py-2 text-sm text-gray-300 rounded-lg shadow bg-roomies-gray3">
        <div class="w-8 h-8 border-4 rounded-full border-roomies-blue border-t-transparent animate-spin" />
        Chargement des discussions...
    </div>

    <div v-else-if="room" class="flex flex-col h-full p-6 text-white">
        <GroupChatHeader :room="room" />

        <div class="flex-1 p-4 h-3/6 bg-roomies-gray4 border-y-2 border-roomies-gray2">
            <div ref="scrollContainer" class="relative flex flex-col h-full p-6 space-y-4 scrollbar"
                @scroll.passive="onScroll">
                <div v-if="chatStore.isLoading" class="absolute top-0 left-0 right-0 flex justify-center">
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

    <div v-else class="p-6 text-gray-400">
        Discussion introuvable ou non autorisée.
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch, nextTick, onBeforeUnmount } from 'vue'
import { useRoute } from 'vue-router'
import { useRoomStore } from '@/stores/roomStore'
import { useAuthStore } from '@/stores/authStore'
import { useChatStore } from '@/stores/chatStore'
import { useWebSocketStore } from '@/stores/wsStore'

import ChatMessage from '@/components/chat/ChatMessage.vue'
import GroupChatHeader from '@/components/chat/GroupChatHeader.vue'
import MessageInput from '@/components/chat/MessageInput.vue'

import { format } from 'date-fns'
import { fr } from 'date-fns/locale'
import type { ChatMessage as TypeChatMessage } from '@/models/Message'

const route = useRoute()
const roomStore = useRoomStore()
const authStore = useAuthStore()
const chatStore = useChatStore()
const webSocketStore = useWebSocketStore()

const roomId = computed(() => route.params.roomId as string)
const scrollContainer = ref<HTMLElement | null>(null)

const isReady = computed(() =>
    authStore.userFetched &&
    authStore.user &&
    roomStore.allRooms.length > 0
)

const room = computed(() =>
    roomStore.allRooms.find((r) => r.id === roomId.value)
)

const messages = computed(() => chatStore.getMessagesForRoom(roomId.value))

watch(
    () => messages.value.length,
    () => {
        nextTick(() => {
            if (scrollContainer.value) {
                scrollContainer.value.scrollTop = scrollContainer.value.scrollHeight
            }
        })
    },
    { immediate: true }
)

watch(
    isReady,
    async (ready) => {
        if (ready && roomId.value) {
            await chatStore.loadInitialMessages(roomId.value)
            await roomStore.updateLastSeen(roomId.value)
        }
    },
    { immediate: true }
)

watch(
    () => roomId.value,
    async (newRoomId) => {
        if (isReady.value && newRoomId) {
            await chatStore.loadInitialMessages(newRoomId)
            await roomStore.updateLastSeen(newRoomId)
        }
    }
)

onBeforeUnmount(() => {
    if (roomId.value) roomStore.updateLastSeen(roomId.value)
})

const groupedMessages = computed(() => {
    const groups: Record<string, TypeChatMessage[]> = {}

    for (const msg of messages.value) {
        const dateKey = format(new Date(msg.createdAt), 'dd MMMM yyyy', { locale: fr })
        if (!groups[dateKey]) groups[dateKey] = []
        groups[dateKey].push(msg)
    }

    return groups
})

function handleSendMessage(content: string) {
    webSocketStore.send({
        type: 'message',
        roomId: roomId.value,
        content
    })
}

const loadMore = async () => {
    const container = scrollContainer.value
    const oldHeight = container?.scrollHeight || 0

    await chatStore.loadMoreMessages(roomId.value)

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
        chatStore.hasMore[roomId.value] &&
        !chatStore.isLoading
    ) {
        loadMore()
    }
}
</script>
