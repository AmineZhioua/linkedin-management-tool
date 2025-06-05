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
      @open-boosted-post="handleOpenBoostedPost"
      @edit-boost-request="handleEditBoostRequest"
    />
  </div>
</template>


<script>
export default {
  name: 'LinkedinAccountsSection',

  props: {
    allUserBoostRequests: {
      type: Array,
      required: true,
    },

    userLinkedinAccounts: {
      type: Array,
      required: true,
    },
  },

  emits: ['open-post-to-boost', 'open-boost-form-editmode'],

  data() {
    return {};
  },
 
  methods: {
    handleOpenBoostedPost(postId) {
      this.$emit('open-post-to-boost', postId);
    },

    handleEditBoostRequest(boostRequest) {
      this.$emit('open-boost-form-editmode', boostRequest);
    },
  },
};
</script>


<style scoped>
</style>