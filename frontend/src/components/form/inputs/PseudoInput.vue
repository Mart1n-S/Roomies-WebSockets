<template>
    <BaseInput :label="label" :name="name" type="text" :model-value="modelValue" :error="error" minlength="2"
        maxlength="20" pattern="^[a-zA-Z0-9_]{2,20}$" autocomplete="off" @update:modelValue="onInput"
        @blur="() => validate(modelValue, true)" />
</template>

<script setup lang="ts">
import { ref } from 'vue'
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

function validate(value = props.modelValue, blurred = false) {
    const isValid = /^[a-zA-Z0-9_]{2,20}$/.test(value)

    if (!isValid && blurred) {
        error.value = 'Le pseudo doit contenir entre 2 et 20 caractères valides.'
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
