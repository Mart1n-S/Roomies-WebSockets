<template>
    <div class="fixed inset-0 z-50 flex flex-col items-center justify-center gap-4 bg-roomies-gray3">
        <img src="@/assets/images/icons/ic_roomies_outline.svg" alt="Roomies Logo"
            class="w-20 h-20 animate-spin-custom" />
        <p class="text-sm font-medium text-white animate-pulse">Un peu de patience...</p>
    </div>
</template>

<style scoped>
@keyframes spin-custom {
    0% {
        transform: rotate(0deg);
        animation-timing-function: cubic-bezier(0.5, 0.1, 0.5, 0.9);
    }

    15% {
        transform: rotate(-12deg);
        animation-timing-function: cubic-bezier(0.2, 0, 0.8, 1);
    }

    100% {
        transform: rotate(360deg);
    }
}

.animate-spin-custom {
    animation: spin-custom 1.8s infinite;
    transform-origin: center;
    will-change: transform;
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {

    0%,
    100% {
        opacity: 0.8;
    }

    50% {
        opacity: 0.4;
    }
}
</style>