<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center px-3 bg-black bg-opacity-60">
        <div class="flex flex-col w-full max-w-md max-h-[90vh] bg-roomies-gray4 rounded-xl shadow-lg">
            <div class="flex items-center justify-between p-6 border-b border-roomies-gray2">
                <h2 class="text-xl font-semibold">Modifier mon profil</h2>
                <BaseButton variant="secondary" iconLeft noIconSpace class="!p-1.5 !w-9 !h-9 rounded-full"
                    @click="$emit('close')">
                    <template #icon-left>
                        <i class="pi pi-times" />
                    </template>
                </BaseButton>
            </div>

            <div class="flex-1 p-6 overflow-y-auto scrollbar">
                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <!-- Pseudo -->
                    <PseudoInput label="Pseudo" name="pseudo" :model-value="pseudo" :error="pseudoError"
                        @update:modelValue="val => pseudo = val" @valid="v => isPseudoValid = v"
                        @error="msg => pseudoError = msg" />

                    <!-- Avatar -->
                    <AvatarInput label="Avatar" name="avatar" :error="avatarError" @update:file="f => avatar = f"
                        @valid="v => isAvatarValid = v" />

                    <!-- Section mot de passe (optionnelle) -->
                    <div class="pt-4 border-t border-roomies-gray2">
                        <h3 class="mb-3 text-sm font-semibold text-gray-300 uppercase">
                            Changer le mot de passe (optionnel)
                        </h3>

                        <!-- Mot de passe actuel -->
                        <PasswordInput label="Mot de passe actuel" name="currentPassword" :model-value="currentPassword"
                            :error="currentPasswordError" :show-conditions="false" :show-confirmation="false"
                            @update:modelValue="val => {
                                currentPassword = val
                                if (!val) currentPasswordError = ''
                            }" @valid="v => isCurrentPasswordValid = v" />

                        <!-- Nouveau mot de passe -->
                        <PasswordInput label="Nouveau mot de passe" name="newPassword" :model-value="newPassword"
                            :error="newPasswordError" :show-conditions="true" :show-confirmation="true"
                            @update:modelValue="val => {
                                newPassword = val
                                if (!val) newPasswordError = ''
                            }" @valid="v => isNewPasswordValid = v" />

                    </div>
                </form>
            </div>

            <div class="p-4 border-t border-roomies-gray2">
                <div class="flex gap-3">
                    <BaseButton variant="danger" type="button" class="flex-1" @click="$emit('close')">
                        Annuler
                    </BaseButton>
                    <BaseButton type="button" class="flex-1" :loading="loading" @click="handleSubmit">
                        Enregistrer
                    </BaseButton>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useToast } from 'vue-toastification'
import { useAuthStore } from '@/stores/authStore'

import PseudoInput from '@/components/form/inputs/PseudoInput.vue'
import AvatarInput from '@/components/form/inputs/AvatarInput.vue'
import PasswordInput from '@/components/form/inputs/PasswordInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'

const props = defineProps<{
    user: { pseudo: string } // Ajoute d'autres champs selon tes besoins
}>()

// Champs du formulaire
const pseudo = ref(props.user.pseudo ?? '')
const avatar = ref<File | null>(null)
const currentPassword = ref('')
const newPassword = ref('')

// Erreurs et validité
const pseudoError = ref('')
const avatarError = ref('')
const currentPasswordError = ref('')
const newPasswordError = ref('')

const isPseudoValid = ref(true)
const isAvatarValid = ref(true)
const isCurrentPasswordValid = ref(true)
const isNewPasswordValid = ref(true)

const loading = ref(false)
const toast = useToast()
const userStore = useAuthStore()
const emit = defineEmits(['close'])
function resetErrors() {
    pseudoError.value = ''
    avatarError.value = ''
    currentPasswordError.value = ''
    newPasswordError.value = ''
}

function isPseudoChanged() {
    return pseudo.value && pseudo.value !== props.user.pseudo
}
function isPasswordChanged() {
    return !!newPassword.value
}
function isAvatarChanged() {
    return !!avatar.value
}

// Gestion de la soumission
async function handleSubmit() {
    resetErrors()

    const pseudoChanged = isPseudoChanged()
    const passwordChanged = isPasswordChanged()
    const avatarChanged = isAvatarChanged()

    // Aucun changement détecté
    if (!pseudoChanged && !passwordChanged && !avatarChanged) {
        toast.info('Aucune modification détectée.')
        return
    }

    // Mot de passe actuel requis UNIQUEMENT si on veut changer le mot de passe
    if (passwordChanged && !currentPassword.value) {
        currentPasswordError.value = 'Veuillez saisir votre mot de passe actuel.'
        toast.error('Merci d’entrer votre mot de passe actuel pour modifier votre mot de passe.')
        return
    }

    // Vérification front
    if (
        !isPseudoValid.value ||
        !isAvatarValid.value ||
        !isCurrentPasswordValid.value ||
        !isNewPasswordValid.value
    ) {
        toast.error('Merci de remplir tous les champs correctement.')
        return
    }

    loading.value = true
    const formData = new FormData()
    if (pseudoChanged) formData.append('pseudo', pseudo.value)
    if (avatarChanged && avatar.value) formData.append('avatar', avatar.value)
    if (passwordChanged) {
        formData.append('currentPassword', currentPassword.value)
        formData.append('newPassword', newPassword.value)
    }

    try {
        await userStore.updateProfile(formData)
        toast.success('Profil mis à jour !')
        loading.value = false
        emit('close')
    } catch (err: any) {
        loading.value = false
        // gestion des violations symfony
        const violations = err.response?.data?.violations
        if (Array.isArray(violations)) {
            for (const v of violations) {
                switch (v.propertyPath) {
                    case 'pseudo':
                        pseudoError.value = v.message
                        break
                    case 'currentPassword':
                        currentPasswordError.value = v.message
                        break
                    case 'newPassword':
                        newPasswordError.value = v.message
                        break
                    case 'avatar':
                        avatarError.value = v.message
                        break
                }
            }
        } else {
            toast.error(err.response?.data?.detail || 'Erreur lors de la mise à jour.')
        }
    }
}
</script>
