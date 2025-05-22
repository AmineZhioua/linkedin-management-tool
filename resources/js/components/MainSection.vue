<template>
  <div class="w-full h-full flex">
    <!-- Sidebar Component -->
    <sidebar 
      @card-to-set="setCard"
    />

    <!-- User Posts Card -->
    <user-posts-card 
      v-if="cardToSet === 'userPosts'"
      :user-linkedin-accounts="userLinkedinAccounts" 
      :user-linkedin-posts="userLinkedinPosts" 
      :campaigns="campaigns" 
    />

    <!-- KPI Section -->
    <kpi-section
      v-if="cardToSet === 'KPIs'"
      :all-linkedin-accounts="userLinkedinAccounts"
      :all-linkedin-posts="userLinkedinPosts"
      :all-campaigns="campaigns"
    /> 

    <!-- LinkedIn Accounts Section -->
    <linkedin-accounts-section
      v-if="cardToSet === 'linkedinAccounts'"
      :user="user"
      :all-linkedin-accounts="userLinkedinAccounts"
      :flash-success="flashSuccess"
      :flash-error="flashError"
      :success-payment="successPayment"
    />
  </div>
</template>

<script>
import Sidebar from "./Sidebar.vue";
import UserPostsCard from "./UserPostsCard.vue";
import KpiSection from "./KpiSection.vue";
import LinkedinAccountsSection from "./LinkedinAccountsSection.vue";

export default {
  name: "MainSection",

  components: {
    Sidebar,
    UserPostsCard,
    KpiSection,
    LinkedinAccountsSection,
  },

  props: {
    userLinkedinAccounts: {
      type: Array,
      required: true,
    },
    userLinkedinPosts: {
      type: Array,
      required: true,
    },
    campaigns: {
      type: Array,
      required: true,
    },
    user: {
      type: Object,
      required: false,
      default: null,
    },
    flashSuccess: {
      type: String,
      default: "",
    },
    flashError: {
      type: String,
      default: "",
    },
    successPayment: {
      type: String,
      default: "",
    },
  },

  data() {
    return {
      cardToSet: "KPIs", // Default view is KPIs
    };
  },

  methods: {
    setCard(card) {
      this.cardToSet = card; // Update the active card based on sidebar selection
    },
  },
};
</script>