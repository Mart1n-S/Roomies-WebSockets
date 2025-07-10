<template>
    <div class="relative flex flex-col gap-1">
        <label v-if="label" :for="name" class="text-sm text-gray-300">{{ label }}</label>

        <div class="relative transition group">
            <textarea v-bind="$attrs" :id="name" :name="name" :rows="rows" :placeholder="placeholder"
                :disabled="disabled" :value="modelValue" @input="onInput" @blur="$emit('blur')" :class="[
                    'w-full px-4 py-2 text-white border rounded resize-none bg-roomies-gray3 border-roomies-gray1',
                    'focus:outline-none focus:ring-2 focus:ring-roomies-blue',
                    'group-hover:border-roomies-blue group-hover:bg-roomies-gray2 transition',
                    props.class
                ]"></textarea>
        </div>

        <p v-if="error" class="text-xs text-red-500">{{ error }}</p>
    </div>
</template>

<script setup lang="ts">
defineOptions({ inheritAttrs: false })

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

const emit = defineEmits(['update:modelValue', 'blur'])

function onInput(event: Event) {
    emit('update:modelValue', (event.target as HTMLTextAreaElement).value)
}
</script>
