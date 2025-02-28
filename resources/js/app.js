import { createApp } from "vue";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";
import Card from "./components/Card.vue";
import SubscriptionButtons from "./components/SubscriptionButtons.vue";


const app = createApp({
    data() {
        return {
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
app.component("subscription-buttons", SubscriptionButtons);

app.mount("#app");