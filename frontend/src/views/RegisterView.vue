<template>
    <PublicNavbar />

    <div class="relative flex items-center justify-center min-h-screen px-4 py-24 text-white bg-roomies-gray5">
        <!-- Formulaire -->
        <form @submit.prevent="handleRegister"
            class="z-10 w-full max-w-md p-6 space-y-6 shadow-lg bg-roomies-gray3 rounded-xl"
            enctype="multipart/form-data">
            <h1 class="text-2xl font-bold text-center text-white">Créer un compte Roomies</h1>

            <!-- Email -->
            <BaseInput label="Email" name="email" type="email" v-model="email" :error="emailError"
                @blur="() => validateEmail(true)" @input="validateEmail()" />

            <!-- Pseudo -->
            <BaseInput label="Pseudo" name="pseudo" type="text" minlength="2" maxlength="20"
                pattern="^[a-zA-Z0-9_]{2,20}$" autocomplete="off" v-model="pseudo" :error="pseudoError"
                @blur="() => validatePseudo(true)" @input="validatePseudo()" />

            <!-- Avatar -->
            <BaseFileInput label="Avatar" name="avatar" @change="handleAvatar" :error="avatarError" />

            <!-- Champ mot de passe -->
            <BaseInput label="Mot de passe" name="password" :type="showPassword ? 'text' : 'password'" minlength="16"
                v-model="password" :error="passwordError" @input="validatePasswords()"
                @blur="() => validatePasswords(true)" @icon-click="showPassword = !showPassword">
                <template #right-icon>
                    <i :class="['pi text-gray-400 hover:text-white', showPassword ? 'pi-eye-slash' : 'pi-eye']" />
                </template>
            </BaseInput>



            <!-- Conditions -->
            <ul class="space-y-1 text-sm">
                <li :class="conditionClass(passwordConditionsValid().length)">• Au moins 16 caractères</li>
                <li :class="conditionClass(passwordConditionsValid().lowercase)">• Une minuscule</li>
                <li :class="conditionClass(passwordConditionsValid().uppercase)">• Une majuscule</li>
                <li :class="conditionClass(passwordConditionsValid().digit)">• Un chiffre</li>
                <li :class="conditionClass(passwordConditionsValid().special)">• Un caractère spécial</li>
            </ul>

            <!-- Champ confirmation -->
            <BaseInput label="Confirmer le mot de passe" name="confirmPassword"
                :type="showPassword ? 'text' : 'password'" minlength="16" autocomplete="new-password" required
                v-model="confirmPassword" :error="confirmError" @input="validatePasswords()"
                @blur="() => validatePasswords(true)" />

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

        <!-- Illustration principale en fond -->
        <img src="@/assets/images/hero-illustration-form.svg"
            class="absolute inset-0 z-0 object-cover w-full h-full pointer-events-none" alt="" />

        <!-- Décorations latérales -->
        <img src="@/assets/images/hero-illustration-left-2.svg"
            class="absolute bottom-0 left-0 z-0 w-1/3 max-w-xs pointer-events-none" alt="" />
        <img src="@/assets/images/hero-illustration-right-2.svg"
            class="absolute bottom-0 right-0 z-0 w-1/3 max-w-xs pointer-events-none" alt="" />
    </div>

    <AppFooter />
</template>


<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseFileInput from '@/components/base/BaseFileInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import { useAuthStore } from '@/stores/authStore'
import PublicNavbar from '@/components/layout/PublicNavbar.vue'
import AppFooter from '@/components/layout/AppFooter.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()
const email = ref('')
const emailError = ref('')
const password = ref('')
const passwordError = ref('')
const confirmPassword = ref('')
const confirmError = ref('')
const pseudo = ref('')
const pseudoError = ref('')
const avatar = ref<File | null>(null)
const avatarError = ref('')
const showPassword = ref(false)
const success = ref(false)

const auth = useAuthStore()
const router = useRouter()

function conditionClass(condition: boolean) {
    return condition ? 'text-green-400' : 'text-gray-400'
}


/**
 * Gère l'inscription d'un nouvel utilisateur.
 * 
 * Étapes :
 * 1. Crée un objet FormData et y ajoute l'email, le mot de passe, le pseudo et l'avatar (si fourni).
 * 2. Valide les champs pseudo, email et mot de passe.
 * 3. Si une erreur de validation est détectée, l'inscription est interrompue.
 * 4. Envoie les données au service d'authentification pour enregistrer l'utilisateur.
 * 5. Si l'inscription réussit, met à jour l'état de succès.
 */
