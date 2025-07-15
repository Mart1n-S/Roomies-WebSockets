<template>
    <div v-if="user"
        class="fixed inset-0 z-50 flex items-center justify-center px-3 bg-black bg-opacity-60 animate-fade-in"
        @click.self="close" @keyup.esc="close" tabindex="0">
        <div
            class="bg-roomies-gray4 rounded-2xl shadow-2xl px-8 py-7 flex flex-col items-center relative min-w-[320px] border border-roomies-gray2">

            <!-- Fermer (BaseButton, variant=secondary, iconOnly, rounded-full) -->
            <BaseButton variant="secondary"
                class="!p-1.5 !w-9 !h-9 absolute top-4 right-4 rounded-full border border-roomies-gray2 shadow hover:bg-roomies-gray2/70 transition"
                iconLeft @click="close" :aria-label="'Fermer la fenêtre'" noIconSpace>
                <template #icon-left>
                    <i class="text-lg pi pi-times" />
                </template>
            </BaseButton>


            <img :src="user.avatar" class="w-24 h-24 mb-3 border-4 rounded-full shadow-lg border-roomies-gray3"
                :alt="user.pseudo" />
            <div class="mb-1 text-xl font-semibold tracking-wide text-white">{{ user.pseudo }}</div>

            <div class="flex items-center mb-4 space-x-2 text-sm text-gray-300">
                <span>Code ami :</span>
                <span class="px-2 py-1 font-mono text-gray-300 rounded select-all bg-roomies-gray2">
                    {{ user.friendCode }}
                </span>
                <BaseButton variant="secondary" class="ml-1 !px-2 !py-1.5" iconLeft @click="copyCode"
                    :aria-label="copied ? 'Copié !' : 'Copier le code ami'" noIconSpace>
                    <template #icon-left>
                        <i v-if="!copied" class="text-base pi pi-copy" />
                        <i v-else class="text-base text-green-400 pi pi-check" />
                    </template>
                </BaseButton>
            </div>

            <BaseButton variant="primary" class="w-full mt-2" :disabled="isMe || isFriend || isPending || isReceived"
                @click="addFriend">
                <template v-if="isMe">C'est toi</template>
                <template v-else-if="isFriend">Déjà ami</template>
                <template v-else-if="isPending">Demande envoyée</template>
                <template v-else-if="isReceived">Demande reçue</template>
                <template v-else>Ajouter en ami</template>
            </BaseButton>

        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import BaseButton from '@/components/base/BaseButton.vue'
import { useAuthStore } from '@/stores/authStore'
import { useFriendshipStore } from '@/stores/friendshipStore'
import type { UserPublic } from '@/models/User'

const props = defineProps<{
    user: UserPublic | null
}>()

const emit = defineEmits<{
    (e: 'close'): void
    (e: 'add-friend', friendCode: string): void
}>()

const authStore = useAuthStore()
const friendshipStore = useFriendshipStore()

const isMe = computed(() => props.user?.friendCode === authStore.user?.friendCode)
const isFriend = computed(() =>
    !!props.user &&
    friendshipStore.friendUsers.some(f => f.friendCode === props.user?.friendCode)
)
const isPending = computed(() =>
    !!props.user && friendshipStore.sentRequests.some(req =>
        req.friend?.friendCode === props.user!.friendCode
    )
)
const isReceived = computed(() =>
    !!props.user && friendshipStore.receivedRequests.some(req =>
        req.friend?.friendCode === props.user!.friendCode
    )
)

const copied = ref(false)
function copyCode() {
    if (!props.user) return
    navigator.clipboard.writeText(props.user.friendCode)
    copied.value = true
    setTimeout(() => (copied.value = false), 1200)
}
function addFriend() {
    if (!props.user || isMe.value || isFriend.value || isPending.value || isReceived.value) return
    emit('add-friend', props.user.friendCode)
    emit('close')
}
function close() {
    emit('close')
}
// focus pour escape
onMounted(() => {
    setTimeout(() => {
        const el = document.querySelector('.fixed.inset-0.z-50')
        if (el) (el as HTMLElement).focus()
    }, 0)
})
</script>

<style scoped>
@keyframes fade-in {
    0% {
        opacity: 0;
        transform: scale(.96);
    }

    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fade-in {
    animation: fade-in .22s cubic-bezier(.4, 0, .2, 1);
}
</style>
