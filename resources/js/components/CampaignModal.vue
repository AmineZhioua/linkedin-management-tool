<template>
    <div 
        v-if="isOpen" 
        class="fixed top-0 left-0 bg-black bg-opacity-50 p-12 w-[100vw] h-[100vh] z-50 flex items-center justify-center" 
        @click="closeModal"
    >
        <div class="campaign-modal bg-white p-8 m-4 max-h-[90%] max-w-[90%] overflow-y-scroll relative" @click.stop>
            <!-- Close Button -->
            <button 
                @click="closeModal" 
                class="absolute top-4 right-4 text-black text-3xl"
            >
                ×
            </button>
            
            <div class="flex flex-col justify-between w-full gap-4">
                <!-- First Line -->
                <div class="grid grid-cols-3 gap-5 text-center">
                    <div>
                        <label for="name" class="mb-3">Nom de Campagne :</label>
                        <p>{{ campaign.name }}</p>
                    </div>
                    <div>
                        <label for="date-debut" class="mb-3">Date de Début :</label>
                        <p>{{ formattedDate(campaign.start_date) }}</p>
                    </div>
                    <div>
                        <label for="date-fin" class="mb-3">Date de Fin:</label>
                        <p>{{ formattedDate(campaign.end_date) }}</p>
                    </div>
                </div>
                <!-- Second Line -->
                <div class="grid grid-cols-3 gap-5 text-center">
                    <div>
                        <label for="audience" class="mb-3">Audience :</label>
                        <p>{{ campaign.target_audience }}</p>
                    </div>
                    <div>
                        <label for="frequence" class="mb-3">Fréquence par Jour :</label>
                        <p>{{ campaign.frequency_per_day }}</p>
                    </div>
                    <div>
                        <label for="couleur" class="mb-3">Couleur :</label>
                        <p>{{ campaign.color }}</p>
                    </div>
                </div>
            </div>
            <!-- Third Line -->
            <div>
                <label for="description" class="mb-4">Description :</label>
                <p>{{ campaign.description }}</p>
            </div>

            <!-- Post Cards -->
            <div class="flex flex-col gap-2 mt-6">
                <h3>Aperçu des Posts :</h3>
                <div v-if="isLoading" class="text-center">Chargement des posts...</div>
                <div v-else-if="error" class="text-red-500">{{ error }}</div>
                <div v-else-if="posts.length === 0" class="text-center text-gray-500">Aucun post trouvé pour cette campagne.</div>
                <div v-else class="grid grid-cols-3 gap-4">
                    <PostCard 
                        v-for="(post, index) in posts" 
                        :key="index" 
                        :post="post" 
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import PostCard from './PostCard.vue';

export default {
    name: 'CampaignModal',

    components: {
        PostCard,
    },

    props: {
        isOpen: {
            type: Boolean,
            required: true
        },
        campaign: {
            type: Object,
            required: true,
        }
    },

    data() {
        return {
            posts: [],
            isLoading: false,
            error: null,
        }
    },

    watch: {
        isOpen(newValue) {
            if (newValue && this.campaign.id) {
                this.getPostsForCampaign(this.campaign.id);
            }
        }
    },

    methods: {
        formattedDate(date) {
            if (!date || isNaN(new Date(date).getTime())) {
                return 'Date invalide';
            }
            const options = { year: 'numeric', month: 'long', day: '2-digit', hour: '2-digit', minute: '2-digit' };
            return new Date(date).toLocaleDateString('fr-FR', options);
        },

        closeModal() {
            this.$emit('close');
        },

        async getPostsForCampaign(campaignId) {
            if (!campaignId) {
                this.error = "Aucune campagne sélectionnée.";
                return;
            }

            this.isLoading = true;
            this.error = null;
            try {
                const response = await axios.get("/linkedin/get-campaign-posts", {
                    params: { campaign_id: campaignId },
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.status === 200) {
                    this.posts = response.data || [];
                } else if (response.status === 404) {
                    this.posts = [];
                    this.error = "Aucun post trouvé pour cette campagne.";
                } else {
                    this.error = "Erreur inattendue lors de la récupération des posts.";
                }
            } catch (error) {
                console.error("Erreur lors de la récupération des posts:", error);
                this.posts = [];
                this.error = error.response?.status === 401 
                    ? "Non autorisé. Veuillez vous connecter."
                    : "Erreur lors de la récupération des posts.";
            } finally {
                this.isLoading = false;
            }
        }
    }
}
</script>

<style>
.campaign-modal {
    border-radius: 15px;
}
</style>