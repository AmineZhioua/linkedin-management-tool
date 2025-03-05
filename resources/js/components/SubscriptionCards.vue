<template>
    <div class="cards-container">
        <!-- Subscription Type Buttons Section -->
        <div class="pricing-toggle-container text-center mt-4 mb-0">
            <div
                class="d-inline-flex bg-light p-1 rounded-pill shadow-sm gap-3"
            >
                <button
                    class="btn rounded-pill px-4 py-2 position-relative"
                    :class="
                        pricingMode === 'mensuel'
                            ? 'btn-active text-white'
                            : 'btn-inactive'
                    "
                    @click="setSubscriptionType('mensuel')"
                >
                    <span class="position-relative z-2">Mensuel</span>
                </button>

                <button
                    class="btn rounded-pill px-4 py-2 position-relative"
                    :class="
                        pricingMode === 'annuel'
                            ? 'btn-active text-white'
                            : 'btn-inactive'
                    "
                    @click="setSubscriptionType('annuel')"
                >
                    <span class="position-relative z-2">Annuel</span>
                </button>
            </div>
        </div>

        <!-- Pricing Cards Section -->
        <div class="cards">
            <!-- Cards -->
            <div class="container py-4">
                <div class="row justify-content-center">
                    <div
                        v-for="(subscription, index) in subscriptions"
                        :key="index"
                        class="col-md-4 mb-4"
                    >
                        <div
                            class="card border-0 rounded-4 shadow-sm position-relative"
                            style="
                                border: 2px solid #ffe8e0 !important;
                                max-width: 350px;
                                margin: 0 auto;
                            "
                        >
                            <div class="card-body p-4">
                                <!-- Title -->
                                <h2 class="fw-bold text-xl mb-1">
                                    {{ subscription.name }}
                                </h2>

                                <!-- Price -->
                                <h3
                                    class="fw-semibold text-muted text-2xl my-3 ml-2"
                                >
                                    {{
                                        pricingMode === "mensuel"
                                            ? subscription.monthly_price +
                                              "€ /mois"
                                            : subscription.yearly_price +
                                              "€ /an"
                                    }}
                                </h3>

                                <!-- Subtitle -->
                                <p class="text-muted mb-4">
                                    {{
                                        subscription.description ||
                                        "Offert pour ton abonnement"
                                    }}
                                </p>

                                <!-- Button -->
                                <form
                                    @submit.prevent="handleSubmit(subscription)"
                                >
                                    <input
                                        type="hidden"
                                        name="_token"
                                        :value="csrfToken"
                                    />
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
                                                : subscription.yearly_price
                                        "
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
                                        class="btn text-white bg-black w-100 rounded-pill py-2 fw-medium"
                                    >
                                        Démarrer
                                    </button>
                                </form>

                                <!-- Divider -->
                                <hr class="my-4" />

                                <!-- Features List -->
                                <p class="fw-medium mb-3">
                                    {{
                                        subscription.name ===
                                        "Création Dashboard"
                                            ? "Création d'un dashboard 100% en ligne"
                                            : "Fonctionnalités incluses"
                                    }}
                                </p>

                                <ul class="list-unstyled">
                                    <li
                                        v-for="(benefit, idx) in parsedBenefits(
                                            subscription.features
                                        )"
                                        :key="idx"
                                        class="d-flex align-items-center gap-2 mb-2"
                                    >
                                        <img
                                            src="\build\assets\icons\check-green.svg"
                                            alt="check-icon"
                                        />
                                        <span>{{ benefit }}</span>
                                    </li>
                                </ul>

                                <!-- Discount Badge -->
                                <div
                                    v-if="subscription.discount"
                                    class="position-absolute top-0 end-0 translate-middle-y me-3"
                                >
                                    <span
                                        class="badge bg-red-600 rounded-pill px-3 py-2 fs-6"
                                    >
                                        -{{ subscription.discount }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from "vue";
import axios from "axios";

export default {
    props: {
        subscriptions: {
            type: Array,
            required: true,
        },
    },
    
    setup() {
        const pricingMode = ref("mensuel");
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

        const setSubscriptionType = (type) => {
            pricingMode.value = type;
        };

        const parsedBenefits = (benefits) => {
            try {
                return Array.isArray(benefits) ? benefits : JSON.parse(benefits);
            } catch (error) {
                console.error("Error parsing benefits:", error);
                return [];
            }
        };

        const handleSubmit = async (subscription) => {
            const price = pricingMode.value === "mensuel" ? subscription.monthly_price : subscription.yearly_price;

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



<style scoped>
    .cards-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 30px;
    }

    .cards {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 30px;
    }

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 1rem;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .card.highlight {
        border: 2px solid #0d6efd !important;
        transform: scale(1.05);
    }

    @media (max-width: 768px) {
        .card {
            margin-bottom: 2rem;
        }
    }

    .pricing-toggle-container {
        margin: 2rem 0;
    }

    .btn-active {
        background: linear-gradient(
            135deg,
            rgb(255 16 185) 0%,
            rgb(255 125 82) 100%
        );
        font-weight: 600;
        box-shadow: 0 2px 5px rgba(79, 70, 229, 0.3);
        transition: all 0.3s ease;
    }

    .btn-inactive {
        background: transparent;
        color: #6b7280;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-inactive:hover {
        background: rgba(255, 16, 183, 0.236);
        color: white;
    }
</style>
