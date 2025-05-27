<template>
    <div class="dashboard-calendar mt-5 border rounded-2xl py-4 px-2">
        <calendar-navigation v-model:currentMonth="currentMonth" v-model:currentYear="currentYear" />
        <loading-overlay :isLoading="isLoading" message="Traitement en cours..." />
        <!-- Day Headers -->
        <div class="grid grid-cols-7 text-center font-medium mb-2">
            <div class="py-2">Dim</div>
            <div class="py-2">Lun</div>
            <div class="py-2">Mar</div>
            <div class="py-2">Mer</div>
            <div class="py-2">Jeu</div>
            <div class="py-2">Ven</div>
            <div class="py-2">Sam</div>
        </div>
    
        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 gap-1">
            <!-- Blank days before the first day of the month -->
            <template v-for="blankDay in Array.from({ length: getFirstDayOfMonth(currentMonth, currentYear) }, (_, i) => i)" :key="'blank-' + blankDay">
                <div class="h-32 bg-gray-100 rounded"></div>
            </template>
    
            <!-- Days of the month -->
            <template v-for="day in getDaysInMonth(currentMonth, currentYear)" :key="day">
                <div 
                    class="border h-32 rounded overflow-hidden relative p-1"
                    :class="{
                        'cursor-pointer hover:bg-gray-200 transition-all duration-200': displayCampaigns(day).isActive && getPostsForDate(day).length > 0,
                        'cursor-not-allowed': !displayCampaigns(day).isActive
                    }"
                >
                    <div class="text-right text-sm">{{ day }}</div>

                    <!-- Campaigns for that Day -->
                    <div class="overflow-y-auto" style="max-height: 100px;">
                        <div 
                            v-for="(campaignData, index) in getCampaignsForDate(day)"
                            :key="index"
                            class="text-sm py-3 px-2 fw-semibold mb-1 rounded truncate cursor-pointer"
                            :style="{ backgroundColor: campaignData.color }"
                            @click="openCampaignInReadMode(campaignData.linkedin_user_id, campaignData.id)"
                        >
                        <!-- @click="getCampaignPostsForDate(campaignData, day)" -->
                            {{ campaignData.name }} ({{ campaignData.postCount }})
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <campaign-portal
            v-if="showCampaignPortal"
            ref="campaignPortal"
            :selected-account="selectedAccount"
            :read-mode="readModeStatus"
            :selectedCampaign="selectedCampaign"
            :linkedin-posts="posts"
            @campaign-post-editing="editCampaignPost"
            @posts-generated="handlePostsGenerated"
            @dates-updated="updateCampaignDates"
            @update:form-data="updateFormData"
            @validate="isFormValid = $event"
            @close-campaign-portal="showCampaignPortal = false; selectedCampaign = null; readModeStatus = false; selectedAccount = null;"
        />
        

        <!-- POST MODAL POPOVER -->
        <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-lg max-h-[600px] overflow-y-scroll">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold mb-0">Détails du Post</h3>
                    <button 
                        @click="closeModal" 
                        class="text-black text-3xl"
                    >
                        ×
                    </button>
                </div>

                <!-- Fields for Adding a Post -->
                <div v-if="isAdding" class="mb-4">
                    <div class="flex flex-col gap-4">
                        <div>
                            <label class="text-sm">Date et heure:</label>
                            <input
                                type="datetime-local"
                                v-model="newPost.scheduled_time"
                                class="border rounded p-2 w-full"
                            />
                        </div>

                        <div>
                            <label class="text-sm">Type de Post:</label>
                            <select
                                v-model="newPost.type"
                                class="border rounded p-2 w-full"
                            >
                                <option value="text">Texte</option>
                                <option value="image">Image</option>
                                <option value="video">Vidéo</option>
                                <option value="article">Article</option>
                            </select>
                        </div>

                        <!-- Content based on post type -->
                        <div v-if="newPost.type === 'text'">
                            <textarea
                                v-model="newPost.content.text"
                                placeholder="Exprimez-vous !"
                                class="w-full border rounded-lg p-2 h-32"
                            ></textarea>
                        </div>

                        <div v-if="newPost.type === 'image' || newPost.type === 'video'">
                            <input
                                type="file"
                                :accept="newPost.type === 'image' ? 'image/*' : 'video/*'"
                                class="w-full border rounded-lg p-2 mb-2"
                                @change="handleFileUploadNewPost"
                            />
                            <p v-if="newPost.content.fileName" class="mt-2 mb-0 text-sm text-gray-600">
                                Fichier actuel : {{ newPost.content.fileName }}
                            </p>
                            <textarea
                                v-model="newPost.content.caption"
                                placeholder="Souhaitez-vous ajouter quelques mots ? (Optionnel)"
                                class="w-full border rounded-lg p-2 h-32 mt-2"
                            ></textarea>
                        </div>

                        <div v-if="newPost.type === 'article'" class="flex flex-col gap-2">
                            <label class="text-sm text-gray-600">URL de l'Article :</label>
                            <input
                                type="text"
                                v-model="newPost.content.url"
                                class="w-full border rounded-lg p-2"
                            />
                            <label class="text-sm text-gray-600">Titre de l'Article :</label>
                            <input
                                type="text"
                                v-model="newPost.content.title"
                                class="w-full border rounded-lg p-2"
                            />
                            <label class="text-sm text-gray-600">Description de l'Article :</label>
                            <input
                                type="text"
                                v-model="newPost.content.description"
                                class="w-full border rounded-lg p-2"
                            />
                            <textarea
                                v-model="newPost.content.caption"
                                placeholder="Souhaitez-vous ajouter quelques mots ? (Optionnel)"
                                class="w-full border rounded-lg p-2 h-32 mt-2"
                            ></textarea>
                        </div>

                        <button 
                            @click="createPost"
                            class="bg-green-500 text-white py-2 px-4 fw-semibold rounded-lg mt-4"
                        >
                            Ajouter le Post
                        </button>
                    </div>
                </div>

                <div v-if="selectedPost && !isAdding" class="mb-4">
                    <!-- Editable Fields when in edit mode -->
                    <div v-if="isEditing" class="flex flex-col gap-4">
                        <div>
                            <label class="text-sm">Date et heure:</label>
                            <input
                                type="datetime-local"
                                v-model="localScheduledDateTime"
                                class="border rounded p-2 w-full"
                            />
                        </div>

                        <div>
                            <label class="text-sm">Type de Post:</label>
                            <select
                                v-model="editedPost.type"
                                class="border rounded p-2 w-full"
                            >
                                <option value="text">Texte</option>
                                <option value="image">Image</option>
                                <option value="video">Vidéo</option>
                                <option value="article">Article</option>
                            </select>
                        </div>

                        <!-- Content based on post type -->
                        <div v-if="editedPost.type === 'text'">
                            <textarea
                                v-model="editedPost.content.text"
                                placeholder="Exprimez-vous !"
                                class="w-full border rounded-lg p-2 h-32"
                            ></textarea>
                        </div>

                        <div v-if="editedPost.type === 'image' || editedPost.type === 'video'">
                            <input
                                type="file"
                                :accept="editedPost.type === 'image' ? 'image/*' : 'video/*'"
                                class="w-full border rounded-lg p-2 mb-2"
                                @change="handleFileUpload"
                            />
                            <p v-if="editedPost.content.file_path" class="mt-2 mb-0 text-sm text-gray-600">
                                Fichier actuel : {{ editedPost.content.file_path.split('/').pop() }}
                            </p>
                            <textarea
                                v-model="editedPost.content.caption"
                                placeholder="Souhaitez-vous ajouter quelques mots ? (Optionnel)"
                                class="w-full border rounded-lg p-2 h-32 mt-2"
                            ></textarea>
                        </div>

                        <div v-if="editedPost.type === 'article'" class="flex flex-col gap-2">
                            <label class="text-sm text-gray-600">URL de l'Article :</label>
                            <input
                                type="text"
                                class="w-full border rounded-lg p-2"
                                v-model="editedPost.content.url"
                            />
                            <label class="text-sm text-gray-600">Titre de l'Article :</label>
                            <input
                                type="text"
                                class="w-full border rounded-lg p-2"
                                v-model="editedPost.content.title"
                            />
                            <label class="text-sm text-gray-600">Description de l'Article :</label>
                            <input
                                type="text"
                                class="w-full border rounded-lg p-2"
                                v-model="editedPost.content.description"
                            />
                            <textarea
                                v-model="editedPost.content.caption"
                                placeholder="Souhaitez-vous ajouter quelques mots ? (Optionnel)"
                                class="w-full border rounded-lg p-2 h-32 mt-2"
                            ></textarea>
                        </div>
                    </div>

                    <!-- View Mode -->
                    <div v-else>
                        <p>
                            <strong>Date & heure de publication :</strong> {{ formatDateTime(selectedPost.scheduledDateTime) }}
                        </p>
                        <p>
                            <strong>Type de Publication :</strong> {{ selectedPost.type.toUpperCase() }}
                        </p>

                        <!-- Content based on post type -->
                        <div v-if="selectedPost.type === 'text'" class="mt-2">
                            <p>
                                <strong>Contenu :</strong> {{ selectedPost.content.text || 'Aucun contenu disponible' }}
                            </p>
                        </div>

                        <div v-if="selectedPost.type === 'image'" class="mt-2">
                            <p v-if="selectedPost.content.caption">
                                <strong>Caption :</strong> {{ selectedPost.content.caption }}
                            </p>
                            <img :src="getMediaUrl(selectedPost.content.file_path)" alt="Image" class="max-w-full h-auto mb-2">
                        </div>

                        <div v-if="selectedPost.type === 'video'" class="mt-2">
                            <p v-if="selectedPost.content.caption">
                                <strong>Caption :</strong> {{ selectedPost.content.caption }}
                            </p>
                            <video :src="getMediaUrl(selectedPost.content.file_path)" controls class="max-w-full h-auto mb-2"></video>
                        </div>

                        <div v-if="selectedPost.type === 'article'" class="mt-2 flex flex-col gap-2">
                            <p>
                                <strong>URL de l'Article :</strong> 
                                <a :href="selectedPost.content.url" target="_blank" class="text-blue-600 hover:underline">
                                    {{ selectedPost.content.url }}
                                </a>
                            </p>
                            <p>
                                <strong>Titre de l'Article :</strong> {{ selectedPost.content.title || 'Sans titre' }}
                            </p>
                            <p v-if="selectedPost.content.description">
                                <strong>Description de l'Article :</strong> {{ selectedPost.content.description }}
                            </p>
                            <p v-if="selectedPost.content.caption">
                                <strong>Légende :</strong> {{ selectedPost.content.caption }}
                            </p>
                        </div>
                    </div>
                </div>

                    <!-- All Buttons -->
                    <div class="flex items-center justify-end gap-2" v-if="selectedPost">
                        <!-- Delete the Post after posting it -->
                        <button 
                            @click="deletePostFromLinkedIn(selectedPost)"
                            v-if="selectedPost.status === 'posted'" 
                            class="flex items-center gap-2 bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-700"
                        >
                            <img 
                                src="/build/assets/icons/linkedin.svg" 
                                alt="LinkedIn Icon"
                                height="20"
                                width="20"
                            />

                            <span>Supprimer de LinkedIn</span>
                        </button>


                        <div class="flex items-center gap-2" v-if="selectedPost.status !== 'posted'">
                            <!-- Delete the Post before Posting -->
                            <button 
                                @click="deletePost(selectedPost.id)"
                                v-if="selectedPost.status !== 'posted' && !isEditing"
                                class="bg-red-500 text-white py-2 px-4 rounded-lg"
                            >
                                Supprimer
                            </button>

            
                            <button 
                                v-if="!isEditing"
                                @click="startEditing"
                                class="bg-blue-500 text-white py-2 px-4 rounded-lg"
                            >
                                Modifier
                            </button>
                            <button 
                                v-else
                                @click="updatePost"
                                :disabled="isLoading"
                                class="bg-green-500 text-white py-2 px-4 rounded-lg"
                            >
                                Enregistrer
                            </button>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import { useToast } from "vue-toastification";
