<template>
    <div class="pr-2 space-y-3 overflow-y-auto max-h-48 scrollbar">
        <div v-if="isLoading" class="flex items-center justify-center h-32">
            <div class="w-8 h-8 border-4 rounded-full border-roomies-blue border-t-transparent animate-spin"></div>
        </div>

        <template v-else>
            <div v-if="filteredFriends.length > 0" class="space-y-2">
                <label v-for="friend in filteredFriends" :key="friend[friendKey]"
                    :for="`${idPrefix}-checkbox-${friend[friendKey]}`"
                    class="flex items-center justify-between p-3 transition-colors rounded-lg cursor-pointer bg-roomies-gray2 hover:bg-roomies-gray1">
                    <div class="flex items-center space-x-3">
                        <img :src="friend.avatar" alt="avatar" class="object-cover w-8 h-8 rounded-full" />
                        <span class="font-medium text-white">{{ friend.pseudo }}</span>
                    </div>

                    <div class="relative">
                        <input type="checkbox" :id="`${idPrefix}-checkbox-${friend[friendKey]}`"
                            :name="`${idPrefix}-${friend[friendKey]}`" :value="friend[friendKey]"
                            :checked="selectedFriends.includes(friend[friendKey])"
                            @change="handleCheckboxChange(friend[friendKey], $event)"
                            class="absolute w-0 h-0 opacity-0" />
                        <div class="flex items-center justify-center w-6 h-6 transition-all border-2 rounded-md border-roomies-blue"
                            :class="{ 'bg-roomies-blue': selectedFriends.includes(friend[friendKey]) }">
                            <svg v-if="selectedFriends.includes(friend[friendKey])" class="w-4 h-4 text-white"
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
                <p v-if="searchQuery.length > 0" class="text-sm">Essayez avec un autre pseudo</p>
            </div>
        </template>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps({
    friends: {
        type: Array as () => Array<{
            [key: string]: any
            avatar: string
            pseudo: string
        }>,
        required: true,
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
    selectedFriends: {
        type: Array as () => string[],
        required: true,
    },
    searchQuery: {
        type: String,
        default: '',
    },
    friendKey: {
        type: String,
        default: 'friendCode',
    },
    idPrefix: {
        type: String,
        default: 'friend',
    },
})

const emit = defineEmits(['update:selectedFriends'])

const filteredFriends = computed(() =>
    props.friends.filter((friend) =>
        friend.pseudo.toLowerCase().includes(props.searchQuery.toLowerCase())
    )
)

function handleCheckboxChange(friendId: string, event: Event) {
    const isChecked = (event.target as HTMLInputElement).checked
    const updatedSelection = [...props.selectedFriends]

    if (isChecked) {
        updatedSelection.push(friendId)
    } else {
        const index = updatedSelection.indexOf(friendId)
        if (index > -1) {
            updatedSelection.splice(index, 1)
        }
    }

    emit('update:selectedFriends', updatedSelection)
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
    border-radius: 2px;
}

.scrollbar::-webkit-scrollbar-track {
    background-color: transparent;
}

.scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #6b7280 transparent;
}
</style>