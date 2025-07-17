<template>
    <div class="flex flex-col items-center gap-2 p-4 shadow bg-roomies-gray3 rounded-xl">
        <img src="@/assets/images/morpion.svg" alt="Jeu" class="w-40 h-40 rounded-lg" />

        <h3 class="text-lg font-bold text-white">{{ room.name }}</h3>
        <p class="text-sm text-blue-400">{{ gameLabel }}</p>

        <div class="flex items-center gap-2 mt-2">
            <img :src="room.creator.avatar" class="rounded-full w-7 h-7" />
            <span class="text-sm text-gray-300">{{ room.creator.pseudo }}</span>
        </div>

        <!-- Nombre de joueurs avec couleur dynamique -->
        <div class="mt-2 text-xs font-semibold" :class="room.playersCount >= 2 ? 'text-red-400' : 'text-green-400'">
            <i class="mr-1 pi pi-users" />
            {{ room.playersCount }} / 2
        </div>

        <!-- Nombre de spectateurs en violet -->
        <div class="mt-1 text-xs font-semibold text-purple-400">
            <i class="mr-1 pi pi-eye" />
            {{ room.viewersCount }} spectateur{{ room.viewersCount > 1 ? 's' : '' }}
        </div>

        <BaseButton class="w-full mt-2" :disabled="room.playersCount >= 2" @click="$emit('join', room)">
            Rejoindre
        </BaseButton>

        <BaseButton class="w-full mt-2" variant="secondary" @click="$emit('watch', room)">
            Regarder en spectateur
        </BaseButton>
    </div>
</template>

<script setup lang="ts">
import type { RoomCard } from '@/models/RoomCard'
import BaseButton from '@/components/base/BaseButton.vue'
import { computed } from 'vue'

const props = defineProps<{ room: RoomCard }>()

const gameLabel = computed(() =>
    props.room.game === 'morpion' ? 'Morpion' : props.room.game
)
</script>
