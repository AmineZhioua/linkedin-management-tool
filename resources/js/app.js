import { createApp } from "vue";
import HomePage from "./components/HomePage.vue";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";


const app = createApp({
    components: {
        HomePage,
    }
});

app.mount("#app");