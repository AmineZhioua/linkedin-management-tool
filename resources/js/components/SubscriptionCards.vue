<template>
    <div>
        <!-- Subscription Type Buttons -->
        <div
            class="d-flex justify-content-center align-items-center gap-2 mt-3 text-black price-btns"
        >
            <button
                class="text-white bg-purple-800 px-5 py-2 rounded-3xl"
                :class="{ 'opacity-50': pricingMode === 'mensuel' }"
                @click="setSubscriptionType('mensuel')"
            >
                Mensuel
            </button>
            <button
                class="text-white bg-orange-500 px-5 py-2 rounded-3xl"
                :class="{ 'opacity-50': pricingMode === 'annuel' }"
                @click="setSubscriptionType('annuel')"
            >
                Annuel
            </button>
        </div>

        <!-- Pricing Cards -->
        <div
            class="flex flex-col md:flex-row justify-center items-center gap-6 w-full max-w-5xl mt-6"
        >
            <div
                v-for="(subscription, index) in subscriptions"
                :key="index"
                class="flex-1 max-w-[350px] border-2 border-orange-300 rounded-xl p-6 shadow-lg relative"
            >
                <!-- Discount Section -->
                <div class="absolute top-0 right-0 mt-2 mr-2">
                    <span
                        v-if="subscription.discount"
                        class="bg-red-500 text-white px-2 py-1 rounded-md"
                    >
                        -{{ subscription.discount }}%
                    </span>
                </div>
                
                <h2 class="text-lg font-bold">{{ subscription.name }}</h2>
                <p class="text-2xl font-semibold text-black">
                    {{
                        pricingMode === "mensuel"
                            ? subscription.monthly_price + "€ / mois"
                            : subscription.yearly_price + "€ / an"
                    }}
                </p>
                <p class="text-sm text-gray-600">
                    {{ subscription.description }}
                </p>

                <form @submit.prevent="handleSubmit(subscription)">
                    <input type="hidden" name="_token" :value="csrfToken" />
                    <input
                        type="hidden"
                        name="title"
                        :value="subscription.name"
                    />
                    
                    <input
                        type="hidden"
                        name="pricingMode"
                        :value="pricingMode"
                    />
                    
                    <input
                        type="hidden"
                        name="price"
                        :value="
                            pricingMode === 'mensuel'
                                ? subscription.monthly_price
                                : subscription.yearly_price"
                    />

                    <input 
                        type="hidden" 
                        name="subscription_id" 
                        :value="subscription.id"
                    />
                    <input 
                        type="hidden"
                        name="linkedin"
                        :value="subscription.linkedin"
                    />

                    <input 
                        type="hidden"
                        name="whatsapp"
                        :value="subscription.whatsapp"
                    />

                    <input 
                        type="hidden"
                        name="discount"
                        :value="subscription.discount"
                    />


                    <button
                        type="submit"
                        class="mt-4 w-full py-2 bg-black text-white rounded-md"
                    >
                        Démarrer
                    </button>
                </form>

                <hr class="my-4" />
                <ul class="text-left">
                    <li
                        v-for="(benefit, idx) in parsedBenefits(
                            subscription.features
                        )"
                        :key="idx"
                        class="flex items-center"
                    >
                        <span class="text-green-500">✔</span> {{ benefit }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from "vue";
import axios from "axios";

export default {
    props: {
        subscriptions: Array,
    },
    setup() {
        const pricingMode = ref("mensuel");
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");

        const setSubscriptionType = (type) => {
            pricingMode.value = type;
        };

        const parsedBenefits = (benefits) => {
            try {
                return Array.isArray(benefits)
                    ? benefits
                    : JSON.parse(benefits);
            } catch (error) {
                console.error("Error parsing benefits:", error);
                return [];
            }
        };

        const handleSubmit = async (subscription) => {
            const price =
                pricingMode.value === "mensuel"
                    ? subscription.monthly_price
                    : subscription.yearly_price;

            try {
                const response = await axios.post("/session", {
                    _token: csrfToken,
                    title: subscription.name,
                    price: price,
                    pricingMode: pricingMode.value,
                    subscription_id: subscription.id,
                    linkedin: subscription.linkedin,
                    whatsapp: subscription.whatsapp,
                    discount: subscription.discount,
                });

                window.location.href = response.data.url;
            } catch (error) {
                console.error(
                    "Error:",
                    error.response ? error.response.data : error.message
                );
                alert("An error occurred. Please try again.");
            }
        };

        return {
            pricingMode,
            setSubscriptionType,
            parsedBenefits,
            handleSubmit,
            csrfToken,
        };
    },
};
</script>