import { getLinkedinUserByID } from '../services/datatables';

export default {
    name: 'DashboardCalendar',

    props: {
        campaigns: {
            type: Array,
            required: true
        },
        posts: {
            type: Array,
            required: true
        },
        initialMonth: {
            type: Number,
            required: true
        },
        initialYear: {
            type: Number,
            required: true
        },
        linkedinAccounts: {
            type: Array,
            required: true,
        },
        showCampaignPortalProp: {
            type: Boolean,
            required: false,
        },
        isCreatingCampaign: {
            type: Boolean,
            required: false,
        },
    },
    
    setup() {
        const toast = useToast();
        return { toast }
    },
    
    data() {
        return {
            showCampaignPortal:false,
            readModeStatus: false,
            postDropdown: false,
            selectedDay: null,
            showPopover: false,
            currentMonth: this.initialMonth,
            currentYear: this.initialYear,
            selectedPost: null,
            editedPost: null,
            isOpen: false,
            isEditing: false,
            isAdding: false,
            isLoading: false,
            isLoadingPosts: false,
            localScheduledDateTime: '',
            popoverPosts: [],
            // ######
            selectedAccount: null,
            campaignPosts: [],
            isFormValid: false,
            selectedCampaign: null,
            // ######
            newPost: {
                type: 'text',
                scheduled_time: '',
                content: {
                    text: '',
                    caption: '',
                    url: '',
                    title: '',
                    description: '',
                    file: null,
                    fileName: ''
                }
            }
        };
    },
  
    methods: {
        getLinkedinUserByID,



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

                if(boostResponse.status === 201) {
                    this.toast.success("La requête de Boost a été envoyé avec succès!", {
                        position: "bottom-left",
                        closeOnClick: true,
                        pauseOnFocusLoss: true,
                        pauseOnHover: true,
                        draggable: true,
                        draggablePercent: 0.6,
                        showCloseButtonOnHover: false,
                        hideProgressBar: false,
                        timeout: 5000,
                        closeButton: "button",
                        icon: true,
                        rtl: false
                    });
                } else if(boostResponse.status === 404){
                    this.toast.error("Post non trouvé !", {
                        position: "bottom-left",
                        closeOnClick: true,
                        pauseOnFocusLoss: true,
                        pauseOnHover: true,
                        draggable: true,
                        draggablePercent: 0.6,
                        showCloseButtonOnHover: false,
                        hideProgressBar: false,
                        timeout: 5000,
                        closeButton: "button",
                        icon: false,
                        rtl: false
                    });
                }
                
            } catch(error) {
                console.error(error);
                this.toast.error("Une erreur s'est produite lors de l'envoi de la requête !", {
                    position: "bottom-left",
                    closeOnClick: true,
                    pauseOnFocusLoss: true,
                    pauseOnHover: true,
                    draggable: true,
                    draggablePercent: 0.6,
                    showCloseButtonOnHover: false,
                    hideProgressBar: false,
                    timeout: 5000,
                    closeButton: "button",
                    icon: true,
                    rtl: false
                });
            }
        },

        displayCampaigns(day) {
            const checkDate = new Date(this.currentYear, this.currentMonth, day);
            checkDate.setHours(0, 0, 0, 0);
            
            for (const campaign of this.campaigns) {
                const startDate = new Date(campaign.start_date);
                startDate.setHours(0, 0, 0, 0);
                
                const endDate = new Date(campaign.end_date);
                endDate.setHours(0, 0, 0, 0);
                
                if (checkDate >= startDate && checkDate <= endDate) {
                    return {
                        isActive: true,
                        color: campaign.color
                    };
                }
            }
            
            return {
                isActive: false,
                color: null
            };
        },

        getLinkedinUserByCampaign(campaign) {
            return this.linkedinAccounts.find(account => account.id === campaign.linkedin_user_id);
        },

        getCampaignPost(campaign) {
            return this.posts.filter(post => post.campaign_id === campaign.id);
        },

        getCampaignEndStartDates(campaignId) {
            const campaign = this.campaigns.find(c => c.id === campaignId);
            if (campaign) {
                return {
                    startDate: new Date(campaign.start_date),
                    endDate: new Date(campaign.end_date)
                };
            }
            return null;
        },

        async getCampaignPostsForDate(campaign, day) {
            this.selectedCampaign = campaign;
            const clickedDate = new Date(this.currentYear, this.currentMonth, day);
            const endDate = new Date(campaign.end_date);
            const startDate = new Date(campaign.start_date);

            clickedDate.setHours(0, 0, 0, 0);
            startDate.setHours(0, 0, 0, 0);
            endDate.setHours(0, 0, 0, 0);

            if (clickedDate >= startDate && clickedDate <= endDate) {
                this.isLoadingPosts = true;
                try {
                    const response = await axios.get('/linkedin/get-campaign-posts-for-day', {
                        params: {
                            linkedin_user_id: campaign.linkedin_user_id,
                            campaign_id: campaign.id,
                            selected_date: this.toLocalISOString(clickedDate).split('T')[0]
                        },
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    this.popoverPosts = response.data.map(post => ({
                        ...post,
                        content: typeof post.content === 'string' ? JSON.parse(post.content) : post.content
                    }));
                    this.selectedDay = day;
                    this.showPopover = true;
                } catch (error) {
                    console.error('Error fetching campaign posts:', error);
                    this.popoverPosts = [];
                    this.showPopover = true; 
                } finally {
                    this.isLoadingPosts = false;
                }
            } else {
                this.popoverPosts = [];
                this.selectedDay = day;
                this.showCampaignPortal = true;
            }
        },

        getCampaignsForDate(day) {
            const campaignsForDay = [];
            const checkDate = new Date(this.currentYear, this.currentMonth, day);
            checkDate.setHours(0, 0, 0, 0);

            this.campaigns.forEach(campaign => {
                const startDate = new Date(campaign.start_date);
                startDate.setHours(0, 0, 0, 0);
                const endDate = new Date(campaign.end_date);
                endDate.setHours(0, 0, 0, 0);

                if (checkDate >= startDate && checkDate <= endDate) {
                    const postCount = this.posts.filter(post => {
                        const postDate = new Date(post.scheduled_time);
                        postDate.setHours(0, 0, 0, 0);
                        return post.campaign_id === campaign.id && postDate.getTime() === checkDate.getTime();
                    }).length;

                    campaignsForDay.push({
                        id: campaign.id,
                        user_id: campaign.user_id,
                        name: campaign.name,
                        description: campaign.description,
                        color: campaign.color,
                        start_date: campaign.start_date,
                        end_date: campaign.end_date,
                        linkedin_user_id: campaign.linkedin_user_id,
                        target_audience: campaign.target_audience,
                        frequency_per_day: campaign.frequency_per_day,
                        status: campaign.status,
                        postCount: postCount
                    });
                }
            });

            return campaignsForDay;
        },

        getDaysInMonth(month, year) {
            return new Date(year, month + 1, 0).getDate();
        },
        
        getFirstDayOfMonth(month, year) {
            return new Date(year, month, 1).getDay();
        },
        
        getPostsForDate(date) {
            return this.posts.filter(post => {
                const postDate = new Date(post.scheduled_time);
                return postDate.getDate() === date && 
                    postDate.getMonth() === this.currentMonth && 
                    postDate.getFullYear() === this.currentYear;
            });
        },
        
        toLocalISOString(date) {
            const pad = (num) => String(num).padStart(2, "0");
            const year = date.getFullYear();
            const month = pad(date.getMonth() + 1);
            const day = pad(date.getDate());
            const hours = pad(date.getHours());
            const minutes = pad(date.getMinutes());
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        },

        utcToLocalForInput(utcString) {
            const date = new Date(utcString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        },
                
        formatTime(dateTime) {
            return new Date(dateTime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },
        
        formatDateTime(dateTime) {
            const date = new Date(dateTime);
            return date.toLocaleString('fr-FR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        getMonthName(monthIndex) {
            const months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            return months[monthIndex];
        },

        getPostTypeIcon(type) {
            switch(type) {
                case 'text': return '📝';
                case 'image': return '🖼️';
                case 'video': return '🎬';
                case 'article': return '📰';
                default: return '📌';
            }
        },
            
        getMediaUrl(filePath) {
            return `/linkedin/${filePath}`;
        },
            
        closeModal() {
            this.isOpen = false;
            this.selectedPost = null;
            this.editedPost = null;
            this.isEditing = false;
            this.isAdding = false;
            this.newPost = {
                type: 'text',
                scheduled_time: '',
                content: {
                    text: '',
                    caption: '',
                    url: '',
                    title: '',
                    description: '',
                    file: null,
                    fileName: ''
                }
            };
        },


        startEditing() {
            this.isEditing = true;
        },

        startAdding() {
            this.isAdding = true;
            this.isOpen = true;
            const selectedDate = new Date(this.currentYear, this.currentMonth, this.selectedDay);
            const now = new Date();
            selectedDate.setHours(now.getHours(), now.getMinutes(), 0, 0);
            this.newPost.scheduled_time = this.toLocalISOString(selectedDate);
        },

        handleFileUpload(event) {
            const file = event.target.files && event.target.files[0];
            if (file) {
                this.editedPost.content.file = file;
                this.editedPost.content.fileName = file.name;
            }
        },

        handleFileUploadNewPost(event) {
            const file = event.target.files && event.target.files[0];
            if (file) {
                this.newPost.content.file = file;
                this.newPost.content.fileName = file.name;
            }
        },

        // ########################## NEW METHODS ##########################
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

        getCampaignByID(id) {
            return this.campaigns.find(campaign => campaign.id === id);
        },

        openCampaignInReadMode(linkedin_id, campaign_id) {
            this.showCampaignPortal = true;
            this.selectedAccount = this.getLinkedinUserByID(this.linkedinAccounts, linkedin_id);
            this.selectedCampaign = this.getCampaignByID(campaign_id);
            this.readModeStatus = true;
        },
        // ########################## NEW METHODS ##########################

        async createPost() {
            this.isLoading = true;
            try {
                const campaignDates = this.getCampaignEndStartDates(this.selectedCampaign.id);
                const scheduledDateTime = new Date(this.newPost.scheduled_time);
                const now = new Date();

                if ((scheduledDateTime < campaignDates.startDate || scheduledDateTime > campaignDates.endDate) && scheduledDateTime > now) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        html: `<p>La date de publication doit être comprise entre<br><b>${this.formatDateTime(campaignDates.startDate)}</b> et <b>${this.formatDateTime(campaignDates.endDate)}</b>.</p>`,
                        confirmButtonColor: "#fd0033",
                        allowOutsideClick: true,
                        timer: 6000,
                        timerProgressBar: true,
                    });
                    return;

                } else if(scheduledDateTime < now) {
                    // If the scheduled date is in the past
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "La date de publication ne peut pas être dans le passé.",
                        confirmButtonColor: "#fd0033",
                        allowOutsideClick: true,
                        timer: 6000,
                        timerProgressBar: true,
                    });
                    return;
                } 

                if (this.newPost.type === 'text' && this.newPost.content.text.trim() === '') {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Le contenu du post ne peut pas être vide.",
                        confirmButtonColor: "#fd0033",
                        allowOutsideClick: true,
                        timer: 6000,
                        timerProgressBar: true,
                    });
                    return;
                }

                const formData = new FormData();
                formData.append('linkedin_id', this.selectedCampaign.linkedin_user_id);
                formData.append('campaign_id', this.selectedCampaign.id);
                formData.append('type', this.newPost.type);
                formData.append('scheduled_date', this.newPost.scheduled_time);

                switch (this.newPost.type) {
                    case "text":
                        formData.append("content[text]", this.newPost.content.text.trim());
                        break;

                    case "image":
                    case "video":
                        formData.append("content[file]", this.newPost.content.file);
                        formData.append("content[caption]", this.newPost.content.caption.trim());
                        formData.append("content[original_filename]", this.newPost.content.file.name);
                        break;

                    case "article":
                        formData.append("content[url]", this.newPost.content.url);
                        formData.append("content[title]", this.newPost.content.title);
                        formData.append("content[description]", this.newPost.content.description);
                        formData.append("content[caption]", this.newPost.content.caption.trim());
                        break;
                    default:
                        throw new Error("Type de publication invalide");
                }

                const response = await axios.post('/linkedin/schedule-post', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                });

                if (response.status === 200 || response.status === 201) {
                    this.isAdding = false;
                    this.closeModal();

                    Swal.fire({
                        icon: "success",
                        html: `<p class='fw-semibold'>Post créé avec succès !</p>`,
                        allowOutsideClick: false,
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer) {
                            window.location.reload();
                        }
                    });
                } else {
                    console.error("Failed to create post:", response);
                    throw new Error("Failed to create post");
                }
            } catch (error) {
                console.error("Error creating post:", error);
                if (error.response && error.response.status === 422) {
                    console.log("Validation errors:", error.response.data);
                }
            } finally {
                this.isLoading = false;
            }
        },

        async updatePost() {
            this.isLoading = true;

            try {
                this.editedPost.scheduledDateTime = this.localScheduledDateTime;

                const campaignDates = this.getCampaignEndStartDates(this.editedPost.campaign_id);
                const scheduledDateTime = new Date(this.editedPost.scheduledDateTime);

                const formData = new FormData();
                formData.append('post_id', this.editedPost.id);
                formData.append('linkedin_user_id', this.editedPost.linkedin_user_id);
                formData.append('job_id', this.editedPost.job_id);
                formData.append('type', this.editedPost.type);
                formData.append('scheduled_time', this.editedPost.scheduledDateTime);
                formData.append('content', JSON.stringify(this.editedPost.content));
                formData.append('_method', 'PUT');

                if (this.editedPost.content.file) {
                    formData.append('file', this.editedPost.content.file);
                }

                if(scheduledDateTime < campaignDates.startDate || scheduledDateTime > campaignDates.endDate) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        html: `<p>La date de publication doit être comprise entre<br><b>${this.formatDateTime(campaignDates.startDate)}</b> et <b>${this.formatDateTime(campaignDates.endDate)}</b>.</p>`,
                        confirmButtonColor: "#fd0033",
                        allowOutsideClick: true,
                        timer: 6000,
                        timerProgressBar: true,
                    });
                    return;
                }

                if(this.editedPost.type === 'text' && this.editedPost.content.text.trim() === '') {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Le contenu du post ne peut pas être vide.",
                        confirmButtonColor: "#fd0033",
                        allowOutsideClick: true,
                        timer: 6000,
                        timerProgressBar: true,
                    });
                    return;
                }

                const response = await axios.post('/linkedin/update-post', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                });

                if (response.status === 200) {
                    this.isEditing = false;
                    this.selectedPost = { ...this.editedPost };
                    this.closeModal();

                    Swal.fire({
                        icon: "success",
                        html: `<p class='fw-semibold'>Post mis à jour avec succès !</p>`,
                        allowOutsideClick: false,
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer) {
                            window.location.reload();
                        }
                    });
                } else {
                    console.error("Failed to update post:", response);
                    throw new Error("Failed to update post");
                }
            } catch (error) {
                console.error("Error updating post:", error);
                if (error.response && error.response.status === 422) {
                    console.log("Validation errors:", error.response.data);
                }
            } finally {
                this.isLoading = false;
            }
        },
        
        async deletePost(postId) {
            this.isLoading = true;
            try {
                const response = await axios.delete("/linkedin/delete-post", {
                    data: {
                        post_id: postId,
                        linkedin_user_id: this.selectedPost.linkedin_user_id,
                    },
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    },
                });

                if (response.status === 200) {
                    this.closeModal();
                    window.location.reload();
                } else {
                    console.error("Failed to delete post:", response);
                    throw new Error("Failed to delete post");
                }
            } catch (error) {
                console.error("Error deleting post:", error);

            } finally {
                this.isLoading = false;
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
                    const deleteData = new FormData();
                    deleteData.append("post_id", post.id);
                    deleteData.append("linkedin_user_id", post.linkedin_user_id);
                    deleteData.append("urn_id", urnId);

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
                        setTimeout(() => {
                            window.location.reload();
                        }, 2500);
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
    },
}
</script>

<style scoped>
.dashboard-calendar {
    /* position: relative; */
    max-width: 100%;
}
</style>