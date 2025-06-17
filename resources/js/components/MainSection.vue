<template>
  <div class="w-full h-[100vh] flex relative">
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
      @open-campaign-portal="handleOpenCampaignPortal"
      @open-post-portal="handleOpenPostPortal"
      @open-campaign-post-portal="handleOpenCampaignPostPortal"
      @open-boost-form="openBoostForm"
      @open-campaign-to-edit="openCampaignPortalForEdit"
    />

    <!-- KPI Section -->
    <kpi-section
      v-if="cardToSet === 'KPIs'"
      :all-linkedin-accounts="userLinkedinAccounts"
      :all-linkedin-posts="userLinkedinPosts"
      :all-campaigns="campaigns"
      @open-ai-recommend="handleAIRecommend"
    /> 

    <!-- LinkedIn Accounts Section -->
    <linkedin-accounts-section
      v-if="cardToSet === 'linkedinAccounts'"
      :all-user-boost-requests="userBoostRequests"
      :user-linkedin-accounts="userLinkedinAccounts"
      @open-post-to-boost="handleOpenPostToBeBoosted"
      @open-boost-form-editmode="handleOpenBoostFormInEditMode"
    />

    <!-- Calendar Section -->
    <calendar-section 
      v-if="cardToSet === 'calendar'"
      :user-linkedin-accounts="userLinkedinAccounts"
      :all-linkedin-posts="userLinkedinPosts"
      :all-campaigns="campaigns"
      @open-campaign-portal="handleOpenCampaignPortal"
      @open-post-portal="handleOpenPostPortal"
      @open-campaign-post-portal="handleOpenCampaignPostPortal"
      @open-campaign-read-mode="handleCampaignReadMode"
      @open-post-read-mode="handleOpenPostPortal"
      @open-campaign-portal-edit-mode="openCampaignPortalForEdit"
      @open-post-portal-edit-mode="handleOpenPostPortal"
    />

    <commhub-section
      v-if="cardToSet === 'commhub'"
      :user-linkedin-accounts="userLinkedinAccounts"
      :all-linkedin-posts="userLinkedinPosts"
      @open-ai-comment-portal="handleOpenAICommentPortal"
    />

    <!-- Centralized Portal Components -->
    <campaign-portal
      v-if="showCampaignPortal"
      ref="campaignPortal"
      :selected-account="selectedAccount"
      :read-mode="readModeStatus"
      :edit-mode="campaignEditMode"
      :selected-campaign="selectedCampaign"
      :linkedin-posts="userLinkedinPosts"
      @campaign-post-editing="editCampaignPost"
      @posts-generated="handlePostsGenerated"
      @dates-updated="updateCampaignDates"
      @update:form-data="updateFormData"
      @validate="isFormValid = $event"
      @close-campaign-portal="closeCampaignPortal"
    />

    <post-portal 
      v-if="showPortal"
      :all-linkedin-accounts="userLinkedinAccounts" 
      :selected-post="selectedPost"
      :read-mode="readModeStatus"
      @close="closePostPortal"
      @close-readmode="closePostPortalReadMode"
    />

    <campaign-post-portal 
      v-if="showCampaignPostPortal"
      :selected-account="selectedAccount"
      :selected-post="selectedPost"
      :all-linkedin-accounts="userLinkedinAccounts"
      :campaign-post-error="campaignPostError"
      :on-save="saveChanges"
      @close="closeCampaignPostPortal"
    />

    <boost-request-form 
      v-if="displayBoostForm"
      :post="postToBoost"
      :boost-request="boostRequestToUpdate"
      @close-boost-form="closeBoostForm"
    />

    <ai-comment-portal
      v-if="showAICommentPortal"
      @close-ai-comment-portal="showAICommentPortal = false"
    />

    <ai-guide
      v-if="showAIRecommend"
      :user-linkedin-accounts="userLinkedinAccounts"
      :all-linkedin-posts="userLinkedinPosts"
      :all-campaigns="campaigns"
      @open-campaign-read-mode="handleCampaignReadMode"
      @close-ai-recommend="handleCloseAIRecommend"
    />

    <alerts-bell />

  </div>
</template>

<script>
import Sidebar from "./Sidebar.vue";
import UserPostsCard from "./UserPostsCard.vue";
import KpiSection from "./KpiSection.vue";
import CalendarSection from "./CalendarSection.vue";
import LinkedinAccountsSection from "./LinkedinAccountsSection.vue";
import { useToast } from "vue-toastification";
import { getLinkedinUserByID } from '../services/datatables';

