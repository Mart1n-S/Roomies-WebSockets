<template>
    <div class="bg-roomies-gray5 text-white min-h-screen flex flex-col">
        <!-- Hero -->
        <section class="relative bg-[#404EED] text-white py-32 px-6 text-center overflow-hidden">
            <div class="relative z-10 max-w-4xl mx-auto flex flex-col items-center gap-4">
                <img src="@/assets/images/icons/ic_roomies_outline.svg" alt="Roomies Logo"
                    class="w-20 h-20 md:w-32 md:h-32" />

                <h1 class="text-6xl font-extrabold tracking-tight text-white drop-shadow mb-4">
                    Roomies
                </h1>

                <h2 class="text-4xl font-bold mb-6 leading-tight text-white/90">
                    IMAGINE UN ENDROIT...
                </h2>

                <p class="text-lg text-white/80 max-w-2xl mx-auto mb-8">
                    ...où tu peux discuter avec tes amis, créer des groupes privés, jouer à des mini-jeux avec tes potes
                    ou contre des inconnus, ou simplement te
                    détendre dans un espace sécurisé. Roomies rend tout ça possible.
                </p>

                <div class="flex justify-center gap-4 flex-wrap">
                    <!-- Bouton Connexion -->
                    <RouterLink to="/login" class="bg-white text-[#23272A] font-semibold px-6 py-3 rounded-full shadow-sm transition-colors duration-200
         hover:bg-gray-200 hover:text-black
         active:bg-gray-300">
                        Connexion
                    </RouterLink>

                    <!-- Bouton Inscription -->
                    <RouterLink to="/register" class="bg-[#23272A] text-white font-semibold px-6 py-3 rounded-full shadow-sm transition-colors duration-200
         hover:bg-[#1c1f22] hover:text-gray-200
         active:bg-[#101214]">
                        Inscription
                    </RouterLink>


                </div>
            </div>

            <img src="@/assets/images/hero-illustration.svg" class="absolute bottom-0 left-0 w-full object-cover z-0"
                alt="" />
            <img src="@/assets/images/hero-illustration-left.svg" class="absolute bottom-0 left-0 w-1/3 max-w-xs z-0"
                alt="" />
            <img src="@/assets/images/hero-illustration-right.svg" class="absolute bottom-0 right-0 w-1/3 max-w-xs z-0"
                alt="" />
        </section>
        <!-- TODO: Mettre les bonnes images -->
        <!-- Features -->
        <section class="bg-roomies-gray3 py-16 px-6 md:px-12 lg:px-32">
            <FeatureCard title="Créer un groupe privé"
                description="Invite uniquement les personnes que tu choisis pour une expérience sécurisée."
                image="src/assets/images/secure.svg" imagePosition="right" />
            <FeatureCard title="Discussion générale"
                description="Rejoins des salons de discussion pour parler de tout et de rien avec la communauté."
                image="src/assets/images/secure.svg" imagePosition="left" />
            <FeatureCard title="Rejoindre une communauté"
                description="Rejoins des salons de discussion pour parler de tout et de rien avec la communauté."
                image="src/assets/images/secure.svg" imagePosition="right" />
            <FeatureCard title="Discussions privées et en groupe"
                description="Rejoins des salons de discussion pour parler de tout et de rien avec la communauté."
                image="src/assets/images/secure.svg" imagePosition="left" />
            <FeatureCard title="Jouer à des mini-jeux"
                description="Rejoins des salons de discussion pour parler de tout et de rien avec la communauté."
                image="src/assets/images/secure.svg" imagePosition="right" />
            <FeatureCard title="100% sécurisé"
                description="Rejoins des salons de discussion pour parler de tout et de rien avec la communauté."
                image="src/assets/images/secure.svg" imagePosition="left" />
        </section>

        <!-- Footer -->
        <AppFooter />

    </div>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'
import FeatureCard from '@/components/home/FeatureCard.vue'
import AppFooter from '@/components/layout/AppFooter.vue'
</script>
