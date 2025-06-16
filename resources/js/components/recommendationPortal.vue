<template>
    <div class="bg-black bg-opacity-50 inset-0 h-full w-full absolute"></div>
    <div class="flex flex-col flex-wrap rounded-lg p-4 gap-2 absolute top-[50%] left-[50%] translate-y-[-50%] translate-x-[-50%] bg-white">

        <div class="flex items-center justify-between gap-4 mb-2">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-robot text-3xl"></i>
                <h3 class="text-xl fw-semibold text-black mb-0 mt-1">Recommendation AI</h3>
            </div>
            <button>
                <img src="/build/assets/icons/close.svg" alt="Close Icon" height="20" width="20" />
            </button>
        </div>

        <p class="mb-0 text-muted mt-4">
            Optimisez vos performances futures grâce à votre assistant IA : 
            analysez l'ensemble de vos données pour obtenir des recommandations personnalisées d'amélioration.
        </p>

        <div class="flex flex-col gap-2 mt-4">
            <textarea 
                name="recommendation-input" 
                id="recommendation-input" 
                placeholder="Ajouter une note ou information supplémetaire..."
                class="bg-white p-2 rounded-lg border min-h-[250px]"
            ></textarea>

            <button class="text-white py-2 px-3 rounded-md border fw-semibold generate-btn">Démarrer</button>
        </div>
    </div>
</template>

<script>
export default {
    name: 'RecommendationPortal',
};
</script>


<style scoped>
.generate-btn {
    background: linear-gradient(
        135deg,
        rgb(255 16 185) 0%,
        rgb(255 125 82) 100%
    );
    border: none;
}
</style>