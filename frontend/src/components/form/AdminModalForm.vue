<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center px-3 bg-black bg-opacity-60" @click.self="close">
        <div class="w-full max-w-lg p-6 text-white shadow-xl bg-roomies-gray4 rounded-xl">
            <h2 class="mb-4 text-xl font-semibold">Paramètres du serveur</h2>

            <!-- Nom du groupe -->
            <BaseInput name="serverName" label="Nom du serveur" v-model="editedName" autocomplete="off"
                placeholder="Ex: Mon serveur" :error="errors.name" @valid="handleNameValidation" />

            <!-- Recherche utilisateur -->
            <div class="mt-2 mb-4">
                <BaseInput name="searchMembers" label="Rechercher un membre" v-model="search"
                    placeholder="Rechercher un membre par son pseudo..." type="text" icon="search">
                </BaseInput>
            </div>

            <!-- Liste des membres + rôles -->
            <div class="space-y-2 overflow-y-auto max-h-64">
                <p class="text-sm text-gray-300">Sélectionnez un rôle pour chaque membre (Admin ou Utilisateur)</p>
                <template v-if="filteredMembers.length > 0">
                    <div v-for="member in filteredMembers" :key="member.friendCode"
                        class="flex items-center justify-between p-2 rounded bg-roomies-gray2">
                        <div class="flex items-center space-x-2">
                            <img :src="member.avatar" alt="avatar" class="w-6 h-6 rounded-full" />
                            <span>{{ member.pseudo }}</span>
                        </div>
                        <select :id="`role-select-${member.friendCode}`" :name="`role-select-${member.friendCode}`"
                            v-model="editedRoles[member.friendCode]"
                            class="px-2 py-1 text-white rounded bg-roomies-gray3">
                            <option value="user">Utilisateur</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </template>
                <div v-else class="p-4 text-center text-white">
                    <p>Aucun membre trouvé 🧙‍♂️</p>
                    <p class="mt-1 text-xs">Essayez avec un autre pseudo</p>
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex justify-end mt-6 space-x-3">
                <BaseButton variant="secondary" @click="close">Fermer</BaseButton>
                <BaseButton variant="primary" @click="submit" :disabled="!isFormValid">
                    Enregistrer les modifications
                </BaseButton>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watchEffect, watch } from 'vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import type { RoomMember } from '@/models/RoomMember'
import { useAuthStore } from '@/stores/authStore'

const props = defineProps<{
    name: string
    members: RoomMember[]
}>()

const emit = defineEmits(['close', 'submit'])

const editedName = ref(props.name)
const search = ref('')
const editedRoles = ref<Record<string, string>>({})
const errors = ref({
    name: ''
})

const authStore = useAuthStore()
const myFriendCode = authStore.user?.friendCode || ''

// Validation en temps réel du nom
watch(() => editedName.value, (newName) => {
    if (newName.length >= 3 && newName.length <= 30) {
        errors.value.name = ''
    }
})

watchEffect(() => {
    editedRoles.value = props.members.reduce((acc, m) => {
        acc[m.member.friendCode] = m.role
        return acc
    }, {} as Record<string, string>)
})

const filteredMembers = computed(() => {
    return props.members
        .filter(m => m.member.friendCode !== myFriendCode &&
            m.member.pseudo.toLowerCase().includes(search.value.toLowerCase()))
        .map(m => ({
            pseudo: m.member.pseudo,
            friendCode: m.member.friendCode,
            avatar: m.member.avatar,
            role: m.role
        }))
})

const isFormValid = computed(() => {
    return editedName.value.length >= 3 &&
        editedName.value.length <= 30 &&
        errors.value.name === ''
})

function handleNameValidation(isValid: boolean) {
    if (isValid) {
        errors.value.name = ''
    } else if (editedName.value.length > 0) {
        errors.value.name = 'Le nom doit contenir entre 3 et 30 caractères.'
    }
}

function validateForm(): boolean {
    errors.value = { name: '' }
    let isValid = true

    if (editedName.value.length < 3 || editedName.value.length > 30) {
        errors.value.name = 'Le nom doit contenir entre 3 et 30 caractères.'
        isValid = false
    }

    return isValid
}

function submit() {
    if (!validateForm()) return

    emit('submit', {
        name: editedName.value.trim(),
        roles: Object.entries(editedRoles.value)
            .filter(([friendCode]) => friendCode !== myFriendCode)
            .map(([friendCode, role]) => ({
                friendCode,
                role
            }))
    })
}

function close() {
    emit('close')
}
</script>