<!-- components/form/RegisterForm.vue -->
<template>
    <form @submit.prevent="handleRegister"
        class="z-10 w-full max-w-md p-6 space-y-6 shadow-lg bg-roomies-gray3 rounded-xl" enctype="multipart/form-data">

        <h1 class="text-2xl font-bold text-center text-white">Créer un compte Roomies</h1>

        <!-- Email -->
        <EmailInput label="Email" name="email" v-model="email" autocomplete="email" required :error="emailError"
            @valid="(v) => { isEmailValid = v; emailError = v ? '' : 'L’adresse email n’est pas valide.' }"
            @error="(msg) => emailError = msg" />

        <!-- Pseudo -->
        <PseudoInput label="Pseudo" name="pseudo" required v-model="pseudo" :error="pseudoError"
            @valid="(v) => { isPseudoValid = v; pseudoError = v ? '' : 'Le pseudo est invalide.' }"
            @error="(msg) => pseudoError = msg" />

        <!-- Avatar -->
        <AvatarInput label="Avatar" name="avatar" required :error="avatarError" @update:file="(file) => avatar = file"
            @valid="(v) => { isAvatarValid = v; avatarError = v ? '' : 'Fichier non valide.' }" />

        <!-- Mot de passe + confirmation + conditions -->
        <PasswordInput label="Mot de passe" name="password" v-model="password" :error="passwordError"
            :show-conditions="true" :show-confirmation="true"
            @valid="(v) => { isPasswordValid = v; passwordError = v ? '' : 'Le mot de passe est invalide ou non confirmé.' }" />

        <!-- Bouton -->
        <BaseButton type="submit" :loading="auth.loading" class="w-full">Créer un compte</BaseButton>


        <!-- Redirection -->
        <p class="text-sm text-center text-white">
            Déjà inscrit ?
            <RouterLink to="/login" class="font-medium text-white underline hover:text-roomies-blue">
                Se connecter
            </RouterLink>
        </p>
    </form>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { useAuthStore } from '@/stores/authStore'

import EmailInput from '@/components/form/inputs/EmailInput.vue'
import PseudoInput from '@/components/form/inputs/PseudoInput.vue'
import AvatarInput from '@/components/form/inputs/AvatarInput.vue'
import PasswordInput from '@/components/form/inputs/PasswordInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'

// Initialisation des outils et stores
const toast = useToast()
const router = useRouter()
const auth = useAuthStore()

// Champs du formulaire (et messages d’erreur associés)
const email = ref('')
const emailError = ref('')
const pseudo = ref('')
const pseudoError = ref('')
const avatar = ref<File | null>(null)
const avatarError = ref('')
const password = ref('')
const passwordError = ref('')

// Indicateurs de validation individuelle
const isEmailValid = ref(false)
const isPseudoValid = ref(false)
const isAvatarValid = ref(true) // avatar est optionnel selon les règles back, sinon mettre à false
const isPasswordValid = ref(false)

// Reset de l’erreur globale lors de l’affichage du composant
auth.resetError()

/**
 * Gère la soumission du formulaire d’inscription.
 * - Valide tous les champs (prérequis de validation des inputs enfants)
 * - Construit le FormData pour un upload multipart (avatar)
 * - Appelle le store d’auth pour la requête d’inscription
 * - Gère les erreurs backend en les dispatchant sur les bons champs
 * - Affiche des toasts en cas de succès ou d’échec général
 */
async function handleRegister() {
    // Réinitialise les messages d’erreur avant validation/soumission
    emailError.value = ''
    pseudoError.value = ''
    avatarError.value = ''
    passwordError.value = ''

    // Vérifie la validité de tous les champs
    if (!isEmailValid.value || !isPseudoValid.value || !isAvatarValid.value || !isPasswordValid.value) {
        toast.error('Merci de remplir tous les champs correctement.')
        return
    }

    // Prépare le payload FormData pour upload (avatar = fichier)
    const formData = new FormData()
    formData.append('email', email.value)
    formData.append('pseudo', pseudo.value)
    formData.append('password', password.value)
    if (avatar.value) formData.append('avatar', avatar.value)

    try {
        await auth.registerUser(formData)
        toast.success('Votre compte a été créé ! Vérifiez vos emails pour confirmer votre inscription.')
        router.push('/login')
    } catch (err: any) {
        // Récupère les violations de validation renvoyées par l’API Platform
        const violations = err.response?.data?.violations
        if (Array.isArray(violations)) {
            for (const v of violations) {
                switch (v.propertyPath) {
                    case 'email':
                        emailError.value = v.message
                        break
                    case 'pseudo':
                        pseudoError.value = v.message
                        break
                    case 'password':
                        passwordError.value = v.message
                        break
                    case 'avatar':
                        avatarError.value = v.message
                        break
                }
            }
        } else {
            toast.error(auth.error || 'Une erreur est survenue.')
        }
    }
}
</script>
