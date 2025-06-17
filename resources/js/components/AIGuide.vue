<template>
    <div class="bg-black bg-opacity-50 inset-0 h-[100vh] w-full absolute"></div>
    <div class="flex items-center w-full p-4 justify-center gap-2 absolute top-[50%] left-[50%] translate-y-[-50%] translate-x-[-50%]">
        <div 
            v-if="currentStep === 1"
            class="flex flex-col gap-4 py-4 px-3 rounded-lg max-w-[500px]"
            style="background-color: #18181b;"
        >
            <!-- Heading & Title -->
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-full bg-white text-black">
                        <i class="fa-solid fa-robot text-3xl"></i>
                    </div>
                    <h3 class="mb-0 fw-semibold mt-1 text-white">Assistant IA</h3>
                </div>
                <button @click="handleClose">
                    <img src="/build/assets/icons/close-white.svg" alt="Close Icon" height="22" width="22" />
                </button>
            </div>
            <!-- Paragraph -->
            <p class="text-sm text-gray-200 mt-2 mb-0">
                Votre assistant IA est là pour vous aider à améliorer votre contenu et présence sur LinkedIn en analysant vos données.<br>
                Veuillez choisir un option :
            </p>

            <div class="flex items-center justify-between w-full">
                <button 
                    @click="selectTypeForRecommend('post')"
                    class="py-2 px-3 bg-white rounded-lg text-black fw-semibold flex gap-2"
                >
                    <img src="/build/assets/icons/posts-black.svg" alt="">
                    <p class="mb-0">Post</p>
                </button>
                <button 
                    @click="selectTypeForRecommend('campaign')"
                    class="p-2 bg-white rounded-lg text-black fw-semibold flex gap-2"
                >
                    <img src="/build/assets/icons/campaign_black.svg" alt="">
                    <p class="mb-0">Campagne</p>
                </button>
            </div>
        </div>

        <!-- If Posts was Selected -->
        <div    
            v-if="currentStep === 2 && selectedRecommend === 'post'" 
            class="relative flex flex-col gap-3 py-4 px-3 rounded-lg min-w-[400px] max-w-[80%] max-h-[800px] overflow-y-scroll"
            style="background-color: #18181b;"
        >
            <div class="flex items-center justify-between gap-3">
                <div class="flex gap-2">
                    <button class="bg-white rounded-full px-3 py-1" @click="currentStep -= 1;">
                        <i class="fa-solid fa-chevron-left text-black"></i>
                    </button>
                    <h3 class="mb-0 fw-semibold mt-1 text-xlt text-white">Sélectionner les Posts :</h3>
                </div>
                <button @click="handleClose">
                    <img src="/build/assets/icons/close-white.svg" alt="Close Icon" height="22" width="22" />
                </button>
            </div>
            <!-- Loading Screen While Generating Content -->
            <div v-if="isGenerating" class="absolute inset-0 h-full z-50 flex items-center justify-center" style="background-color: #18181b;">
                <div class="ai-loader">
                    <label>Chargement...</label>
                    <div class="loading"></div>
                </div>
            </div>
            <div v-else class="flex flex-col gap-2 w-full">
                <label for="ai-note" class="fw-semibold text-white">Ajouter une note pour votre assistant IA à considérer :</label>
                <div class="flex gap-2">
                    <input 
                        type="text" 
                        name="ai-note" 
                        id="ai-note" 
                        v-model="aiUserNote" 
                        class="border p-2 rounded-lg w-[60%] text-white" 
                        style="background-color: #18181b;"
                        placeholder="Ajouter votre note..."
                    />
                    <button 
                        @click="generateRecommendForPosts"
                        class="py-2 px-3 rounded-lg text-black fw-semibold bg-white"
                    >
                        Continuer
                    </button>
                </div>
            </div>
            <div class="w-full grid grid-cols-3 gap-3">
                <div 
                    v-for="post in allLinkedinPosts"
                    :key="post.id"
                    @click="selectPostsForRecommend(post)"
                    class="cursor-pointer rounded-lg"
                    :class="isSelected(post) ? 'selected' : 'not-selected'"
                >
                    <linkedin-post 
                        :user-linkedin-accounts="userLinkedinAccounts"
                        :post="post"
                    />
                </div>
            </div>
        </div>

        <!-- If Campaigns was Selected -->
        <div    
            v-if="currentStep === 2 && selectedRecommend === 'campaign'" 
            class="relative flex flex-col gap-3 py-4 px-3 rounded-lg min-w-[400px] max-w-[80%] max-h-[800px] overflow-y-scroll"
            style="background-color: #18181b;"
        >
            <div class="flex items-center justify-between gap-2">
                <div class="flex gap-2">
                    <button class="bg-white rounded-full px-3 py-1" @click="currentStep -= 1;">
                        <i class="fa-solid fa-chevron-left text-black"></i>
                    </button>
                    <h3 class="mb-0 fw-semibold mt-1 text-xl text-white">Sélectionner les Campagnes :</h3>
                </div>
                <button @click="handleClose">
                    <img src="/build/assets/icons/close-white.svg" alt="Close Icon" height="22" width="22" />
                </button>
            </div>
            <!-- Loading Screen While Generating Content -->
            <div v-if="isGenerating" class="absolute inset-0 h-full flex items-center justify-center z-50" style="background-color: #18181b;">
                <div class="ai-loader">
                    <label>Chargement...</label>
                    <div class="loading"></div>
                </div>
            </div>

            <div v-else class="flex flex-col gap-2 w-full">
                <label for="ai-note" class="fw-semibold text-white">Ajouter une note pour votre assistant IA à considérer :</label>
                <div class="flex gap-2">
                    <input 
                        type="text" 
                        name="ai-note" 
                        id="ai-note" 
                        v-model="aiUserNote" 
                        class="border p-2 rounded-lg w-[60%] text-white" 
                        placeholder="Ajouter votre note..."
                        style="background-color: #18181b;"
                    />
                    <button 
                        @click="generateRecommendForCampaigns"
                        class="py-2 px-3 rounded-lg text-black fw-semibold bg-white"
                    >
                        Continuer
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3 w-full">
                <div 
                    v-for="campaign in allCampaigns"
                    @click="selectCampaignsForRecommend(campaign)"
                    class="p-2 rounded-lg w-full flex items-center gap-2 cursor-pointer shadow-md px-3"
                    :style="{
                        borderTop: `8px solid ${campaign.color}`, 
                        borderLeft: '1px solid #ddd', borderRight: '1px solid #ddd', borderBottom: '1px solid #ddd'
                    }"
                    :class="isCampaignSelected(campaign) ? 'campaign-selected' : 'campaign-not-selected'"
                >
                    <div class="relative">
                        <img 
                            :src="getProfilePicture(userLinkedinAccounts, campaign.linkedin_user_id)" 
                            height="50"
                            width="50"
                            class="rounded-full"
                            alt="Photo de Profile"
                        />
                        <div 
                            class="p-1 absolute rounded-full bottom-[-3px] right-[-5px]" 
                            style="background-color: rgb(23 92 179); border: 2px solid white;"
                        >
                            <img 
                                src="/build/assets/icons/linkedin.svg" 
                                alt="Linkedin Icon" 
                                height="8"
                                width="8"
                            />
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="mb-0 fw-semibold">
                            {{ campaign.name }} ({{ getCampaignPostCount(campaign) }})
                        </p>
                        <p class="mb-0 fw-light text-muted text-sm">
                            {{ getUsername(userLinkedinAccounts, campaign.linkedin_user_id) }}
                        </p>
                    </div>
                    <button @click="openCampaignDetails(campaign.linkedin_user_id, campaign)">
                        <i class="fa-regular fa-eye text-lg"></i>
                    </button>
                </div>
            </div>
        </div>


        <!-- AI RECOMMENDATION SECTION -->
        <div v-if="currentStep === 3" 
            class="relative flex flex-col gap-3 py-4 px-3 rounded-lg min-w-[400px] max-w-[80%] max-h-[800px] overflow-y-scroll"
            style="background-color: #18181b;"
        >
            <div class="flex items-center justify-between gap-2">
                <div class="flex gap-2">
                    <button class="bg-white rounded-full px-3 py-1" @click="currentStep -= 1; aiResponse = ''">
                        <i class="fa-solid fa-chevron-left text-black"></i>
                    </button>
                    <h3 class="mb-0 fw-semibold mt-1 text-xl text-white">Recommendations :</h3>
                </div>
                <button @click="handleClose">
                    <img src="/build/assets/icons/close-white.svg" alt="Close Icon" height="22" width="22" />
                </button>
            </div>

            <div v-if="isGenerating" class="w-full h-[400px] flex items-center justify-center">
                <progress-spinner 
                    style="width: 20px; height: 20px" strokeWidth="10" fill="transparent" 
                    animationDuration=".5s" 
                    aria-label="Custom ProgressSpinner" 
                />
            </div>

            <div v-else class="flex gap-2">
                <div class="p-2 rounded-full bg-white h-fit">
                    <i class="fa-solid fa-robot text-xl text-black"></i>
                </div>

                <ScrollPanel style="max-width: 700px; height: 500px;" class="p-2 rounded-lg border w-full text-white mt-2">
                    <div v-html="formattedRecommendation"></div>
                </ScrollPanel>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { useToast } from 'vue-toastification';
