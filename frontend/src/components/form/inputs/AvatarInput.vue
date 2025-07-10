<template>
    <BaseFileInput :label="label" :name="name" :error="error" @change="handleChange" />
</template>

<script setup lang="ts">
import { ref } from 'vue'
import BaseFileInput from '@/components/base/BaseFileInput.vue'

const props = defineProps<{
    label?: string
    name: string
}>()

const emit = defineEmits<{
    (e: 'update:file', file: File | null): void
    (e: 'valid', isValid: boolean): void
}>()

const error = ref('')

function handleChange(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0] || null
    emit('update:file', file)

    if (!file) {
        error.value = ''
        emit('valid', true)
        return
    }

    const validTypes = ['image/jpeg', 'image/png', 'image/webp']
    if (!validTypes.includes(file.type)) {
        error.value = 'Format invalide (JPEG, PNG, WEBP uniquement).'
        emit('valid', false)
    } else if (file.size > 5 * 1024 * 1024) {
        error.value = 'L’image ne doit pas dépasser 5 Mo.'
        emit('valid', false)
    } else {
        error.value = ''
        emit('valid', true)
    }
}
</script>
