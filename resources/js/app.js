import { createApp } from "vue"; // Import Vue

import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";

// Import your components
import Card from "./components/Card.vue";

// Create and mount the Vue app
const app = createApp({
    data() {
        return {
            pricingMode: "monthly", // Default to monthly pricing
            subscriptions: window.subscriptions, // Accessing subscriptions from the global window object
        };
    },
    methods: {
        togglePricingMode(mode) {
            this.pricingMode = mode;
        },
    },
});

app.component("card", Card);

app.mount("#app");