<template>
    <div class="w-full h-full p-4 overflow-y-scroll">
      <!-- Heading Section -->
      <div class="flex items-center justify-between">
        <!-- Accounts Buttons Sections -->
        <div class="grid grid-cols-3 gap-2">
          <div 
            v-for="linkedinAccount in userLinkedinAccounts" 
            :key="linkedinAccount.id"
            @click="selectAccount(linkedinAccount)"
            class="flex items-center justify-end gap-2 py-2 px-3 rounded-xl cursor-pointer shadow-lg"
            :class="selectedAccount && selectedAccount.id === linkedinAccount.id ? 'bg-gray-500' : 'bg-black'"
          >
            <div class="relative">
              <img 
                v-if="linkedinAccount.linkedin_picture"
                :src="linkedinAccount.linkedin_picture" 
                alt="Linkedin Picture" 
                class="rounded-full"
                height="45" 
                width="45"
              />
              <img 
                v-else
                src="/build/assets/images/default-profile.png"
                alt="Linkedin Picture" 
                class="rounded-full px-0"
                height="45" 
                width="45"
              />
              <div 
                class="p-1 absolute rounded-full bottom-[-3px] right-[-5px]" 
                style="background-color: rgb(23 92 179); border: 3px solid white;"
              >
                <img 
                  src="/build/assets/icons/linkedin.svg" 
                  alt="Linkedin Icon" 
                  height="12"
                  width="12"
                />
              </div>
            </div>
            <div class="flex flex-col ml-2 flex-1">
              <h3 class="fw-semibold text-lg mb-0 text-white">{{ linkedinAccount.linkedin_firstname }} {{ linkedinAccount.linkedin_lastname }}</h3>
              <p class="mb-0 text-sm text-gray-400">Compte personnel</p>
            </div>
          </div>
        </div>
  
        <!-- New Post & Campaign Buttons Section -->
        <div class="flex items-center gap-2">
            <Button 
                @click="openPostPortal" 
                class="cursor-pointer bg-black text-white px-3 py-2 rounded-md flex items-center gap-2"
                style="border: 1px solid pink;"
            >
                <img src="/build/assets/icons/add-white.svg" alt="Add Icon" />
                <span>Nouveau Post</span>
            </Button>
            <Button 
                @click="openCampaignPortal"
                class="cursor-pointer bg-black text-white px-4 py-2 rounded-md flex items-center gap-2"
                style="border: 1px solid pink;"
            >
                <img src="/build/assets/icons/add-white.svg" alt="Add Icon" />
                <span>Nouveau Campagne</span>
            </Button>
            </div>
        </div>
    
      <!-- En Attente & Brouillons & Publié & Echoué Buttons -->
      <div class="flex items-center justify-between px-2 mt-4" style="border-bottom: 1px solid #ddd;">
        <div class="grid grid-cols-4 gap-2">
          <button 
            @click="filterPostsByStatus('queued')"
            class="flex items-center justify-center gap-2 py-4 px-1 fw-semibold"
            style="border-top-left-radius: 8px; border-top-right-radius: 8px;"
            :class="isStatusSelected('queued') ? 'btn-active' : 'btn-inactive'"
          >
            <span>En Attente</span>
            <span class="px-2 bg-gray-300 rounded-full">{{ queuedPostsCount }}</span>
          </button>
          <button 
            @click="filterPostsByStatus('draft')"
            class="flex items-center justify-center gap-2 py-4 px-1 fw-semibold"
            style="border-top-left-radius: 8px; border-top-right-radius: 8px;"
            :class="isStatusSelected('draft') ? 'btn-active' : 'btn-inactive'"
          >
            <span>Brouillons</span>
            <span class="px-2 bg-gray-300 rounded-full">{{ draftPostsCount }}</span>
          </button>
          <button 
            @click="filterPostsByStatus('posted')"
            class="flex items-center justify-center gap-2 py-4 px-1 fw-semibold"
            style="border-top-left-radius: 8px; border-top-right-radius: 8px;"
            :class="isStatusSelected('posted') ? 'btn-active' : 'btn-inactive'"
          >
            <span>Publié</span>
            <span class="px-2 bg-gray-300 rounded-full">{{ postedPostsCount }}</span>
          </button>
          <button 
            @click="filterPostsByStatus('failed')"
            class="flex items-center justify-center gap-2 py-4 px-1 fw-semibold"
            style="border-top-left-radius: 8px; border-top-right-radius: 8px;"
            :class="isStatusSelected('failed') ? 'btn-active' : 'btn-inactive'"
          >
            <span>Echoué</span>
            <span class="px-2 bg-gray-300 rounded-full">{{ failedPostsCount }}</span>
          </button>
        </div>
        <div class="flex items-center gap-4">
          <FloatLabel class="w-full md:w-40" variant="over">
            <Select 
              v-model="view" 
              inputId="over_label" 
              :options="views" 
              optionLabel="name" 
              class="w-full" 
              variant="filled"
              style="background-color: black;"
            >
            </Select>
            <label for="over_label">vue</label>
          </FloatLabel>
        </div>
      </div>
  
      <!-- Posts Cards Container -->
        <div class="flex flex-col justify-between h-full relative" v-if="view && view.name === 'Cartes'">
            <div v-if="filteredPosts.length === 0" class="flex items-center justify-center">
          <svg viewBox="0 0 200 200" width="220" height="220" xmlns="http://www.w3.org/2000/svg">
            <path d="M50 120 Q60 70 100 70 Q140 70 150 120 Q150 130 140 130 Q130 130 125 120 Q120 130 110 130 Q100 130 95 120 Q90 130 80 130 Q70 130 65 120 Q60 130 50 130 Q50 125 50 120Z" fill="#F9F9F9" stroke="#CCC" stroke-width="2"/>
            <circle cx="80" cy="100" r="5" fill="#333"/>
            <circle cx="120" cy="100" r="5" fill="#333"/>
            <path d="M75 90 Q80 85 85 90" stroke="#333" stroke-width="2" fill="none"/>
            <path d="M115 90 Q120 85 125 90" stroke="#333" stroke-width="2" fill="none"/>
            <circle cx="60" cy="60" r="12" fill="none" stroke="#555" stroke-width="3"/>
            <line x1="68" y1="68" x2="75" y2="75" stroke="#555" stroke-width="3"/>
            <path d="M90 110 Q100 115 110 110" stroke="#333" stroke-width="2" fill="none"/>
            <ellipse cx="100" cy="140" rx="40" ry="6" fill="#eee"/>
            <text x="50%" y="180" text-anchor="middle" font-size="14" fill="#666">
              Pas des Posts...
            </text>
          </svg>
            </div>
            <div class="grid grid-cols-4 gap-3 w-full pb-3 mt-4">
                <linkedin-post 
                    v-for="post in paginatedPosts" 
                    :key="post.id" 
                    :user-linkedin-accounts="userLinkedinAccounts" 
                    :post="post" 
                    @edit-post="editPost"
                    @delete-post="deletePost"
                    @delete-post-from-linkedin="deletePostFromLinkedIn"
                />
            </div>
            <div class="py-2">
                <Paginator 
                    :rows="rowsPerPage" 
                    :totalRecords="filteredPosts.length" 
                    :rowsPerPageOptions="[10, 20, 30]"
                    @page="onPageChange"
                ></Paginator>
            </div>
        </div>
  
      <!-- DataTable for Tableau view -->
      <div v-if="view && view.name === 'Tableau'" class="flex flex-col">
        <posts-datatable 
          :linkedin-posts="userLinkedinPosts"
          :campaigns="campaigns"
          :linkedin-accounts="userLinkedinAccounts"
          @edit-linkedin-post="editPost"
          @delete-post="deletePost"
          @delete-post-from-linkedin="deletePostFromLinkedIn"
          @request-boost-interaction="emitBoostData"
          :open-post-read-mode="openPostInReadMode"
        />
      </div>
  
      <!-- Campaigns DataTable -->
      <div v-if="view && view.name === 'Tableau'" class="flex flex-col">
        <campaigns-datatable 
          :linkedin-posts="userLinkedinPosts"
          :campaigns="campaigns"
          :linkedin-accounts="userLinkedinAccounts" 
          @delete-campaign="deleteCampaign"
          @edit-campaign="handleEditCampaign"
          :open-campaign-read-mode="openCampaignInReadMode"
        />
      </div>
    </div>
  </template>
  
