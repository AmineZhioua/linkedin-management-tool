<template>
    <div
        class="flex-1 max-w-[350px] border-2 border-orange-300 rounded-xl p-6 shadow-lg"
    >
        <h2 class="text-lg font-bold">{{ title }}</h2>
        <p class="text-2xl font-semibold text-black">
            {{ price }} € / {{ pricingMode === "monthly" ? "mois" : "an" }}
        </p>
        <p class="text-sm text-gray-600">{{ description }}</p>
        <button class="mt-4 w-full py-2 bg-black text-white rounded-md">
            Démarrer
        </button>
        <hr class="my-4" />
        <ul class="text-left">
            <li
                v-for="(benefit, index) in parsedBenefits"
                :key="index"
                class="flex items-center"
            >
                <span class="text-green-500">✔</span> {{ benefit }}
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: {
        title: String,
        price: [String, Number], // Accept both string and number for price
        description: String,
        benefits: Array, // Array of benefits from the database
    },
    data() {
        return {
            pricingMode: "monthly", // Default to monthly pricing
        };
    },
    computed: {
        parsedBenefits() {
            // Ensure benefits are an array and parse them if necessary
            return Array.isArray(this.benefits)
                ? this.benefits
                : JSON.parse(this.benefits);
        },
    },
};
</script>
