<template>
    <div class="space-y-4">
        <!-- Mot de passe -->
        <BaseInput :type="showPassword ? 'text' : 'password'" :label="label" :name="name" :placeholder="placeholder"
            :error="passwordError" :model-value="modelValue" @update:modelValue="handleInput"
            @blur="() => handleBlur(modelValue)" @icon-click="togglePassword">
            <template #right-icon>
                <i :class="['pi text-gray-400 hover:text-white', showPassword ? 'pi-eye-slash' : 'pi-eye']" />
            </template>
        </BaseInput>

        <!-- Conditions -->
        <ul v-if="showConditions" class="space-y-1 text-sm">
            <li :class="conditionClass(conditions.length)">• Au moins 16 caractères</li>
            <li :class="conditionClass(conditions.lowercase)">• Une minuscule</li>
            <li :class="conditionClass(conditions.uppercase)">• Une majuscule</li>
            <li :class="conditionClass(conditions.digit)">• Un chiffre</li>
            <li :class="conditionClass(conditions.special)">• Un caractère spécial</li>
        </ul>

        <!-- Confirmation -->
        <BaseInput v-if="showConfirmation" :type="showPassword ? 'text' : 'password'"
            :label="confirmLabel || 'Confirmer le mot de passe'" name="confirmPassword" :model-value="confirmPassword"
            :error="confirmError" @update:modelValue="val => { confirmPassword = val; validateConfirmPassword(val) }"
            @blur="() => validateConfirmPassword(confirmPassword, true)" />

    </div>
</template>
<script setup lang="ts">
import { ref, computed } from 'vue'
import BaseInput from '@/components/base/BaseInput.vue'

const props = defineProps<{
    label?: string
    name: string
    placeholder?: string
    modelValue: string
    showConditions?: boolean
    showConfirmation?: boolean
    confirmLabel?: string
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void
    (e: 'valid', isValid: boolean): void
}>()

const showPassword = ref(false)
const confirmPassword = ref('')
const passwordError = ref('')
const confirmError = ref('')

// Expose pour usage externe
defineExpose({
    confirmPassword,
    validateConfirmPassword: (value: string, blurred = false) => validateConfirmPassword(value, blurred)
})

function togglePassword() {
    showPassword.value = !showPassword.value
}

function passwordConditionsValid(value: string) {
    return {
        length: value.length >= 16,
        lowercase: /[a-z]/.test(value),
        uppercase: /[A-Z]/.test(value),
        digit: /\d/.test(value),
        special: /[\W_]/.test(value)
    }
}

const conditions = computed(() => passwordConditionsValid(props.modelValue))

const passwordValid = computed(() => {
    const c = conditions.value
    return c.length && c.lowercase && c.uppercase && c.digit && c.special
})

const confirmValid = computed(() => {
    return !props.showConfirmation || confirmPassword.value === props.modelValue
})

function emitValidity() {
    const validPassword = props.showConditions ? passwordValid.value : true
    emit('valid', validPassword && confirmValid.value)
}


function handleInput(value: string) {
    emit('update:modelValue', value)
    passwordError.value = ''
    emitValidity()
}

function handleBlur(value: string) {
    if (props.showConditions && !passwordValid.value) {
        passwordError.value = 'Le mot de passe ne respecte pas les critères.'
    } else {
        passwordError.value = ''
    }

    if (props.showConfirmation) {
        validateConfirmPassword(confirmPassword.value, true)
    }

    emitValidity()
}


function validateConfirmPassword(value: string, blurred = false): boolean {
    if (!props.showConfirmation) return true

    const match = value === props.modelValue

    if (blurred) {
        if (!value) {
            confirmError.value = 'Veuillez confirmer votre mot de passe.'
        } else if (!match) {
            confirmError.value = 'Les mots de passe ne correspondent pas.'
        } else {
            confirmError.value = ''
        }
    } else {
        confirmError.value = match ? '' : 'Les mots de passe ne correspondent pas.'
    }

    emitValidity()
    return match
}

function conditionClass(condition: boolean) {
    return condition ? 'text-green-400' : 'text-gray-400'
}
</script>
