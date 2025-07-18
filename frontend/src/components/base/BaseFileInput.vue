<template>
    <div class="flex flex-col gap-2">
        <label v-if="label" :for="name" class="text-sm font-medium text-gray-200">{{ label }}</label>

        <!-- Zone de drop personnalisée -->
        <div class="relative flex flex-col items-center justify-center w-full h-32 transition border-2 border-dashed rounded-md cursor-pointer bg-roomies-gray3 border-roomies-gray2 hover:border-roomies-blue hover:bg-roomies-gray2"
            @click="triggerFileInput">
            <i class="mb-1 text-3xl text-gray-400 pi pi-image"></i>
            <p class="text-xs text-gray-400">Cliquez pour sélectionner une image</p>
            <input ref="fileInput" type="file" :id="name" :name="name"
                accept="image/png,image/jpeg,image/webp,image/svg+xml" @change="handleChange" class="hidden" />
        </div>

        <!-- Aperçu de l’image -->
        <div v-if="preview" class="relative w-24 h-24 mt-3 group">
            <img :src="preview" alt="Prévisualisation"
                class="object-cover w-full h-full border rounded-lg shadow border-roomies-gray2" />
            <button @click="removeImage" type="button" title="Supprimer l’image" aria-label="Supprimer l’image"
                class="absolute flex items-center justify-center w-6 h-6 text-xs text-white transition bg-red-600 rounded-full shadow top-[-8px] right-[-8px] hover:bg-red-700 group-hover:scale-110">
                ✕
            </button>
        </div>

        <!-- Message d'erreur -->
        <p v-if="error" class="mt-1 text-xs text-red-500">{{ error }}</p>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

defineProps<{
    label?: string
    name: string
    error?: string
}>()

const emit = defineEmits(['update:modelValue'])

const preview = ref<string | null>(null)
const fileInput = ref<HTMLInputElement | null>(null)

function handleChange(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0]
    emit('update:modelValue', file)

    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader()
        reader.onload = () => (preview.value = reader.result as string)
        reader.readAsDataURL(file)
    } else {
        preview.value = null
    }
}

function triggerFileInput() {
    fileInput.value?.click()
}

function removeImage() {
    preview.value = null
    if (fileInput.value) fileInput.value.value = ''
    emit('update:modelValue', null)
}
</script>
