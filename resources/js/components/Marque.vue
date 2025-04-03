<template>
    <div class="marque-container container py-8">
        <div class="max-w-6xl mx-auto px-4">

            <!-- Brand Information Section -->
            <section class="mb-12 w-full relative">
                <h2 class="text-4xl font-bold mb-4">Ta marque</h2>
                <div class="px-6 py-10 w-full">
                    <div class="flex align-items-center justify-center gap-4">
                        <!-- Brand Logo -->
                        <img
                            :src="logoUrl"
                            alt="Logo de la marque"
                            class="w-32 h-24 object-contain"
                            v-if="plateforme.logo_marque"
                        />

                        <!-- Brand Information -->
                        <div>
                            <h3 class="text-4xl font-bold">
                                {{ plateforme.nom_marque }}
                            </h3>
                            <p class="text-md">
                                {{ plateforme.description_marque }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Mon marque Button -->
                <div class="text-center mt-6">
                    <a href="/plateforme-marque">
                        <button class="px-6 py-2 bg-black text-white rounded-full">
                            Modifier
                            <i class="fa-solid fa-pen ml-2"></i>
                        </button>
                    </a>
                </div>

                <!-- Image Decoration -->
                <img 
                    src="/build/assets/icons/fi-sr-calendar.svg" 
                    alt="Calendar Icon"
                    class="absolute bottom-0 right-0"
                />
            </section>

            <!-- Brand Name Section -->
            <section class="mb-12">
                <h2 class="text-xl font-bold mb-4">Nom de marque</h2>
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <p class="text-lg font-medium">
                        {{ plateforme.nom_marque }}
                    </p>
                </div>
                <div class="text-center mt-6"></div>
            </section>

            <!-- Brand Domain Section -->
            <section class="mb-12">
                <h2 class="text-xl font-bold mb-4">Domaine de la marque</h2>
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <p class="text-lg font-medium">
                        {{ plateforme.domaine_marque }}
                    </p>
                </div>
                <div class="text-center mt-6"></div>
            </section>

            <!-- Brand Logo Section -->
            <section class="mb-12">
                <h2 class="text-xl font-bold mb-4">Logo</h2>
                <div class="bg-white rounded-lg p-6 shadow-sm flex justify-center">
                    <div class="w-48 h-48">
                        <img
                            :src="logoUrl"
                            alt="Logo de la marque"
                            class="w-full h-full object-contain"
                            v-if="plateforme.logo_marque"
                        />
                        <div
                            class="w-full h-full bg-gray-200 rounded-lg flex items-center justify-center"
                            v-else
                        >
                            <span class="text-gray-400"
                                >Logo non disponible</span
                            >
                        </div>
                    </div>
                </div>
                <div class="text-center mt-6"></div>
            </section>

            <!-- Brand Description Section -->
            <section class="mb-12">
                <h2 class="text-xl font-bold mb-4">Description de la marque</h2>
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <p class="text-lg">{{ plateforme.description_marque }}</p>
                </div>
                <div class="text-center mt-6"></div>
            </section>
        </div>
    </div>
</template>

<script>
export default {
    name: "MarqueComponent",
    props: {
        plateforme: {
            type: Object,
            required: true,
        },
    },
    computed: {
        logoUrl() {
            if (this.plateforme.logo_marque) {
                if (this.plateforme.logo_marque.startsWith("/storage/")) {
                    return this.plateforme.logo_marque;
                } else if (this.plateforme.logo_marque.startsWith("storage/")) {
                    return "/" + this.plateforme.logo_marque;
                } else {
                    return "/storage/" + this.plateforme.logo_marque;
                }
            }
            return "";
        },
    },
};
</script>

<style scoped>
.marque-container {
    position: relative;
    min-height: 100vh;
}

@media (max-width: 768px) {
    .marque-container .bg-white {
        padding: 20px;
    }
}

button {
    transition: background-color 0.2s;
}
</style>