<script>
  import axios from 'axios';
  import Swal from 'sweetalert2';
  import { useToast } from "vue-toastification";
  
  export default {
    name: 'UserPostsCard',
  
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
        required: false,
        default: () => []
      },
    },

    emits: ['open-post-portal', 'open-boost-form', 'open-campaign-portal', 'open-campaign-to-edit'],
  
    setup() {
      const toast = useToast();
      return { toast }
    },
  
    data() {
      return {
        view: { name: 'Tableau' },
        views: [
          { name: 'Tableau' },
          { name: 'Cartes' },
        ],
        postStatus: "queued",
        selectedAccount: null,
        currentPage: 0,
        rowsPerPage: 10,
      };
    },
  
    computed: {
      filteredPosts() {
        return this.userLinkedinPosts.filter(post => post.status === this.postStatus);
      },
      queuedPostsCount() {
        return this.userLinkedinPosts.filter(post => post.status === 'queued').length;
      },
      draftPostsCount() {
        return this.userLinkedinPosts.filter(post => post.status === 'draft').length;
      },
      postedPostsCount() {
        return this.userLinkedinPosts.filter(post => post.status === 'posted').length;
      },
      failedPostsCount() {
        return this.userLinkedinPosts.filter(post => post.status === 'failed').length;
      },
      paginatedPosts() {
        const start = this.currentPage * this.rowsPerPage;
        const end = start + this.rowsPerPage;
        return this.filteredPosts.slice(start, end);
      },
    },
  
    watch: {
      postStatus() {
        this.currentPage = 0;
      },
    },
  
    methods: {
      onPageChange(event) {
        this.currentPage = event.page;
        this.rowsPerPage = event.rows;
      },
  
      editPost(post) {
        this.$emit('open-post-portal', { post, readMode: false });
      },

      emitBoostData(post) {
        this.$emit('open-boost-form', post);
      },
  
      async requestBoost(post) {
        try {
          const boostData = new FormData();
          const postUrl = `https://www.linkedin.com/feed/update/${post.post_urn}`;
          boostData.append("post_id", post.id);
          boostData.append("linkedin_user_id", post.linkedin_user_id);
          boostData.append("post_url", postUrl);
  
          const boostResponse = await axios.post("/boost-interaction/request", boostData, {
            headers: {
              "Content-Type": "multipart/form-data",
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
          });
  
          if (boostResponse.status === 201) {
            this.toast.success("La requête de Boost a été envoyé avec succès!");
          } else if (boostResponse.status === 404) {
            this.toast.error("Post non trouvé !");
          }
        } catch (error) {
          console.error(error);
          this.toast.error("Une erreur s'est produite lors de l'envoi de la requête !");
        }
      },
  
      async deletePost(postId) {
        const result = await Swal.fire({
          title: "Vous êtes sûr ?",
          text: "Vous ne pourrez pas revenir en arrière !",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Oui, supprimer !",
          cancelButtonText: "Annuler"
        });
  
        if (result.isConfirmed) {
          try {
            const post = this.userLinkedinPosts.find(p => p.id === postId);
            const response = await axios.delete("/linkedin/delete-post", {
              data: {
                post_id: postId,
                linkedin_user_id: post ? post.linkedin_user_id : null,
              },
              headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
              },
            });
  
            if (response.status === 200) {
              await Swal.fire({
                title: "Supprimé !",
                text: "Votre post a été supprimé.",
                icon: "success"
              });
              window.location.reload();
            } else {
              this.toast.error("Une erreur s'est produite lors de la suppression !");
            }
          } catch (error) {
              console.error("Error deleting post:", error);
              await Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Une erreur s'est produite lors de la suppression du post !",
              });
          }
        }
      },
  
      async deletePostFromLinkedIn(post) {
        const result = await Swal.fire({
          title: "Vous êtes sûr ?",
          text: "Vous ne pourrez pas revenir en arrière !",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Oui, supprimer !",
          cancelButtonText: "Annuler"
        });
  
        if (result.isConfirmed) {
          try {
            const urnId = post.post_urn.split(':')[3];
            const deleteResponse = await axios.delete("/linkedin/post/delete", {
              data: {
                post_id: post.id,
                linkedin_user_id: post.linkedin_user_id,
                urn_id: urnId
              },
              headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
              }
            });
  
            if (deleteResponse.status === 200) {
              await Swal.fire({
                title: "Supprimé !",
                text: "Votre post a été supprimé.",
                icon: "success"
              });
              window.location.reload();
            }
          } catch (error) {
            console.error(error);
            await Swal.fire({
              icon: "error",
              title: "Oops...",
              text: "Une erreur s'est produite lors de la suppression du post !",
            });
          }
        }
      },
  
      async deleteCampaign(campaignId) {
        const result = await Swal.fire({
          title: "Vous êtes sûr ?",
          text: "Vous ne pourrez pas revenir en arrière !",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Oui, supprimer !",
          cancelButtonText: "Annuler"
        });
  
        if (result.isConfirmed) {
          try {
            const response = await axios.delete('/campaign/delete', {
              data: {
                campaign_id: campaignId,
              },
              headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
              },
            });
  
            if (response.status === 200) {
              await Swal.fire({
                title: "Campagne supprimé !",
                text: "Votre campagne a été supprimé avec succès.",
                icon: "success"
              });
              window.location.reload();
            } else {
              this.toast.error("Une erreur s'est produite lors de la suppression !");
            }
          } catch (error) {
            console.error("Error deleting campaign:", error);
            Swal.fire({
              icon: "error",
              title: "Oops...",
              text: "Une erreur s'est produite lors de la suppression de la campagne !",
            });
          }
        }
      },

      handleEditCampaign(campaign, campaignOwner) {
        this.$emit('open-campaign-to-edit', campaign, campaignOwner);
      },
  
      getLinkedinUserByID(id) {
        return this.userLinkedinAccounts.find(account => account.id === id);
      },
  
      getPostByID(id) {
        return this.userLinkedinPosts.find(post => post.id === id);
      },
  
      getCampaignByID(id) {
        return this.campaigns.find(campaign => campaign.id === id);
      },
  
      selectAccount(account) {
        this.selectedAccount = account;
      },
  
      filterPostsByStatus(status) {
        this.postStatus = status;
      },

      isStatusSelected(status) {
        return this.postStatus === status;
      },
  
      openPostInReadMode(id) {
        const post = this.getPostByID(id);
        this.$emit('open-post-portal', { post, readMode: true });
      },
  
      openCampaignInReadMode(linkedin_id, campaign_id) {
        const selectedAccount = this.getLinkedinUserByID(linkedin_id);
        const selectedCampaign = this.getCampaignByID(campaign_id);
        if (selectedAccount && selectedCampaign) {
          this.$emit('open-campaign-portal', {
            account: selectedAccount,
            campaign: selectedCampaign,
            readMode: true
          });
        }
      },
  
      openPostPortal() {
        this.$emit('open-post-portal', { post: null, readMode: false });
      },
  
      openCampaignPortal() {
        if (this.selectedAccount) {
          this.$emit('open-campaign-portal', { account: this.selectedAccount, campaign: null, readMode: false });
        } else {
          Swal.fire({
            title: "<h3 class='text-black fw-semibold'>Compte?</h3>",
            html: "<p class='text-muted fw-light text-md mb-0'>Veuillez choisir un compte tout d'abord</p>",
            icon: "warning",
          });
        }
      },
    },
  }
</script>

<style scoped>
.btn-active {
    background: linear-gradient(
        135deg,
        rgb(255 16 185) 0%,
        rgb(255 125 82) 100%
    );
    font-weight: 600;
    box-shadow: 0 2px 5px rgba(79, 70, 229, 0.3);
    transition: all 0.3s ease;
    font-size: 0.9rem;
    color: white;
}
.btn-inactive {
    color: #6b7280;
    font-weight: 500;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    color: black;
}
.btn-inactive:hover {
    background: rgba(255, 16, 183, 0.236);
    color: white;
}
::-webkit-scrollbar {
    width: 6px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
.no-scroll {
    overflow: hidden;
}
::-webkit-scrollbar-thumb {
    background: transparent;
    border-radius: 1px;
}
</style>