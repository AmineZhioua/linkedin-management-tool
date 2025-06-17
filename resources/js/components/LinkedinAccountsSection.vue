
<template>
  <div class="w-full h-full p-4 overflow-y-scroll">
    <!-- Title & Icon -->
    <div class="flex flex-col">
      <div class="flex items-center gap-3 text-black">
          <img 
              src="/build/assets/icons/linkedin-black.svg" 
              alt="KPI Icon" 
              class="mb-1"
              height="45"
              width="45"
          />
          <h1 class="mb-0 fw-semibold">Espace Linkedin</h1>
      </div>
      <p class="mt-2 text-lg text-muted">
        Votre centre de commande ! 
        Boostez vos publications en un clic et gérer vos compte LinkedIn !
      </p>
    </div>

    <!-- Datatable for the Boost Requests -->
    <boost-request-datatable 
      :boost-requests="allUserBoostRequests"
      :linkedin-accounts="userLinkedinAccounts"
      :subscription-data="subscriptionData"
      @open-boosted-post="handleOpenBoostedPost"
      @edit-boost-request="handleEditBoostRequest"
      @refresh-data="handleRefreshData"
    />

    <users-datatable 
      :user-linkedin-accounts="userLinkedinAccounts"
    />
  </div>
</template>

<script>
import BoostRequestDatatable from './BoostRequestDatatable.vue';
import UsersDatatable from './UsersDatatable.vue'; // Adjust path if needed
import { useToast } from 'vue-toastification';

export default {
  name: 'LinkedinAccountsSection',

  components: {
    BoostRequestDatatable,
    UsersDatatable
  },

  props: {
    allUserBoostRequests: {
      type: Array,
      required: true,
    },
    userLinkedinAccounts: {
      type: Array,
      required: true,
    },
    subscriptionData: {
      type: Object,
      required: true,
      default: () => ({ boost_likes: 0, boost_comments: 0 })
    }
  },

  emits: ['open-post-to-boost', 'open-boost-form-editmode'],

  setup() {
    const toast = useToast();
    return { toast };
  },

  data() {
    return {};
  },

  mounted() {
    console.log('LinkedinAccountsSection Subscription Data:', this.subscriptionData);
  },

  methods: {
    handleOpenBoostedPost(postId) {
      this.$emit('open-post-to-boost', postId);
    },

    handleEditBoostRequest(boostRequest) {
      this.$emit('open-boost-form-editmode', boostRequest);
    },

    handleRefreshData() {
      window.location.reload(); // Simple reload to refresh data
      // Alternatively, emit an event to parent or fetch updated data via API
    }
  },
};
</script>

<style scoped>
.fw-semibold {
  font-weight: 600;
}
.text-muted {
  color: #6c757d;
}
</style>