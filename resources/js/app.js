import { createApp } from "vue";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";
import SubscriptionCards from "./components/SubscriptionCards.vue";

const app = createApp({
    data() {
        return {
            subscriptions: window.subscriptions, // Accessing subscriptions from the global window object
        };
    },
});

app.component("subscription-cards", SubscriptionCards);

app.mount("#app");