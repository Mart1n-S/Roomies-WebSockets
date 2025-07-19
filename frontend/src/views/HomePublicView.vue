<template>
    <div class="flex flex-col min-h-screen text-white bg-roomies-gray5">
        <!-- Hero -->
        <section class="relative bg-[#404EED] text-white py-32 px-6 text-center overflow-hidden">
            <div class="relative z-10 flex flex-col items-center max-w-4xl gap-4 mx-auto">
                <img src="@/assets/images/icons/ic_roomies_outline.svg" alt="Roomies Logo"
                    class="w-20 h-20 md:w-32 md:h-32" />

                <h1 class="mb-4 text-6xl font-extrabold tracking-tight text-white drop-shadow">
                    Roomies
                </h1>

                <h2 class="mb-6 text-4xl font-bold leading-tight text-white/90">
                    IMAGINE UN ENDROIT...
                </h2>

                <p class="max-w-2xl mx-auto mb-8 text-lg text-white/80">
                    ...où tu peux discuter avec tes amis, créer des groupes privés, jouer à des mini-jeux avec tes potes
                    ou contre des inconnus, ou simplement te
                    détendre dans un espace sécurisé. Roomies rend tout ça possible.
                </p>

                <div class="flex flex-wrap justify-center gap-4">
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

            <img src="@/assets/images/hero-illustration.svg" class="absolute bottom-0 left-0 z-0 object-cover w-full"
                alt="" />
            <img src="@/assets/images/hero-illustration-left.svg" class="absolute bottom-0 left-0 z-0 w-1/3 max-w-xs"
                alt="" />
            <img src="@/assets/images/hero-illustration-right.svg" class="absolute bottom-0 right-0 z-0 w-1/3 max-w-xs"
                alt="" />
        </section>
        <!-- Features -->
        <section class="px-6 py-16 bg-roomies-gray3 md:px-12 lg:px-32">
            <FeatureCard title="Créer un groupe privé"
                description="Invite uniquement les personnes que tu choisis pour une expérience sécurisée."
                image="src/assets/images/serveur.png" imagePosition="right" />
            <FeatureCard title="Discussion générale"
                description="Rejoins un chat global pour parler de tout et de rien avec la communauté."
                image="src/assets/images/global-chat.png" imagePosition="left" />
            <FeatureCard title="Rejoindre une communauté"
                description="Fais toi des amis et découvre de nouvelles personnes partageant les mêmes centres d'intérêt."
                image="src/assets/images/friends-add.png" imagePosition="right" />
            <FeatureCard title="Discussions privées"
                description="Profite de la confidentialité pour discuter en tête-à-tête avec tes amis."
                image="src/assets/images/private-chat.png" imagePosition="left" />
            <FeatureCard title="Jouer à des mini-jeux"
                description="Amuse-toi avec des jeux intégrés pour te détendre ou défier tes amis."
                image="src/assets/images/game-morpion.png" imagePosition="right" />
            <FeatureCard title="100% sécurisé"
                description="L'accès à Roomies nécessite d'être inscrit et connecté, garantissant la sécurité de tes données."
                image="src/assets/images/login.png" imagePosition="left" />
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
