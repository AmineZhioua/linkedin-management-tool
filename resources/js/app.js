import { createApp } from "vue";
import HomePage from "./components/HomePage.vue";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";
const app = createApp({});
app.component("home-page", HomePage);
app.mount("#app");