async function handleRegister() {
    const formData = new FormData()
    formData.append('email', email.value)
    formData.append('password', password.value)
    formData.append('pseudo', pseudo.value)
    if (avatar.value) {
        formData.append('avatar', avatar.value)
    }

    // Réinitialiser les erreurs
    emailError.value = ''
    pseudoError.value = ''
    passwordError.value = ''
    confirmError.value = ''
    avatarError.value = ''

    // Réinitialiser les erreurs du store
    auth.resetError();

    // Valider côté frontend
    validatePseudo()
    validateEmail()
    validatePasswords()
    handleAvatar({ target: { files: [avatar.value] } } as unknown as Event)

    if (pseudoError.value || emailError.value || passwordError.value || confirmError.value || avatarError.value) {
        return
    }

    try {
        await auth.registerUser(formData)

        toast.success('🎉 Votre compte a été créé ! Vérifiez vos emails pour confirmer votre inscription.')
        success.value = true

        setTimeout(() => {
            router.push('/login')
        }, 5000)
    } catch (err: any) {
        // 💡 Parser automatiquement les violations API Platform
        const violations = err.response?.data?.violations
        if (Array.isArray(violations)) {
            for (const violation of violations) {
                switch (violation.propertyPath) {
                    case 'email':
                        emailError.value = violation.message
                        break
                    case 'pseudo':
                        pseudoError.value = violation.message
                        break
                    case 'password':
                        passwordError.value = violation.message
                        break
                    case 'avatar':
                        avatarError.value = violation.message
                        break
                }
            }
        }

        // Fallback général
        if (!violations?.length) {
            toast.error(auth.error)
        }
    }
}


/**
 * Valide le format de l'email
 */
function validateEmail(blurred = false) {
    const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)

    if (!isValid && blurred) {
        emailError.value = 'L’adresse email n’est pas valide.'
    } else if (isValid) {
        emailError.value = ''
    }
}

/**
 * Valide le pseudo
 */
function validatePseudo(blurred = false) {
    const isValid = /^[a-zA-Z0-9_]{2,20}$/.test(pseudo.value)

    if (!isValid && blurred) {
        pseudoError.value = 'Le pseudo doit contenir entre 2 et 20 caractères valides (lettres, chiffres, underscore).'
    } else if (isValid) {
        pseudoError.value = ''
    }
}

/**
 * Valide les règles de sécurité (pour liste des critères)
 */
function passwordConditionsValid() {
    return {
        length: password.value.length >= 16,
        lowercase: /[a-z]/.test(password.value),
        uppercase: /[A-Z]/.test(password.value),
        digit: /\d/.test(password.value),
        special: /[\W_]/.test(password.value)
    }
}

/**
 * Gère l'affichage des messages d'erreur uniquement si le champ a été touché
 */
function validatePasswords(blurred = false) {
    const conditions = passwordConditionsValid()
    const confirmValid = confirmPassword.value === password.value

    // Mot de passe
    if (blurred) {
        passwordError.value = conditions.length
            ? ''
            : 'Le mot de passe doit faire au moins 16 caractères.'
    } else if (conditions.length) {
        passwordError.value = ''
    }

    // Confirmation du mot de passe
    if (blurred) {
        if (!confirmPassword.value) {
            confirmError.value = 'Veuillez confirmer votre mot de passe.'
        } else if (!confirmValid) {
            confirmError.value = 'Les mots de passe ne correspondent pas.'
        } else {
            confirmError.value = ''
        }
    } else if (confirmValid) {
        confirmError.value = ''
    }
}

/**
 * Gère la sélection d'un fichier avatar par l'utilisateur.
 * Cette fonction est appelée lorsqu'un utilisateur choisit une image à utiliser comme avatar
 * lors de l'inscription. Elle peut inclure la validation du type et de la taille du fichier,
 * ainsi que la mise à jour de l'aperçu de l'avatar dans l'interface utilisateur.
 */
function handleAvatar(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0]
    avatar.value = file || null

    if (!file) {
        avatarError.value = ''
        return
    }

    const validTypes = ['image/jpeg', 'image/png', 'image/webp']
    if (!validTypes.includes(file.type)) {
        avatarError.value = 'Format invalide (JPEG, PNG, WEBP uniquement).'
    } else if (file.size > 5 * 1024 * 1024) {
        avatarError.value = 'L’image ne doit pas dépasser 5 Mo.'
    } else {
        avatarError.value = ''
    }
}

</script>
