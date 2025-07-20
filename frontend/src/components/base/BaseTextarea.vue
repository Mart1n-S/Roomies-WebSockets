<template>
    <div class="relative flex flex-col gap-1">
        <!-- Affiche le label au-dessus du textarea si fourni -->
        <label v-if="label" :for="name" class="text-sm text-gray-300">
            {{ label }}
        </label>

        <!--
            Zone de saisie personnalisée :
            - Styles Roomies (bords arrondis, couleur custom, effet focus et hover)
            - Désactive la resize classique pour une UI plus propre
            - Les props et attributs HTML sont forwardés (grâce à inheritAttrs)
         -->
        <div class="relative transition group">
            <textarea v-bind="$attrs" :id="name" :name="name" :rows="rows" :placeholder="placeholder"
                :disabled="disabled" :value="modelValue" @input="onInput" @blur="$emit('blur')" :class="[
                    'w-full px-4 py-2 text-white border rounded resize-none bg-roomies-gray3 border-roomies-gray1',
                    'focus:outline-none focus:ring-2 focus:ring-roomies-blue',
                    'group-hover:border-roomies-blue group-hover:bg-roomies-gray2 transition',
                    props.class
                ]"></textarea>
        </div>

        <!-- Affiche l’erreur de validation, le cas échéant -->
        <p v-if="error" class="text-xs text-red-500">{{ error }}</p>
    </div>
</template>

<script setup lang="ts">
// On désactive l’héritage auto des attributs sur la racine (permet de gérer v-bind="$attrs" explicitement)
defineOptions({ inheritAttrs: false })

/**
 * Props :
 * - label        : string      — label affiché au-dessus du champ
 * - name         : string      — attribut name/id du champ (unique)
 * - modelValue   : string      — valeur contrôlée (pour v-model)
 * - rows         : number      — hauteur (lignes)
 * - placeholder  : string      — texte d’indication
 * - disabled     : boolean     — état désactivé
 * - error        : string      — message d’erreur à afficher
 * - class        : string      — classes additionnelles pour customisation
 */
const props = defineProps<{
    label?: string
    name: string
    modelValue: string
    rows?: number
    placeholder?: string
    disabled?: boolean
    error?: string
    class?: string
}>()

// Événements : update:modelValue (pour le v-model), blur (pour la validation)
const emit = defineEmits(['update:modelValue', 'blur'])

/**
 * Sur saisie : on transmet la valeur au parent
 */
function onInput(event: Event) {
    emit('update:modelValue', (event.target as HTMLTextAreaElement).value)
}
</script>