export default {
  name: "MainSection",

  components: {
    Sidebar,
    UserPostsCard,
    KpiSection,
    LinkedinAccountsSection,
    CalendarSection,
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

    userBoostRequests: {
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

  },

  setup() {
    const toast = useToast();
    return { toast };
  },

  data() {
    return {
      cardToSet: "linkedinAccounts",
      // Portals variables
      showCampaignPostPortal: false,
      showCampaignPortal: false,
      showPortal: false,
      // Selection variables
      selectedCampaign: null,
      selectedPost: null,
      selectedAccount: null,
      // Read mode
      readModeStatus: false,
      // Error variables
      isFormValid: false,
      campaignPostError: '',
      // Campaign related variables
      campaignPosts: [],
      campaignStartDateTime: null,
      campaignEndDateTime: null,
      selectedCible: '',
      frequenceParJour: '',
      descriptionCampagne: '',
      couleurCampagne: '',
      nomCampagne: '',
      campaignEditMode: false,
      // Boost Request variables
      nbLikesToRequest: 0,
      nbCommentsToRequest: 0,
      boostMessage: '',
      displayBoostForm: false,
      postToBoost: null,
      boostRequestToUpdate: null,
      // AI Comment Portal variables
      showAICommentPortal: false,
      showAIRecommend: false,
    };
  },

  methods: {
    getLinkedinUserByID,

    setCard(card) {
      this.cardToSet = card;
    },

    // Event Handlers for Portal Open Requests
    handleOpenCampaignPortal(data) {
      this.selectedAccount = data.account;
      this.selectedCampaign = data.campaign || null;
      this.readModeStatus = data.readMode || false;
      this.showCampaignPortal = true;
    },

    handleOpenPostPortal(data) {
      this.selectedPost = data.post;
      this.readModeStatus = data.readMode || false;
      this.selectedAccount = data.account || null;
      this.showPortal = true;
      console.log(this.selectedPost)
    },

    // AI RECOMMENDATION PORTAL FUNCTIONS
    handleAIRecommend() {
      this.showAIRecommend = true;
    },

    handleCloseAIRecommend() {
      this.showAIRecommend = false;
    },

    handleOpenCampaignPostPortal(data) {
      this.selectedAccount = data.account;
      this.selectedPost = data.post;
      this.showCampaignPostPortal = true;
    },

    openCampaignPortalForEdit(campaign, campaignOwner) {
      this.showCampaignPortal = true;
      this.selectedAccount = campaignOwner;
      this.selectedCampaign = campaign;
      this.campaignEditMode = true;
    },

    handleCampaignReadMode(selectedAccount, selectedCampaign) {
      this.selectedAccount = selectedAccount;
      this.selectedCampaign = selectedCampaign;
      this.readModeStatus = true;
      this.showCampaignPortal = true;
    },

    handlePostReadMode(selectedAccount, post) {
      this.selectedAccount = selectedAccount;
      this.selectedPost = post;
      this.readModeStatus = true;
      this.showPortal = true;
    },

    openBoostForm(post) {
      this.displayBoostForm = true;
      this.postToBoost = post;
      this.boostRequestToUpdate = null; // Explicitly null for create mode
    },

    closeBoostForm() {
      this.displayBoostForm = false;
      this.postToBoost = null;
      this.boostRequestToUpdate = null; // Reset to null
    },

    handleOpenPostToBeBoosted(postId) {
      const post = this.userLinkedinPosts.find(post => post.id === postId);

      if(post) {
        this.selectedPost = post;
        const account = this.userLinkedinAccounts.find(account => account.id === post.user_id);
        this.selectedAccount = account;
        this.readModeStatus = true;
        this.showPortal = true;
      } else {
        this.toast.error("Post non trouvé !");
      }
    },

    handleOpenBoostFormInEditMode(boostRequest) {
      this.displayBoostForm = true;
      this.boostRequestToUpdate = boostRequest;
      const post = this.userLinkedinPosts.find(post => post.id === boostRequest.post_id);
      this.postToBoost = post;
    },

    handleOpenAICommentPortal() {
      this.showAICommentPortal = true;
    },

    // Portal Close Handlers
    closeCampaignPortal() {
      this.showCampaignPortal = false;
      this.selectedCampaign = null;
      this.readModeStatus = false;
      this.selectedAccount = null;
    },

    closePostPortal() {
      this.showPortal = false;
      this.selectedPost = null;
    },

    closePostPortalReadMode() {
      this.showPortal = false;
      this.selectedPost = null;
      this.readModeStatus = false;
    },

    closeCampaignPostPortal() {
      this.showCampaignPostPortal = false;
      this.selectedPost = null;
      this.campaignPostError = '';
    },

    editCampaignPost(post) {
      this.selectedPost = { ...post };
      this.showCampaignPostPortal = true;
    },

    handlePostsGenerated(posts) {
      this.campaignPosts = posts.map(post => ({ ...post }));
    },

    updateCampaignDates({ startDate, endDate }) {
      this.campaignStartDateTime = startDate;
      this.campaignEndDateTime = endDate;
    },

    updateFormData(formData) {
      this.campaignStartDateTime = formData.startDate;
      this.campaignEndDateTime = formData.endDate;
      this.selectedCible = formData.selectedCible;
      this.frequenceParJour = formData.frequenceParJour;
      this.descriptionCampagne = formData.descriptionCampagne;
      this.couleurCampagne = formData.couleurCampagne;
      this.nomCampagne = formData.nomCampagne;
    },

    saveChanges(updatedPost) {
      const now = new Date();
      const scheduled = new Date(updatedPost.scheduledDateTime);
      const start = new Date(this.campaignStartDateTime);
      const end = new Date(this.campaignEndDateTime);

      if (scheduled < now) {
          this.campaignPostError = "La date de publication ne peut pas être dans le passé!";
          return;
      }
      if (scheduled < start || scheduled > end) {
          this.campaignPostError = `La date de publication doit être comprise entre ${this.campaignDateTimeOutput(this.campaignStartDateTime)} et ${this.campaignDateTimeOutput(this.campaignEndDateTime)} !`;
          return;
      }

      if (updatedPost.type === "text" && updatedPost.content.caption.trim() === "") {
          this.campaignPostError = "Le contenu du post ne peut pas être vide !";
          return;
      } else if (updatedPost.type === "text" && updatedPost.content.caption.length > 3000) {
          this.campaignPostError = "Le contenu du post ne peut pas dépasser 3000 caractères !";
          return;
      } else if (updatedPost.type === "text" && updatedPost.content.caption.length < 50) {
          this.campaignPostError = "Le contenu du post doit contenir au moins 50 caractères !";
          return;
      }
      if ((updatedPost.type === "image" || updatedPost.type === "video") && !updatedPost.content.file && !updatedPost.content.file_path) {
          this.campaignPostError = "Veuillez sélectionner un fichier pour le publier !";
          return;
      }
      if (updatedPost.type === "article") {
          const { url, title } = updatedPost.content;
          if (!url.trim() || !title.trim()) {
              this.campaignPostError = "Veuillez remplir au moins l'URL et le titre de l'article.";
              return;
          } else if (!url.startsWith("https://")) {
              this.campaignPostError = "L'URL de l'article doit commencer par 'https://'";
              return;
          } else if (title.length > 200) {
              this.campaignPostError = "Le titre de l'article ne peut pas dépasser 200 caractères !";
              return;
          } else if (title.length < 5) {
              this.campaignPostError = "Le titre de l'article doit contenir au moins 5 caractères !";
              return;
          }
      }

      // Deep copy the updated post
      const postToSave = {
          id: updatedPost.id,
          tempId: updatedPost.tempId || `temp-${Date.now()}`,
          job_id: updatedPost.job_id,
          scheduledDateTime: updatedPost.scheduledDateTime,
          type: updatedPost.type,
          content: {
              text: updatedPost.content?.text || '',
              caption: updatedPost.content?.caption || '',
              url: updatedPost.content?.url || '',
              title: updatedPost.content?.title || '',
              description: updatedPost.content?.description || '',
              file_path: updatedPost.content?.file_path || '',
              files: Array.isArray(updatedPost.content?.files) ? updatedPost.content.files.map(file => file) : [], // Preserve File objects
              original_filenames: Array.isArray(updatedPost.content?.original_filenames) ? [...updatedPost.content.original_filenames] : [],
              file: updatedPost.content?.file || null,
              fileName: updatedPost.content?.fileName || (updatedPost.content?.file?.name || null),
          },
          accountId: updatedPost.accountId,
          isExisting: updatedPost.isExisting || false,
      };

      const index = this.campaignPosts.findIndex(p => p.tempId === postToSave.tempId);
      if (index !== -1) {
          this.campaignPosts.splice(index, 1, { ...postToSave });
          if (this.$refs.campaignPortal) {
              this.$refs.campaignPortal.updatePost({ ...postToSave });
          }
      } else {
          console.error("Post not found in campaignPosts");
      }

      this.showCampaignPostPortal = false;
      this.selectedPost = null;
    },

    campaignDateTimeOutput(date) {
      const dateTime = new Date(date);
      const year = dateTime.getFullYear();
      const month = String(dateTime.getMonth() + 1).padStart(2, '0');
      const day = String(dateTime.getDate()).padStart(2, '0');
      const hours = String(dateTime.getHours()).padStart(2, '0');
      const minutes = String(dateTime.getMinutes()).padStart(2, '0');
      return `${month}/${day}/${year} ${hours}:${minutes}`;
    },
  },
};
</script>