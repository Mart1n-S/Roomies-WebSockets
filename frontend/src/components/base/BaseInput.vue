<template>
    <div class="relative flex flex-col gap-1">
        <label v-if="label" :for="name" class="text-sm text-gray-300">{{ label }}</label>

        <div class="relative transition group">
            <input v-bind="$attrs" :id="name" :name="name" :type="type" :placeholder="placeholder" :disabled="disabled"
                :value="modelValue" @input="onInput" @blur="$emit('blur')" :class="[
                    'w-full px-4 py-2 rounded bg-roomies-gray3 text-white border border-roomies-gray1',
                    'focus:outline-none focus:ring-2 focus:ring-roomies-blue',
                    'group-hover:border-roomies-blue group-hover:bg-roomies-gray2 transition',
                    $slots['right-icon'] ? 'pr-10' : '',
                    props.class
                ]" />

            <!-- Icone à droite (cliquable) -->
            <div v-if="$slots['right-icon']" class="absolute inset-y-0 flex items-center cursor-pointer right-3"
                @click="$emit('icon-click')">
                <slot name="right-icon" />
            </div>
        </div>

        <p v-if="error" class="text-xs text-red-500">{{ error }}</p>
    </div>
</template>

<script setup lang="ts">
/**
 * BaseInput.vue
 * Composant d'input de base avec support pour v-model, icône cliquable et validation.
 * Utilisé pour les formulaires dans l'application Roomies.
 */
defineOptions({ inheritAttrs: false })
/**
 * defineProps : permet au composant de recevoir des données
 * defineEmits : permet au composant de renvoyer des actions au parent
 * onInput() : est appelé quand l’utilisateur tape dans le champ, et envoie la nouvelle valeur au parent
 */

// On déclare les props que ce composant attend du parent
const props = defineProps<{
    label?: string            // Texte du label affiché au-dessus de l'input
    name: string              // Attribut "name" (et "id") du champ input
    type?: string             // Type d'input : "text", "email", "password", etc.
    placeholder?: string      // Texte gris affiché quand le champ est vide
    disabled?: boolean        // Pour désactiver l'input
    error?: string            // Message d'erreur à afficher sous l'input
    modelValue: string        // Valeur de l'input liée au v-model dans le parent
    class?: string            // Classe CSS personnalisée (optionnelle)
}>()

const emit = defineEmits([
    'update:modelValue',  // Mise à jour du v-model
    'icon-click',         // Clic sur l’icône
    'blur'                // Événement focusout pour déclencher la validation côté parent
])

// Fonction appelée à chaque saisie dans l'input
function onInput(event: Event) {
    // On émet un événement "update:modelValue" pour que le parent mette à jour sa variable
    emit('update:modelValue', (event.target as HTMLInputElement).value)
}
</script>
