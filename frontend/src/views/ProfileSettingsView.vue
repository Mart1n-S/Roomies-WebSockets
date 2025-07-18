<template>
    <div class="relative z-10 flex flex-col items-center w-full h-screen px-2 py-10">
        <div class="z-10 w-full max-w-lg p-8 space-y-8 shadow-lg bg-roomies-gray3 rounded-2xl">
            <!-- Header profil -->
            <div class="flex items-center justify-between gap-4 ">
                <div class="flex items-center gap-4">
                    <img :src="user?.avatar"
                        class="object-cover w-20 h-20 rounded-full shadow ring-4 ring-roomies-blue bg-roomies-gray2"
                        :alt="user?.pseudo" />

                    <div class="flex flex-col">
                        <span class="text-lg font-bold text-white">
                            {{ user?.pseudo }}
                        </span>
                        <span class="text-xs text-gray-400 tracking-wider mt-0.5">
                            #{{ user?.friendCode }}
                        </span>
                    </div>
                </div>

                <BaseButton variant="primary" iconLeft @click="showUpdateModal = true">
                    <template #icon-left>
                        <i class="pi pi-pencil" />
                    </template>
                    Modifier
                </BaseButton>
            </div>

            <hr class="border-roomies-gray1" />

            <!-- Infos -->
            <div class="space-y-4">
                <div class="p-4 rounded-lg bg-roomies-gray2">
                    <span class="block text-sm text-gray-400">Adresse email</span>
                    <span class="block text-base font-medium text-white">{{ user?.email }}</span>
                </div>
            </div>
        </div>

        <!-- Modal de modification -->
        <UpdateUserModalForm v-if="showUpdateModal" :user="user" @close="showUpdateModal = false"
            @update-success="handleUpdateSuccess" />

        <img src="@/assets/images/hero-illustration-form.svg"
            class="absolute inset-0 z-0 object-cover w-full h-full pointer-events-none" alt="" />
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { storeToRefs } from 'pinia'
import BaseButton from '@/components/base/BaseButton.vue'
import UpdateUserModalForm from '@/components/form/UpdateUserModalForm.vue'

const authStore = useAuthStore()
const { user } = storeToRefs(authStore)
const showUpdateModal = ref(false)

const handleUpdateSuccess = () => {
    showUpdateModal.value = false
}
</script>