<template>
    <aside v-if="currentComponent"
        class="flex flex-col h-full overflow-hidden border-l w-72 bg-roomies-gray4 border-roomies-gray3">
        <component :is="currentComponent" v-bind="currentProps" />
    </aside>
</template>


<script setup lang="ts">
import { computed } from 'vue'
import { useContextPanelStore } from '@/stores/contextPanelStore'

// On récupère dynamiquement le composant et les props à afficher selon le contexte
const store = useContextPanelStore()

const currentComponent = computed(() => store.component)
const currentProps = computed(() => store.props || {})
</script>