import { getLinkedinUserByID, getProfilePicture, getUsername } from '../services/datatables';

export default {
    name: "AIGuide",
    props: {
        userLinkedinAccounts: {
            type: Array,
            required: true,
        },
        allLinkedinPosts: {
            type: Array,
            required: true,
        },
        allCampaigns: {
            type: Array,
            required: true,
        },
    },

    emits: ['open-campaign-read-mode', 'close-ai-recommend'],

    setup() {
        const toast = useToast();
        return { toast };
    },

    mounted() {
        console.log(this.allLinkedinPosts)
    },

    data() {
        return {
            currentStep: 1,
            selectedPosts: [],
            selectedCampaigns: [],
            socialActionsData: {},
            selectedRecommend: '',
            aiUserNote: '',
            isGenerating: false,
            aiResponse: '',
        };
    },

    computed: {
        formattedRecommendation() {
            if (!this.aiResponse) return '';

            // Split the response into lines for processing
            const lines = this.aiResponse.split('\n');
            let html = '';
            let inList = false;

            lines.forEach(line => {
                line = line.trim();

                // Section headers
                if (line.match(/^(Analyse des Publications LinkedIn|Recommandations d'Amélioration) :$/)) {
                    if (inList) {
                        html += '</ul>';
                        inList = false;
                    }
                    html += `<h4>${line}</h4>`;
                }
                // Start of a numbered item (e.g., "1. Première Publication :" or "1. Diversifiez le contenu :")
                else if (line.match(/^\d+\.\s/)) {
                    if (!inList) {
                        html += '<ul>';
                        inList = true;
                    }
                    const [number, ...rest] = line.split('. ');
                    html += `<li><strong>${number}.</strong> ${rest.join('. ')}</li>`;
                }
                // Sub-details (e.g., "- Contenu : ...")
                else if (line.startsWith('- ')) {
                    if (!inList) {
                        html += '<ul>';
                        inList = true;
                    }
                    const [key, value] = line.substring(2).split(' : ');
                    html += `<li><strong>${key} :</strong> ${value || ''}</li>`;
                }
                // Regular paragraph text
                else if (line && !inList) {
                    html += `<p>${line}</p>`;
                }
            });

            if (inList) html += '</ul>';
            return html;
        },
    },

    methods: {
        getLinkedinUserByID,
        getProfilePicture,
        getUsername,

        openCampaignDetails(linkedinId, selectedCampaign) {
            const selectedAccount = this.getLinkedinUserByID(this.userLinkedinAccounts, linkedinId);
            this.$emit('open-campaign-read-mode', selectedAccount, selectedCampaign);
        },

        handleClose() {
            this.currentStep = 1;
            this.aiUserNote = '';
            this.aiResponse = '';
            this.selectedPosts = [],
            this.selectedCampaigns = [],
            this.socialActionsData = {},
            this.selectedRecommend = '',
            this.$emit('close-ai-recommend');
        },

        selectTypeForRecommend(type) {
            this.selectedRecommend = type;
            this.currentStep = 2;
        },

        // POSTS & CAMPAIGNS SELECTION METHODS
        selectPostsForRecommend(post) {
            const index = this.selectedPosts.findIndex(p => p.id === post.id);
            if (index === -1) {
                if (this.selectedPosts.length >= 6) {
                    this.showErrorToast("Vous pouvez sélectionner 6 posts au maximum");
                    return;
                }
                this.selectedPosts.push(post);
            } else {
                this.selectedPosts.splice(index, 1);
            }
        },

        selectCampaignsForRecommend(campaign) {
            const index = this.selectedCampaigns.findIndex(c => c.id === campaign.id);
            if (index === -1) {
                if (this.selectedCampaigns.length >= 3) {
                    this.showErrorToast("Vous pouvez sélectionner 3 campagnes au maximum");
                    return;
                }
                this.selectedCampaigns.push(campaign);
            } else {
                this.selectedCampaigns.splice(index, 1);
            }
        },

        // FUNCTION FOR CHECKING POST OR CAMPAIGN IS SELECTED OR NOT
        isSelected(post) {
            return this.selectedPosts.some(selectedPost => selectedPost.id === post.id);
        },

        isCampaignSelected(campaign) {
            return this.selectedCampaigns.some(selectedCampaign => selectedCampaign.id === campaign.id);
        },

        // FUNCTION TO GET CAMPAIGN POST COUNT
        getCampaignPostCount(campaign) {
            return this.allLinkedinPosts.filter(post => campaign.id === post.campaign_id).length;
        },  

        // SOCIAL ACTIONS FETCHING FUNCTIONS
        async fetchSocialActions() {
            await this.fetchSocialActionsForPosts(this.selectedPosts);
        },

        async fetchSocialActionsForPosts(posts) {
            this.isGenerating = true;
            const promises = posts.map(post => {
                const linkedinUser = this.getLinkedinUserByID(this.userLinkedinAccounts, post.linkedin_user_id);
                if (!linkedinUser) {
                    console.error(`No LinkedIn user found for linkedin_user_id: ${post.linkedin_user_id}`);
                    this.showErrorToast("Compte LinkedIn non trouvé !");
                    return Promise.resolve(null);
                }
                return axios.get('/linkedin/get-social-actions', {
                    params: {
                        post_id: post.id,
                        urn: encodeURIComponent(post.post_urn),
                        linkedin_user_id: post.linkedin_user_id,
                        linkedin_token: linkedinUser.linkedin_token
                    },
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(response => {
                    if (response.status === 200) {
                        return {
                            postId: post.id,
                            likes: response.data.likesSummary?.totalLikes || 0,
                            comments: response.data.commentsSummary?.aggregatedTotalComments || 0
                        };
                    }
                    console.warn(`Skipping post ${post.id} due to unsuccessful response: ${response.status}`);
                    return null;
                }).catch(error => {
                    if (error.response) {
                        const status = error.response.status;
                        if (status === 401) {
                            console.warn(`Skipping post ${post.id}: Token expired or invalid`);
                        } else if (status === 403) {
                            console.warn(`Skipping post ${post.id}: Insufficient permissions`);
                        } else if (status === 404) {
                            console.warn(`Skipping post ${post.id}: Post not found, possibly deleted`);
                        } else {
                            console.error(`Skipping post ${post.id}: Error status ${status}`, error.response.data);
                        }
                    } else {
                        console.error(`Skipping post ${post.id}: Network or server error`, error);
                    }
                    return null;
                });
            });

            const results = await Promise.all(promises);
            const newSocialActions = results.filter(result => result !== null)
                .reduce((acc, { postId, likes, comments }) => {
                    acc[postId] = { likes, comments };
                    return acc;
                }, {});
            this.socialActionsData = { ...this.socialActionsData, ...newSocialActions };
            this.isGenerating = false;
        },

        async fetchSocialActionsForCampaigns() {
            const campaignPosts = this.selectedCampaigns.flatMap(campaign =>
                this.allLinkedinPosts.filter(post => post.campaign_id === campaign.id)
            );
            if (campaignPosts.length === 0) {
                this.showErrorToast("Aucun post trouvé dans les campagnes sélectionnées");
                return;
            }
            await this.fetchSocialActionsForPosts(campaignPosts);
        },

        // AI RECOMMENDATION GENERATION FUNCTIONS
        async generateRecommendForPosts() {
            if (this.selectedPosts.length < 1) {
                this.showErrorToast("Veuillez sélectionner au moins un Post pour continuer");
                return;
            }

            try {
                this.isGenerating = true;
                this.currentStep = 3;
                await this.fetchSocialActions();

                const postData = this.selectedPosts.map(post => {
                    const socialActions = this.socialActionsData[post.id] || { likes: 0, comments: 0 };
                    return {
                        content: post.content.text || post.content.caption || post.content || '',
                        likes: socialActions.likes,
                        comments: socialActions.comments
                    };
                });

                const openaiKey = import.meta.env.VITE_OPENAI_API_KEY;
                const prompt = `Analyze the following LinkedIn posts and provide recommendations for improvement based on their content, likes, and comments:\n\n${JSON.stringify(postData, null, 2)}\n\nUser note: ${this.aiUserNote}\n\nThe response must be in French language`;

                const response = await axios.post('https://api.openai.com/v1/chat/completions', {
                    model: 'gpt-3.5-turbo',
                    messages: [
                        { role: 'system', content: 'You are a LinkedIn content strategist.' },
                        { role: 'user', content: prompt }
                    ],
                    max_tokens: 500,
                }, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${openaiKey}`
                    }
                });

                if (response.status === 200) {
                    this.aiResponse = response.data.choices[0].message.content.trim();
                    const recommendation = response.data.choices[0].message.content.trim();
                    console.log('Recommendation:', recommendation);
                    this.toast.success('Recommendation générée avec succès !');

                } else {
                    this.showErrorToast('Échec de la génération de la recommandation');
                }
            } catch (error) {
                console.error('Erreur lors de la génération de la recommandation:', error);
                this.showErrorToast('Une erreur est survenue lors de la génération de la recommandation');
            } finally {
                this.isGenerating = false;
            }
        },

        async generateRecommendForCampaigns() {
            if (this.selectedCampaigns.length < 1) {
                this.showErrorToast("Veuillez sélectionner au moins une Campagne pour continuer");
                return;
            }

            try {
                this.isGenerating = true;
                this.currentStep = 3;
                await this.fetchSocialActionsForCampaigns();

                const campaignData = this.selectedCampaigns.map(campaign => {
                    const campaignPosts = this.allLinkedinPosts.filter(post => post.campaign_id === campaign.id);
                    const postsData = campaignPosts.map(post => {
                        const socialActions = this.socialActionsData[post.id] || { likes: 0, comments: 0 };
                        return {
                            content: post.content.text || post.content.caption || post.content || '',
                            likes: socialActions.likes,
                            comments: socialActions.comments
                        };
                    });
                    return {
                        name: campaign.name,
                        posts: postsData
                    };
                });

                if (campaignData.every(c => c.posts.length === 0)) {
                    this.showErrorToast("Aucun post trouvé dans les campagnes sélectionnées");
                    return;
                }

                const openaiKey = import.meta.env.VITE_OPENAI_API_KEY;
                const prompt = `Analyze the following LinkedIn campaigns and provide recommendations for improvement based on their posts' content, likes, and comments:\n\n${JSON.stringify(campaignData, null, 2)}\n\nUser note: ${this.aiUserNote}\n\nThe response must be in French language`;

                const response = await axios.post('https://api.openai.com/v1/chat/completions', {
                    model: 'gpt-3.5-turbo',
                    messages: [
                        { role: 'system', content: 'You are a LinkedIn content strategist.' },
                        { role: 'user', content: prompt }
                    ],
                    max_tokens: 500,
                }, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${openaiKey}`
                    }
                });

                if (response.status === 200) {
                    this.aiResponse = response.data.choices[0].message.content.trim();
                    const recommendation = response.data.choices[0].message.content.trim();
                    console.log('Recommendation for campaigns:', recommendation);
                    this.toast.success('Recommendation pour les campagnes générée avec succès !');
                } else {
                    this.showErrorToast('Échec de la génération de la recommandation pour les campagnes');
                }
            } catch (error) {
                console.error('Erreur lors de la génération de la recommandation pour les campagnes:', error);
                this.showErrorToast('Une erreur est survenue lors de la génération de la recommandation pour les campagnes');
            } finally {
                this.isGenerating = false;
            }
        },

        showErrorToast(error) {
            this.toast.error(`${error}`, {
                position: "bottom-right",
                timeout: 3000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: false,
                closeButton: "button",
                icon: true,
                rtl: false
            });
        },
    },
};
</script>

<style scoped>
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
/* Post Selection Classes */
.selected {
    border-top: 5px solid green;
}
.not-selected {
    border-top: 5px solid red;
}
/* Campaign Selection Classes */
.campaign-selected {
    background-color: lightgrey;
}

.campaign-not-selected {
    background-color: white;
}

.ai-loader {
  width: 350px;
  height: 180px;
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-evenly;
  padding: 30px;
}
.loading {
  width: 100%;
  height: 10px;
  background: lightgrey;
  border-radius: 10px;
  position: relative;
}
.loading::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 50%;
  height: 10px;
  background: black;
  border-radius: 10px;
  z-index: 1;
  animation: loading 0.6s alternate infinite;
}
.ai-loader label {
  color: white;
  font-size: 18px;
  animation: bit 0.6s alternate infinite;
}

@keyframes bit {
  from {
    opacity: 0.3;
  }
  to {
    opacity: 1;
  }
}

@keyframes loading {
  0% {
    left: 25%;
  }
  100% {
    left: 50%;
  }
  0% {
    left: 0%;
  }
}
</style>