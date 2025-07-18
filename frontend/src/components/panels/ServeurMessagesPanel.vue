<template>
    <div class="flex flex-col h-full p-3 space-y-3 text-white">
        <p class="my-4 text-lg font-bold"><i class="pi pi-users" /> Membres du groupe</p>

        <BaseInput name="search" label="Rechercher un membre" v-model="search" autocomplete="off"
            placeholder="Tapez un pseudo..." type="text" />

        <div class="mb-1 text-sm text-gray-400">
            {{ filteredMembers.length }} membre{{ filteredMembers.length > 1 ? 's' : '' }} dans le groupe
        </div>

        <BaseButton @click="showAddMemberModal = true" size="sm" variant="primary" class="w-full">
            <template #icon-left>
                <i class="pi pi-user-plus" />
            </template>
            Ajouter un membre
        </BaseButton>

        <ItemList :items="filteredMembers" itemKey="friendCode"
            :empty-message="search.length ? 'Aucun résultat...' : 'Aucun membre dans ce groupe.'">
            <template #item-content="{ item: user }">
                <div class="flex items-center px-2 py-1 space-x-2 transition rounded-lg cursor-pointer hover:bg-roomies-gray2/60"
                    @click="openUserProfile(user.friendCode)">
                    <div class="relative flex-shrink-0 w-8 h-8">
                        <img :src="user.avatar" class="w-8 h-8 rounded-full" :alt="user.pseudo" />
                        <span class="absolute bottom-0 right-0 w-3 h-3 border-2 rounded-full border-roomies-gray1"
                            :class="user.isOnline ? 'bg-green-500' : 'bg-gray-500'" title="Statut" />
                    </div>
                    <div class="flex flex-col min-w-0">
                        <!-- Pseudo, coupe après 2 lignes si besoin -->
                        <span class="font-medium text-sm break-all leading-snug max-w-[110px] truncate"
                            :class="{ 'whitespace-normal': user.pseudo.length > 10, 'whitespace-nowrap': user.pseudo.length <= 10 }">{{
                                user.pseudo }}</span>
                        <!-- Badge en-dessous du pseudo -->
                        <template v-if="user.role === 'owner'">
                            <span
                                class="flex items-center px-2 py-0.5 mt-0.5 text-xs rounded bg-yellow-500/20 text-yellow-400 font-semibold gap-1 w-fit">
                                <i class="text-sm pi pi-crown"></i>
                                Owner
                            </span>
                        </template>
                        <template v-else-if="user.role === 'admin'">
                            <span
                                class="flex items-center px-2 py-0.5 mt-0.5 text-xs rounded bg-blue-500/20 text-blue-300 font-semibold gap-1 w-fit">
                                <i class="text-sm pi pi-shield"></i>
                                Admin
                            </span>
                        </template>
                    </div>
                </div>
            </template>
        </ItemList>

        <div class="flex flex-col space-y-2">
            <BaseButton v-if="isOwner" @click="showAdminModal = true" variant="secondary" class="w-full">
                <template #icon-left>
                    <i class="pi pi-cog" />
                </template>
                Paramètres
            </BaseButton>
            <BaseButton variant="danger" class="mt-auto" @click="showConfirmModal = true">
                <template #icon-left>
                    <i :class="isOwner ? 'pi pi-trash' : 'pi pi-sign-out'" />
                </template>
                {{ isOwner ? 'Supprimer le serveur' : 'Quitter le serveur' }}
            </BaseButton>
        </div>

        <ConfirmDeleteModal v-if="showConfirmModal" :title="isOwner ? 'Supprimer le serveur' : 'Quitter le serveur'"
            :message="isOwner
                ? 'Supprimer ce serveur ? Cette action est irréversible et supprimera toutes les données du groupe.'
                : 'Es-tu sûr de vouloir quitter ce serveur ? Tu perdras l’accès au chat et à son historique.'"
            :onConfirm="handleLeaveOrDeleteServer" @close="showConfirmModal = false" />

        <GroupInviteModal v-if="showAddMemberModal" :room-id="roomId" @close="showAddMemberModal = false" />

        <AdminModalForm v-if="room && showAdminModal" :name="room.name" :members="room.members"
            @submit="handleSubmitAdminSettings" @close="showAdminModal = false" />

        <!-- MODAL PROFIL UTILISATEUR -->
        <ProfilPublicModalForm v-if="showUserProfileModal && selectedUser" :user="selectedUser"
            :currentUserRole="myRole" :memberRole="selectedUserRole ?? undefined" @add-friend="handleAddFriend"
            @kick="openConfirmKickModal" @close="closeUserProfile" />

        <!-- MODAL DE CONFIRMATION SUPPRESSION MEMBRE -->
        <ConfirmDeleteModal v-if="showConfirmKickModal && selectedUser" title="Retirer du serveur"
            :message="`Retirer ${selectedUser.pseudo} du serveur ? Il/elle perdra l'accès au chat et à l'historique.`"
            :onConfirm="handleKickMember" @close="closeConfirmKickModal" />
    </div>
