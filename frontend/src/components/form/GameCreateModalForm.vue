<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center px-3 bg-black bg-opacity-60" @click.self="close">
        <div class="w-full max-w-md p-6 shadow-lg bg-roomies-gray4 rounded-xl">
            <form @submit.prevent="submitForm" class="space-y-4">
                <h2 class="text-xl font-semibold text-white">Créer une partie</h2>

                <!-- Sélection du jeu -->
                <div>
                    <label for="selectGame" class="block mb-1 font-medium text-white">Jeu</label>
                    <select v-model="form.game" id="selectGame" name="selectGame"
                        class="w-full p-2 text-white rounded bg-roomies-gray2">
                        <option value="morpion">Morpion</option>
                        <option value="puissance4">Puissance 4</option>
                        <!-- D’autres jeux à venir ici -->
                    </select>
                </div>

                <!-- Nom de la partie -->
                <BaseInput name="roomName" label="Nom de la partie" v-model="form.name"
                    placeholder="Ex : Partie entre amis" :error="errors.name" @valid="handleNameValidation"
                    autocomplete="off" />

                <!-- Erreur globale -->
                <p v-if="errors.global" class="text-sm text-red-500">{{ errors.global }}</p>

                <!-- Boutons -->
                <div class="flex flex-col-reverse gap-3 mt-4 sm:flex-row sm:items-center">
                    <BaseButton @click="close" variant="danger" class="flex-1 text-center" type="button">
                        Fermer
                    </BaseButton>
                    <BaseButton type="submit" :loading="loading" class="flex-1 text-center">
                        Créer la partie
                    </BaseButton>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useGameStore } from '@/stores/gameStore'
import { useToast } from 'vue-toastification'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'

const emit = defineEmits(['close'])
const form = ref({ name: '', game: 'morpion' })
const errors = ref<{ name?: string; global?: string }>({})
const loading = ref(false)
const gameStore = useGameStore()
const toast = useToast()

function close() {
    emit('close')
}

function handleNameValidation(valid: boolean) {
    if (!valid) {
        errors.value.name = 'Le nom est invalide'
    } else {
        errors.value.name = undefined
    }
}

async function submitForm() {
    errors.value = {}
    if (!form.value.name) {
        errors.value.name = 'Le nom est obligatoire.'
        return
    }
    if (!form.value.game) {
        errors.value.global = 'Choisis un jeu.'
        return
    }

    loading.value = true
    try {
        // Envoie via store
        gameStore.createRoom({
            name: form.value.name,
            game: form.value.game,
        })
        close()
    } catch (e: any) {
        errors.value.global = e.message || 'Erreur inattendue'
        toast.error(errors.value.global)
    } finally {
        loading.value = false
    }
}
</script>
