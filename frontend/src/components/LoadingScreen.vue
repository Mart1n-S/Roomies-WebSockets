<template>
    <div class="fixed inset-0 z-50 flex flex-col items-center justify-center gap-4 bg-roomies-gray3">
        <img src="@/assets/images/icons/ic_roomies_outline.svg" alt="Roomies Logo"
            class="w-20 h-20 animate-spin-rapide" />
        <p class="text-sm font-medium text-white animate-pulse">Un peu de patience...</p>
    </div>
</template>

<style scoped>
@keyframes spin-rapide {
    0% {
        transform: rotate(0deg);
        animation-timing-function: cubic-bezier(0.3, 0, 0.7, 1);
        /* Courbe plus aggressive */
    }

    8% {
        /* Élan plus court et plus rapide */
        transform: rotate(-10deg);
        animation-timing-function: cubic-bezier(0.1, 0, 0.9, 1.1);
        /* Rebond léger */
    }

    100% {
        transform: rotate(360deg);
    }
}

.animate-spin-rapide {
    animation: spin-rapide 0.9s infinite;
    /* Durée très raccourcie */
    transform-origin: center;
    will-change: transform;
}

.animate-pulse {
    animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    /* Synchronisé avec le spinner */
}

@keyframes pulse {

    0%,
    100% {
        opacity: 0.9;
    }

    50% {
        opacity: 0.5;
    }
}
</style>