<template>
    <div class="cards-container">
        <!-- Subscription Type Buttons Section -->
        <div class="pricing-toggle-container text-center mt-4 mb-0">
            <div class="d-inline-flex bg-light p-1 rounded-pill shadow-sm gap-3">
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

        <!-- Debugging: Show if subscriptions are empty -->
        <div v-if="!subscriptions || subscriptions.length === 0" class="text-center my-4">
            <p class="text-danger">No subscriptions available.</p>
        </div>

        <!-- Pricing Cards Section -->
        <div class="cards">
            <div class="container py-4">
                <div class="row justify-content-center">
                    <div
                        v-for="(subscription, index) in subscriptions"
                        :key="index"
                        class="col-md-6 mb-4"
                    >
                        <div
                            class="card border-0 rounded-4 shadow-sm position-relative"
                            style="
                                border: 2px solid #ffe8e0 !important;
                                max-width: 500px;
                                margin: 0 auto;
                            "
                        >
                            <div class="card-body p-3">
                                <!-- Title -->
                                <h2 class="fw-bold text-lg mb-1">
                                    {{ subscription.name }}
                                </h2>

                                <!-- Price with discount display -->
                                <h3 class="fw-semibold text-muted text-lg my-2 ml-2">
                                    <template v-if="appliedDiscount.amount > 0">
                                        <span class="text-decoration-line-through me-2">
                                            {{
                                                pricingMode === 'mensuel' ? subscription.monthly_price + '€'
                                                    : subscription.yearly_price + '€'
                                            }}
                                        </span>
                                        <span class="text-success">
                                            {{
                                                calculateDiscountedPrice(
                                                    pricingMode === 'mensuel'
                                                        ? subscription.monthly_price
                                                        : subscription.yearly_price
                                                )
                                            }}€
                                            {{
                                                pricingMode === 'mensuel'
                                                    ? '/mois'
                                                    : '/an'
                                            }}
                                        </span>
                                    </template>
                                    <template v-else>
                                        {{
                                            pricingMode === 'mensuel'
                                                ? subscription.monthly_price + '€ /mois'
                                                : subscription.yearly_price + '€ /an'
                                        }}
                                    </template>
                                </h3>

                                <!-- Subtitle -->
                                <p class="text-muted mb-3 text-sm">
                                    {{ subscription.description }}
                                </p>

                                <!-- Subscription Form -->
                                <form @submit.prevent="handleSubmit(subscription)">
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
                                        :value="calculateDiscountedPrice(
                                                pricingMode === 'mensuel'
                                                    ? subscription.monthly_price
                                                    : subscription.yearly_price
                                            )"
                                    />
                                    <input
                                        type="hidden"
                                        name="subscription_id"
                                        :value="subscription.id"
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

                                    <!-- Conditionally Render Button -->
                                    <button
                                        type="submit"
                                        class="btn text-white bg-black w-100 rounded-pill py-1 fw-medium text-sm"
                                        :disabled="isSubscribed(subscription.id, pricingMode)"
                                    >
                                        {{ isSubscribed(subscription.id, pricingMode) ? 'Déja activé' : 'Démarrer' }}
                                    </button>
                                </form>

                                <!-- Divider -->
                                <hr class="my-3" />

                                <!-- Features -->
                                <p class="fw-medium mb-2 text-sm">
                                    Fonctionnalités incluses
                                </p>

                                <ul class="list-unstyled text-sm">
                                    <li class="d-flex align-items-center gap-2 mb-1">
                                        <img
                                            src="/build/assets/icons/check-green.svg"
                                            alt="check-icon"
                                            style="width: 20px; height: 20px;"
                                        />
                                        <span>{{ subscription.available_posts }} Publications disponibles</span>
                                    </li>
                                    <li class="d-flex align-items-center gap-2 mb-1">
                                        <img
                                            src="/build/assets/icons/check-green.svg"
                                            alt="check-icon"
                                            style="width: 20px; height: 20px;"
                                        />
                                        <span>{{ subscription.boost_likes }} Boost de J'aime</span>
                                    </li>
                                    <li class="d-flex align-items-center gap-2 mb-1">
                                        <img
                                            src="/build/assets/icons/check-green.svg"
                                            alt="check-icon"
                                            style="width: 20px; height: 20px;"
                                        />
                                        <span>{{ subscription.boost_comments }} Boost de Commentaires</span>
                                    </li>
                                </ul>

                                <!-- Discount Badge -->
                                <div v-if="subscription.discount"
                                    class="position-absolute top-0 end-0 translate-middle-y me-3"
                                >
                                    <span class="badge bg-red-600 rounded-pill px-2 py-1 fs-6">
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
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
    props: {
        subscriptions: {
            type: Array,
            required: true,
        },
        userSubscriptions: {
            type: Array,
            required: false,
            default: () => [],
        },
    },

    setup(props) {
        const pricingMode = ref('mensuel');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const appliedDiscount = ref({
            amount: 0,
            type: 'percentage',
        });

        onMounted(() => {
            console.log('Subscriptions:', props.subscriptions);
            console.log('User Subscriptions:', props.userSubscriptions);
            setupCouponHandler();
        });

        const isSubscribed = (subscriptionId, pricingMode) => {
            return Array.isArray(props.userSubscriptions) &&
                props.userSubscriptions.some(sub =>
                    sub.subscription_id === subscriptionId &&
                    sub.pricing_mode === pricingMode
                );
        };

        const setSubscriptionType = (type) => {
            pricingMode.value = type;
        };

        const calculateDiscountedPrice = (originalPrice) => {
            originalPrice = parseFloat(originalPrice);
            if (appliedDiscount.value.amount <= 0) {
                return originalPrice.toFixed(2);
            }

            if (appliedDiscount.value.type === 'percentage') {
                return Math.max(0, originalPrice - (originalPrice * appliedDiscount.value.amount) / 100).toFixed(2);
            } else {
                return Math.max(0, originalPrice - appliedDiscount.value.amount).toFixed(2);
            }
        };

        const setupCouponHandler = () => {
            const applyButton = document.getElementById('apply-coupon');
            if (!applyButton) return;

            applyButton.addEventListener('click', function () {
                const couponCode = document.getElementById('coupon-code')?.value;
                const messageEl = document.getElementById('coupon-message');

                if (!couponCode || !messageEl) return;

                axios.post('/apply-coupon', { coupon_code: couponCode }, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    },
                }).then((response) => {
                    const data = response.data;
                    if (data.success) {
                        messageEl.innerText = 'Code promo appliqué !';
                        messageEl.classList.remove('text-red-500');
                        messageEl.classList.add('text-green-500');

                        appliedDiscount.value = {
                            amount: data.discount,
                            type: data.type,
                        };
                    } else {
                        messageEl.innerText = data.error || 'Code promo invalide';
                        messageEl.classList.add('text-red-500');
                        messageEl.classList.remove('text-green-500');
                    }
                }).catch((error) => {
                    messageEl.innerText = 'Une erreur s\'est produite. Veuillez réessayer plus tard.';
                    messageEl.classList.add('text-red-500');
                    console.error('Coupon error:', error);
                });
            });
        };

        const handleSubmit = async (subscription) => {
            const basePrice =
                pricingMode.value === 'mensuel'
                    ? subscription.monthly_price
                    : subscription.yearly_price;

            const finalPrice = calculateDiscountedPrice(basePrice);

            try {
                const response = await axios.post('/session', {
                    _token: csrfToken,
                    title: subscription.name,
                    price: finalPrice,
                    pricingMode: pricingMode.value,
                    subscription_id: subscription.id,
                    whatsapp: subscription.whatsapp,
                    discount: subscription.discount,
                });

                window.location.href = response.data.url;
            } catch (error) {
                console.error('Error:', error.response ? error.response.data : error.message);
                alert('Une erreur s\'est produite. Veuillez réessayer plus tard.');
            }
        };

        return {
            pricingMode,
            setSubscriptionType,
            handleSubmit,
            csrfToken,
            calculateDiscountedPrice,
            appliedDiscount,
            isSubscribed,
        };
    },
};
</script>

<style scoped>
.cards-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
}

.cards {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
}

.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 1rem;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
}

.card.highlight {
    border: 2px solid #0d6efd !important;
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .card {
        margin-bottom: 1.5rem;
        max-width: 90% !important;
    }
}

.pricing-toggle-container {
    margin: 1.5rem 0;
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
    font-size: 0.9rem;
}

.btn-inactive {
    background: transparent;
    color: #6b7280;
    font-weight: 500;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.btn-inactive:hover {
    background: rgba(255, 16, 183, 0.236);
    color: white;
}

.text-lg {
    font-size: 1rem;
}

.text-sm {
    font-size: 0.875rem;
}
</style>