<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60" @click.self="handleClose">
        <div class="w-full max-w-sm p-6 text-white shadow-lg bg-roomies-gray4 rounded-xl">
            <h2 class="mb-4 text-lg font-semibold">{{ title }}</h2>
            <p class="mb-6">{{ message }}</p>
            <div class="flex justify-end space-x-3">
                <BaseButton variant="secondary" @click="handleClose" :disabled="loading">
                    {{ cancelLabel || 'Annuler' }}
                </BaseButton>
                <BaseButton variant="danger" @click="handleConfirm" :loading="loading">
                    {{ confirmLabel || 'Confirmer' }}
                </BaseButton>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import BaseButton from '@/components/base/BaseButton.vue'

const props = defineProps<{
    title: string
    message: string
    confirmLabel?: string
    cancelLabel?: string
    onConfirm: () => Promise<void> | void
}>()

const emit = defineEmits(['close'])
const loading = ref(false)

function handleClose() {
    if (!loading.value) {
        emit('close')
    }
}

async function handleConfirm() {
    loading.value = true
    try {
        await props.onConfirm()
        emit('close')
    } catch (e) {
        console.error('Erreur dans la confirmation :', e)
    } finally {
        loading.value = false
    }
}
</script>
