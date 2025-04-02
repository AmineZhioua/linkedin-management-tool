<template>
    <div class="marque-container py-8">
        <div class="max-w-6xl mx-auto px-4">
            <!-- Brand Information Section -->
            <section class="mb-16">
                <h2 class="text-lg font-medium text-gray-500 mb-4">
                    Ta marque
                </h2>
                <div class="bg-white rounded-lg p-6 shadow-md flex flex-col gap-6">
                    <!-- Brand Logo -->
                    <div class="flex-shrink-0 w-32 h-32 mx-auto">
                        <img
                            :src="logoUrl"
                            alt="Logo de la marque"
                            class="w-full h-full object-contain mx-auto"
                            v-if="plateforme.logo_marque"
                        />
                        <div class="w-full h-full bg-gray-200 rounded-full flex items-center justify-center"
                            v-else
                        >
                            <span class="text-gray-400">Logo</span>
                        </div>
                    </div>

                    <!-- Brand Information -->
                    <div class="text-center">
                        <h3 class="text-2xl font-bold text-gray-800">
                            {{ plateforme.nom_marque }}
                        </h3>
                        <p class="text-gray-600 mt-1">
                            {{ plateforme.description_marque }}
                        </p>
                    </div>

                    <!-- Brand Domain -->
                    <div class="mt-4 text-center">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
                        >
                            {{ plateforme.domaine_marque }}
                        </span>
                    </div>
                </div>
            </section>

            <!-- My Marque Button -->
            <div class="text-center mt-6">
                <a href="/plateforme-marque">
                    <button
                        class="px-6 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors duration-200"
                    >
                        My Marque
                    </button>
                </a>
            </div>

            <!-- Debugging URL -->
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
                }
                else if (this.plateforme.logo_marque.startsWith("storage/")) {
                    return "/" + this.plateforme.logo_marque;
                }
                else {
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
    background-color: #f8fafc;
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
