import { createApp } from "vue";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";
import SubscriptionCards from "./components/SubscriptionCards.vue";
import Popup from "./components/Popup.vue";
import EditAccount from './components/EditAccount.vue';
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
import ExtraInfoForm from "./components/ExtraInfoForm.vue";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import "./echo";
import UserPostsCard from "./components/UserPostsCard.vue";
import LinkedinPost from "./components/LinkedinPost.vue";
import PostPortal from "./components/PostPortal.vue";
import CampaignPortal from "./components/CampaignPortal.vue";
import CampaignPostPortal from "./components/CampaignPostPortal.vue";
import PostView from "./components/PostView.vue";
import CampaignDetails from "./components/CampaignDetails.vue";
import Sidebar from "./components/Sidebar.vue";
import MainSection from "./components/MainSection.vue";
import KpiSection from "./components/KpiSection.vue";
import PostsDatatable from "./components/PostsDatatable.vue";
import CampaignsDatatable from "./components/CampaignsDatatable.vue";
import NotificationsBell from "./components/NotificationsBell.vue";
import CalendarSection from "./components/CalendarSection.vue";


// Importing PrimeVue for UI Components
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';

// PrimeVue Components
import 'primeicons/primeicons.css';
import Button from "primevue/button";
import Select from "primevue/select";
import FloatLabel from "primevue/floatlabel";
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';
import DatePicker from "primevue/datepicker";
import SplitButton from "primevue/splitbutton";
import ScrollPanel from 'primevue/scrollpanel';
import RadioButton from 'primevue/radiobutton';
import RadioButtonGroup from 'primevue/radiobuttongroup';
import ConfirmDialog from "primevue/confirmdialog";
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from "primevue/inputicon";
import Stepper from 'primevue/stepper';
import StepList from 'primevue/steplist';
import StepPanels from 'primevue/steppanels';
import StepItem from 'primevue/stepitem';
import Step from 'primevue/step';
import StepPanel from 'primevue/steppanel';
import Paginator from 'primevue/paginator';
import FileUpload from "primevue/fileupload";
import Galleria from 'primevue/galleria';
import ProgressSpinner from 'primevue/progressspinner'

const app = createApp({
    data() {
        return {
            showEditProfile: false
        };
    }
});

app.use(Toast);
app.use(PrimeVue, {
    theme: {
        preset: Aura
    }
});

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
app.component("edit-account", EditAccount);
app.component("extra-info-form", ExtraInfoForm);
app.component("user-posts-card", UserPostsCard);
app.component("linkedin-post", LinkedinPost);
app.component("post-portal", PostPortal);
app.component("campaign-portal", CampaignPortal);
app.component("campaign-post-portal", CampaignPostPortal);
app.component("post-view", PostView);
app.component("campaign-details", CampaignDetails);
app.component("sidebar", Sidebar);
app.component("main-section", MainSection);
app.component("kpi-section", KpiSection);
app.component("posts-datatable", PostsDatatable);
app.component("campaigns-datatable", CampaignsDatatable);
app.component("notifications-bell", NotificationsBell);
app.component("calendar-section", CalendarSection);

// PrimeVue Components
app.component("Button", Button);
app.component("Select", Select);
app.component("FloatLabel", FloatLabel);
app.component("DataTable", DataTable);
app.component("Column", Column);
app.component("ColumnGroup", ColumnGroup);
app.component("Row", Row);
app.component("DatePicker", DatePicker);
app.component("SplitButton", SplitButton);
app.component("ScrollPanel", ScrollPanel);
app.component("ConfirmDialog", ConfirmDialog);
app.component("RadioButton", RadioButton);
app.component("RadioButtonGroup", RadioButtonGroup);
app.component("InputText", InputText);
app.component("IconField", IconField);
app.component("InputIcon", InputIcon);
app.component("Stepper", Stepper);
app.component("StepList", StepList);
app.component("StepPanels", StepPanels);
app.component("StepItem", StepItem);
app.component("Step", Step);
app.component("StepPanel", StepPanel);
app.component("Paginator", Paginator);
app.component("FileUpload", FileUpload);
app.component("Galleria", Galleria);
app.component("ProgressSpinner", ProgressSpinner);

app.mount("#app");