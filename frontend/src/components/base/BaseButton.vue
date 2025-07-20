<template>
    <!--
        Bouton personnalisable :
        - Affiche une icône à gauche (slot 'icon-left') et/ou à droite (slot 'icon-right')
        - Affiche un loader animé si loading=true
        - Slots pour contenu custom
        - Gère la désactivation propre (disabled/chargement)
     -->
    <button :type="type" :disabled="loading || disabled" :class="computedClasses">
        <!-- Icône à gauche (si fourni et pas en loading) -->
        <span v-if="iconLeft && !loading" :class="{ 'mr-2': !noIconSpace }">
            <slot name="icon-left" />
        </span>

        <!-- Contenu principal du bouton (slot par défaut) -->
        <slot v-if="!loading" />

        <!-- Loader si loading -->
        <span v-else class="animate-pulse">
            <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
        </span>

        <!-- Icône à droite (si fourni et pas en loading) -->
        <span v-if="iconRight && !loading" :class="{ 'ml-2': !noIconSpace }">
            <slot name="icon-right" />
        </span>
    </button>
</template>

<script setup lang="ts">
import { computed } from 'vue'

/**
 * Composant bouton ultra réutilisable Roomies, pour tous les cas d’usage (CTA, formulaire, danger, etc.)
 *
 * Props :
 * - type: 'button' | 'submit'
 * - variant: palette couleur ('primary', 'success', 'secondary', 'danger', 'link')
 * - loading: true = affichage du spinner
 * - disabled: true = inactif, désactivé visuellement et UX
 * - iconLeft / iconRight: true = affiche les slots 'icon-left' ou 'icon-right'
 * - noIconSpace: retire l’espace entre l’icône et le texte si true
 * - class: string CSS additionnelle pour override/ajout
 */
const props = defineProps<{
    type?: 'button' | 'submit'
    variant?: 'primary' | 'success' | 'secondary' | 'danger' | 'link'
    loading?: boolean
    disabled?: boolean
    iconLeft?: boolean
    iconRight?: boolean
    noIconSpace?: boolean
    class?: string
}>()

/**
 * Calcule dynamiquement la classe CSS du bouton
 * - Gère les couleurs selon l’état (variant, inactif, etc.)
 * - Ajoute la classe custom éventuelle
 */
const computedClasses = computed(() => {
    const isInactive = props.loading || props.disabled
    const base = 'inline-flex items-center justify-center px-4 py-2 rounded text-white font-medium transition disabled:opacity-50'

    const variants: Record<string, string> = {
        primary: isInactive
            ? 'bg-roomies-blue'
            : 'bg-roomies-blue hover:bg-roomies-hover active:bg-roomies-active',
        success: isInactive
            ? 'bg-green-500'
            : 'bg-green-500 hover:bg-green-600',
        secondary: isInactive
            ? 'bg-roomies-gray2'
            : 'bg-roomies-gray2 hover:bg-roomies-gray1',
        danger: isInactive
            ? 'bg-red-500'
            : 'bg-red-500 hover:bg-red-600',
        link: isInactive
            ? 'text-roomies-blue underline bg-transparent'
            : 'text-roomies-blue underline hover:text-blue-400 px-0 py-0 bg-transparent'
    }

    // Concatène toutes les classes propres au bouton
    return [base, variants[props.variant || 'primary'], props.class].join(' ')
})
</script>
