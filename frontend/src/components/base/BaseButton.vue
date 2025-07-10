<template>
    <button :type="type" :disabled="loading || disabled" :class="computedClasses">
        <span v-if="iconLeft && !loading" class="mr-2">
            <slot name="icon-left" />
        </span>

        <slot v-if="!loading" />

        <span v-else class="animate-pulse"><i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i></span>

        <span v-if="iconRight && !loading" class="ml-2">
            <slot name="icon-right" />
        </span>
    </button>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
    type?: 'button' | 'submit'
    variant?: 'primary' | 'success' | 'secondary' | 'danger' | 'link'
    loading?: boolean
    disabled?: boolean
    iconLeft?: boolean
    iconRight?: boolean
    class?: string
}>()

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

    return [base, variants[props.variant || 'primary'], props.class].join(' ')
})

</script>