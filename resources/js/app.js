import { createApp } from "vue";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";
import SubscriptionCards from "./components/SubscriptionCards.vue";
import Popup from "./components/Popup.vue";
import PlateformeCard from "./components/PlateformeCard.vue";
import LoadingOverlay from "./components/LoadingOverlay.vue";
import ProgressIndicator from "./components/ProgressIndicator.vue";
import LinkedinPostCard from "./components/LinkedinPostCard.vue";
import PlateformeStart from "./components/PlateformeStart.vue";
import MarqueComponent from "./components/Marque.vue";
import MonthCalendar from "./components/MonthCalendar.vue";
import PostModal from "./components/PostModal.vue";
import CalendarNavigation from "./components/CalendarNavigation.vue";
import CampaignForm from "./components/CampaignForm.vue";
import DashboardCalendar from "./components/DashboardCalendar.vue";
import CampaignTable from "./components/CampaignTable.vue";
import CampaignModal from "./components/CampaignModal.vue";
import PostCard from "./components/PostCard.vue";
import KpiList from "./components/KpiList.vue";
import NotificationCard from "./components/NotificationCard.vue";
import "./echo"

const app = createApp();

app.component("subscription-cards", SubscriptionCards);
app.component("popup", Popup);
app.component("plateforme-card", PlateformeCard);
app.component("loading-overlay", LoadingOverlay);
app.component("progress-indicator", ProgressIndicator);
app.component("linkedin-postcard", LinkedinPostCard);
app.component("plateforme-start", PlateformeStart);
app.component("marque-component", MarqueComponent);
app.component("month-calendar", MonthCalendar);
app.component("post-modal", PostModal);
app.component("calendar-navigation", CalendarNavigation);
app.component("campaign-form", CampaignForm);
app.component("dashboard-calendar", DashboardCalendar);
app.component("campaign-table", CampaignTable);
app.component("campaign-modal", CampaignModal);
app.component("post-card", PostCard);
app.component("kpi-list", KpiList);
app.component("notification-card", NotificationCard);

app.mount("#app");
