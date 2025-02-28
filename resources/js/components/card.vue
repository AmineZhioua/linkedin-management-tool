<template>
    <div class="flex-1 max-w-[350px] border-2 border-orange-300 rounded-xl p-6 shadow-lg">
        <h2 class="text-lg font-bold">{{ title }}</h2>
        <p class="text-2xl font-semibold text-black">
            {{ pricingMode === "mensuel" ? monthly_price + "€ / mois" : yearly_price + "€ / an" }}
        </p>
        <p class="text-sm text-gray-600">{{ description }}</p>
        <form @submit.prevent="handleSubmit">

            <input type="hidden" name="_token" :value="csrfToken" />
            <input type="hidden" name="title" :value="title" />
            <input type="hidden" name="price" :value="price" />
            <input type="hidden" name="pricingMode" :value="pricingMode" />

            <button type="submit" class="mt-4 w-full py-2 bg-black text-white rounded-md">
                Démarrer
            </button>
        </form>
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
    import axios from 'axios';

    export default {
        props: {
            title: String,
            monthly_price: [String, Number],
            yearly_price: [String, Number],
            description: String,
            benefits: Array,
            pricingMode: String,
        },
        inject: ['pricingMode'],
        data() {
            return {
                csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            };
        },
        computed: {
            parsedBenefits() {
                return Array.isArray(this.benefits)
                    ? this.benefits
                    : JSON.parse(this.benefits);
            },
        },

        methods: {
            async handleSubmit() {
                try {
                    const response = await axios.post('/session', {
                        _token: this.csrfToken,
                        title: this.title,
                        price: this.price,
                        pricingMode: this.pricingMode,
                    });

                    window.location.href = response.data.url;
                } catch (error) {
                    console.error('Error:', error.response.data);
                    alert('An error occurred. Please try again.');
                }
            }
        }
    };
</script>