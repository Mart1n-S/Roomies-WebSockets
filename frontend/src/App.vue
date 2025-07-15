<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore'
import { ref, watchEffect } from 'vue'
import LoadingScreen from '@/components/UI/LoadingScreen.vue'

const auth = useAuthStore()
const showLoader = ref(true)

watchEffect(() => {
  showLoader.value = !auth.appReady
})
</script>

<template>
  <div>
    <RouterView />
    <LoadingScreen v-if="showLoader" />
  </div>
</template>
