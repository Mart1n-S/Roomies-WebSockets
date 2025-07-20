<template>
    <div class="flex items-center justify-between px-4 py-2 rounded-t-lg bg-roomies-gray4">
        <button class="flex items-center space-x-3 group" @click="$emit('open-friend-modal')">
            <img :src="otherMember.member.avatar" alt="Avatar" class="w-10 h-10 rounded-full" />
            <div class="flex flex-col text-left">
                <span class="font-semibold text-white group-hover:underline">
                    {{ otherMember.member.pseudo }}
                </span>
                <span class="text-xs" :class="otherMember.member.isOnline ? 'text-green-500' : 'text-gray-400'">
                    {{ otherMember.member.isOnline ? 'En ligne' : 'Hors ligne' }}
                </span>
            </div>
        </button>
    </div>
</template>

<script setup lang="ts">
import type { RoomMember } from '@/models/RoomMember'

/**
 * Props :
 * - otherMember : objet RoomMember, contient les infos du correspondant (avatar, pseudo, statut en ligne…)
 */
defineProps<{
    otherMember: RoomMember
}>()

/**
 * Événement custom émis lors du clic sur l’avatar/pseudo :
 * - 'open-friend-modal' : permet d’ouvrir un modal d’info ou d’action sur le membre
 */
defineEmits<{
    (e: 'open-friend-modal'): void
}>()
</script>
