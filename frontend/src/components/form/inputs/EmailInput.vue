<template>
    <BaseInput :label="label" :name="name" type="email" :model-value="modelValue" :error="error"
        @update:modelValue="onInput" @blur="() => validate(modelValue, true)" />
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import BaseInput from '@/components/base/BaseInput.vue'

const props = defineProps<{
    label?: string
    name: string
    modelValue: string
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void
    (e: 'valid', isValid: boolean): void
    (e: 'error', message: string): void
}>()

const error = ref('')

function validate(value: string, blurred = false) {
    const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)

    if (!isValid && blurred) {
        error.value = "L’adresse email n’est pas valide."
    } else if (isValid) {
        error.value = ''
    }

    emit('valid', isValid)
    emit('error', error.value)
}

function onInput(value: string) {
    emit('update:modelValue', value)
    validate(value)
}
</script>
