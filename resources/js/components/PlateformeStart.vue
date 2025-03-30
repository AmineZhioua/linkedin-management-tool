<template>
    <div class="w-full h-[80%] flex align-items-center justify-content-center">
        <div class="flex flex-col gap-4 text-center align-items-center">
            <h2 class="text-black">Plateforme de Marque</h2>
            <p class="text-muted">
                {{
                    hasCompletedData
                        ? "Modifiez les informations de votre marque."
                        : hasPartialData
                        ? "Continuez à compléter les informations de votre marque."
                        : "Complétez les informations de votre marque."
                }}
            </p>

            <!-- If fully completed, show "Recommencer" -->
            <button
                v-if="hasCompletedData"
                class="py-2 px-4 rounded-full bg-black text-white fw-semibold"
                @click="$emit('start')"
            >
                Recommencer la plateforme de marque
            </button>

            <!-- If partially completed, show "Continuer" -->
            <button
                v-if="hasPartialData && !hasCompletedData"
                class="py-2 px-4 rounded-full bg-black text-white fw-semibold"
                @click="$emit('start')"
            >
                Continuer le questionnaire
            </button>

            <!-- If never started, show "Commencer" -->
            <button
                v-if="!hasPartialData && !hasCompletedData"
                class="py-2 px-4 rounded-full bg-black text-white fw-semibold"
                @click="$emit('start')"
            >
                Commencer
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        hasCompletedData: Boolean,
        hasPartialData: Boolean
    }
};
</script>
