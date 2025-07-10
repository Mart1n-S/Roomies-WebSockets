<template>
    <div class="flex flex-col items-center gap-6 p-8 text-white md:flex-row bg-roomies-gray3"
        :class="imagePosition === 'right' ? 'md:flex-row-reverse' : ''">
        <!-- Image -->
        <div class="w-full md:w-1/2">
            <img :src="image" alt="feature image" class="object-cover w-full h-auto rounded-lg" />
        </div>

        <!-- Text content -->
        <div class="flex flex-col w-full gap-3 md:w-1/2">
            <h3 class="text-xl font-bold">{{ title }}</h3>
            <p class="text-sm text-gray-300">{{ description }}</p>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    title: string
    description: string
    image: string
    imagePosition?: 'left' | 'right'
}>()
</script>
