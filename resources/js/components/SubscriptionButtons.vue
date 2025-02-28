<template>
    <div>
        <!-- Subscription Type Buttons -->
        <div class="d-flex justify-content-center align-items-center gap-2 mt-3 text-black price-btns">
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

        <!-- Subscription Cards -->
        <div class="flex flex-wrap gap-4 justify-center mt-6">
            <!-- Pass the Children Component -->
            <slot></slot>
        </div>
    </div>
</template>

<script>
    import { ref, provide } from 'vue';

    export default {
        setup() {
            const pricingMode = ref('mensuel');

            const setSubscriptionType = (type) => {
                pricingMode.value = type;
            };

            provide('pricingMode', pricingMode);

            return {
                pricingMode,
                setSubscriptionType,
            };
        },
    };
</script>