</template>


<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'

import { useAuthStore } from '@/stores/authStore'
import { useRoomStore } from '@/stores/roomStore'
import { useUserStatusStore } from '@/stores/userStatusStore'
import { useWebSocketStore } from '@/stores/wsStore'

import type { UserPublic } from '@/models/User'

import BaseInput from '@/components/base/BaseInput.vue'
import ItemList from '@/components/UI/ItemList.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import GroupInviteModal from '@/components/UI/GroupInviteModal.vue'
import ConfirmDeleteModal from '@/components/base/ConfirmDeleteModal.vue'
import AdminModalForm from '@/components/form/AdminModalForm.vue'
import ProfilPublicModalForm from '@/components/form/ProfilPublicModalForm.vue'

const route = useRoute()
const router = useRouter()

const authStore = useAuthStore()
const roomStore = useRoomStore()
const userStatusStore = useUserStatusStore()
const wsStore = useWebSocketStore()

const search = ref('')
const showAddMemberModal = ref(false)
const showConfirmModal = ref(false)
const showAdminModal = ref(false)

// Etats modales membres
const showUserProfileModal = ref(false)
const showConfirmKickModal = ref(false)
const selectedUser = ref<UserPublic | null>(null)
const selectedUserRole = ref<'user' | 'admin' | 'owner' | null>(null)

const roomId = computed(() => route.params.roomId as string)
const room = computed(() => roomStore.allRooms.find(r => r.id === roomId.value))

const isOwner = computed(() => {
    const currentUserCode = authStore.user?.friendCode
    const myMembership = room.value?.members.find(m => m.member.friendCode === currentUserCode)
    return myMembership?.role === 'owner'
})

const myRole = computed(() => {
    const currentUserCode = authStore.user?.friendCode
    const myMembership = room.value?.members.find(m => m.member.friendCode === currentUserCode)
    return myMembership?.role ?? 'user'
})

const filteredMembers = computed(() => {
    const members = room.value?.members || []
    return members
        .map(m => ({
            pseudo: m.member.pseudo,
            avatar: m.member.avatar,
            friendCode: m.member.friendCode,
            isOnline: userStatusStore.isOnline(m.member.friendCode),
            role: m.role
        }))
        .filter(user =>
            user.pseudo.toLowerCase().includes(search.value.toLowerCase())
        )
})

// Ouvre le UserProfileModal avec les infos du user cliqué
function openUserProfile(friendCode: string) {
    const fullMember = room.value?.members.find(m => m.member.friendCode === friendCode)
    if (fullMember) {
        selectedUser.value = fullMember.member
        selectedUserRole.value = fullMember.role
        showUserProfileModal.value = true
    }
}

function closeUserProfile() {
    showUserProfileModal.value = false
    // On ne reset pas selectedUser si la modal de confirmation est encore ouverte
    if (!showConfirmKickModal.value) {
        selectedUser.value = null
        selectedUserRole.value = null
    }
}

// Ouvre la modal de confirmation sans fermer la modal profil
function openConfirmKickModal() {
    showConfirmKickModal.value = true
}

function closeConfirmKickModal() {
    showConfirmKickModal.value = false
    // Si la modal profil est fermée, on nettoie aussi selectedUser
    if (!showUserProfileModal.value) {
        selectedUser.value = null
        selectedUserRole.value = null
    }
}

// Handler suppression membre
async function handleKickMember() {
    if (!selectedUser.value) return
    try {
        await roomStore.kickMember(roomId.value, selectedUser.value.friendCode)
        // Ferme les deux modales après succès
        showConfirmKickModal.value = false
        showUserProfileModal.value = false
        selectedUser.value = null
        selectedUserRole.value = null
    } catch (error) {
        console.error("Erreur lors de la suppression du membre:", error)
    }
}

function handleAddFriend(friendCode: string) {
    wsStore.send({
        type: 'friend_request',
        payload: {
            friendCode
        }
    })
    // On peut fermer la modal profil après l'envoi de la demande
    showUserProfileModal.value = false
    selectedUser.value = null
    selectedUserRole.value = null
}

async function handleLeaveOrDeleteServer() {
    if (isOwner.value) {
        await roomStore.deleteGroup(roomId.value)
    } else {
        await roomStore.leaveGroup(roomId.value)
    }
    router.push('/dashboard')
}

async function handleSubmitAdminSettings(data: { name: string; roles: { friendCode: string; role: string }[] }) {
    await roomStore.updateGroupSettings(roomId.value, data)
    showAdminModal.value = false
}
</script>
