<template>
    <div class="flex items-center px-4 py-2 rounded-t-lg bg-roomies-gray4">
        <div class="flex flex-col">
            <span class="text-lg font-semibold text-white">
                {{ room.name }}
            </span>
            <span class="text-xs text-gray-400">
                {{ room.members.length }} membres
            </span>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { Room } from '@/models/Room'

/**
 * Props :
 * - room : objet Room, représente la room de groupe courante
 *   (contient au moins : name, members[])
 */
defineProps<{
    room: Room
}>()
</script>
