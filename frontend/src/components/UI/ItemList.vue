<template>
    <div class="flex-1 space-y-1 overflow-y-auto scrollbar">
        <!-- Loader -->
        <div v-if="isLoading" class="flex items-center justify-center h-32">
            <div class="w-8 h-8 border-4 rounded-full border-roomies-blue border-t-transparent animate-spin"></div>
        </div>

        <!-- Liste -->
        <template v-else>
            <template v-if="items.length">
                <div v-for="item in items" :key="itemKey ? item[itemKey] : item.id" :class="[
                    'group relative flex items-center justify-between p-2 rounded cursor-pointer hover:bg-roomies-gray2',
                    item.id === activeId ? 'bg-roomies-gray2' : ''
                ]" @click="$emit('item-click', item)">
                    <!-- Slot personnalisable pour le contenu -->
                    <slot name="item-content" :item="item">
                        <div class="flex items-center space-x-2">
                            <img v-if="item.avatar" :src="item.avatar" class="w-8 h-8 rounded-full"
                                :alt="item.pseudo || 'Avatar'">
                            <span>{{ item.pseudo || item.name || 'Item' }}</span>
                        </div>
                    </slot>

                    <!-- Bouton optionnel -->
                    <slot name="item-action" :item="item">
                        <button v-if="showDeleteButton" @click.stop="$emit('item-action', item)"
                            class="text-gray-400 transition-opacity duration-150 opacity-0 hover:text-red-500 group-hover:opacity-100"
                            aria-label="Action">
                            <i class="pi pi-times" />
                        </button>
                    </slot>
                </div>
            </template>

            <!-- Message vide personnalisable -->
            <slot v-else name="empty-state">
                <div class="p-4 text-sm text-center text-gray-400">
                    {{ emptyMessage }}
                </div>
            </slot>
        </template>
    </div>
</template>


<script setup lang="ts">
import type { PropType } from 'vue'

const props = defineProps({
    isLoading: {
        type: Boolean,
        default: false
    },
    items: {
        type: Array as PropType<any[]>,
        required: true
    },
    itemKey: {
        type: String,
        default: 'id'
    },
    showDeleteButton: {
        type: Boolean,
        default: false
    },
    emptyMessage: {
        type: String,
        default: 'Aucun élément à afficher'
    },
    activeId: {
        type: [String, Number],
        default: null
    }
})

const emit = defineEmits<{
    (e: 'item-click', item: any): void
    (e: 'item-action', item: any): void
}>()
</script>
