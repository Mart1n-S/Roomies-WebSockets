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

/**
 * ConfirmDeleteModal.vue
 * Composant de modal de confirmation pour les actions de suppression.
 * Affiche un message et demande une confirmation avant d'exécuter une action.
 */
// Définition des props reçues par le composant
const props = defineProps<{
    title: string                   // Titre du modal
    message: string                 // Message principal du modal
    confirmLabel?: string           // Libellé du bouton de confirmation (optionnel)
    cancelLabel?: string            // Libellé du bouton d'annulation (optionnel)
    onConfirm: () => Promise<void> | void  // Fonction à appeler lors de la confirmation
}>()

// Déclaration de l'événement "close" émis lors de la fermeture du modal
const emit = defineEmits(['close'])

// État local pour afficher un loader et désactiver les boutons pendant une action asynchrone
const loading = ref(false)

// Fonction appelée pour fermer le modal
function handleClose() {
    // On ne ferme pas si une action est en cours
    if (!loading.value) {
        emit('close')
    }
}

// Fonction appelée lorsqu'on clique sur "Confirmer"
async function handleConfirm() {
    loading.value = true    // Active le loader et désactive les boutons
    try {
        // Appelle la fonction onConfirm passée en props
        await props.onConfirm()
        // Ferme le modal après confirmation réussie
        emit('close')
    } catch (e) {
        // En cas d'erreur lors de la confirmation, on l'affiche dans la console
        console.error('Erreur dans la confirmation :', e)
    } finally {
        // Désactive le loader dans tous les cas
        loading.value = false
    }
}
</script>
