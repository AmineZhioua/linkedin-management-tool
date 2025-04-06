<template>
    <div class="w-full h-[80%] flex align-items-center justify-content-center py-10 px-5">
        <div class="flex flex-col gap-4 text-center align-items-center justify-center h-full">

            <!-- Title & Sentence -->
            <div class="flex flex-col gap-4 text-center align-items-center">
                <h2 class="text-black fw-bold">Plateforme de Marque</h2>
                <p class="text-black">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
                </p>
            </div>

            <!-- Card -->
            <div 
                class="bg-white rounded-xl py-12 px-6 mt-4 flex flex-col align-items-center justify-between w-full max-w-[1000px] min-h-[300px]"
                style="box-shadow: rgb(180 234 237 / 92%) 0px 0px 13px 13px;"
            >
                <h1 class="text-black fw-bold text-3xl">C'est Parti  pour Vous!</h1>
                <p class="text-black">
                    {{
                        hasCompletedData
                            ? "Modifiez les informations de votre marque."
                            : hasPartialData
                            ? "Continuez à compléter les informations de votre marque."
                            : "Veuillez complétez le questionnaire pour accéder à votre marque."
                    }}
                </p>

                <!-- If fully completed, show "Recommencer" -->
                <button
                    v-if="hasCompletedData"
                    class="py-2 px-4 rounded-full bg-black text-white fw-semibold"
                    @click="$emit('start')"
                >
                    Recommencer la plateforme de marque à nouveau
                </button>

                <!-- If partially completed, show "Continuer" -->
                <button
                    v-if="hasPartialData && !hasCompletedData"
                    class="py-2 px-4 rounded-full bg-black text-white fw-semibold"
                    @click="$emit('start')"
                >
                    Continuer ma plateforme de marque
                </button>

                <!-- If never started, show "Commencer" -->
                <button
                    v-if="!hasPartialData && !hasCompletedData"
                    class="py-2 px-4 rounded-full bg-black text-white fw-semibold"
                    @click="$emit('start')"
                >
                    Réaliser ma plateforme de marque
                </button>
            </div>
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
