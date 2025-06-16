<template>
    <div class="w-full h-full p-4 overflow-y-scroll">
        <div class="flex flex-col">
            <div class="flex items-center gap-2 text-black">
                <img 
                    src="/build/assets/icons/calendar-black.svg" 
                    alt="Calendar Icon"
                    class="mb-2"
                    height="50"
                    width="50"
                />
                <h1 class="mb-0 fw-semibold">Votre Calendrier</h1>
            </div>
            <p class="mt-2 ml-2 text-lg text-muted">
                Visualisez et organisez vos campagnes et posts jour après jour pour rester maître de votre stratégie !
            </p>
        </div>

        <div class="flex items-center justify-between mt-4">
            <!-- Accounts Buttons Sections -->
            <div class="grid grid-cols-3 gap-2">
                <div 
                    v-for="linkedinAccount in this.userLinkedinAccounts" 
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
                    <button class="cursor-pointer ml-2">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff">
                            <path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm70-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z"/>
                        </svg>
                    </button>
                </div>
            </div>
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

        <div class="dashboard-calendar mt-5 border rounded-2xl py-4 px-2">
            <calendar-navigation 
                :currentMonth="currentMonth" 
                :currentYear="currentYear" 
                :viewMode="viewMode" 
                @update:viewMode="viewMode = $event" 
                @navigate="handleNavigation" 
            />
            <loading-overlay :isLoading="isLoading" message="Traitement en cours..." />
            <div class="grid grid-cols-7 text-center font-medium">
                <div class="py-2 border fw-semibold text-lg bg-gray-200">Dimanche</div>
                <div class="py-2 border fw-semibold text-lg bg-gray-200">Lundi</div>
                <div class="py-2 border fw-semibold text-lg bg-gray-200">Mardi</div>
                <div class="py-2 border fw-semibold text-lg bg-gray-200">Mercredi</div>
                <div class="py-2 border fw-semibold text-lg bg-gray-200">Jeudi</div>
                <div class="py-2 border fw-semibold text-lg bg-gray-200">Vendredi</div>
                <div class="py-2 border fw-semibold text-lg bg-gray-200">Samedi</div>
            </div>
            <div class="grid grid-cols-7">
                <!-- Blank days for month view only -->
                <template v-if="viewMode === 'month'">
                    <template v-for="blankDay in Array.from({ length: displayBlankDays }, (_, i) => i)" :key="'blank-' + blankDay">
                        <div class="h-48 bg-gray-100"></div>
                    </template>
                </template>
                <!-- Days for both month and week views -->
                <template v-for="date in displayDays" :key="date.toISOString()">
                    <div 
                        class="border h-48 overflow-hidden relative p-1"
                        :class="{
                            'cursor-pointer hover:bg-gray-200 transition-all duration-200': displayCampaigns(date).isActive && getPostsForDate(date).length > 0,
                            'cursor-not-allowed': !displayCampaigns(date).isActive,
                            'h-[400px]': viewMode === 'week'
                        }"
                    >
                        <div class="text-right text-md m-2 flex justify-between items-center">
                            <p 
                                class="text-white fw-semibold py-1 px-2 rounded-full month-text"
                                v-if="viewMode === 'week'"
                            >
                                {{ getMonthName(date.getMonth()) }}
                            </p>
                            <p>{{ date.getDate() }}</p>
                        </div>
                        <div class="overflow-y-scroll pb-2"
                            :class="{
                                'max-h-[300px]': viewMode === 'week',
                                'max-h-[120px]': viewMode === 'month'
                            }"
                        >
                            <div 
                                v-for="(item, index) in getItemsForDate(date)"
                                :key="index"
                                class="text-sm py-3 bg-gray-50 px-2 fw-semibold mb-1 rounded truncate cursor-pointer shadow-sm"
                                :style="{ 
                                    borderTop: `8px solid ${item.color}`, 
                                    borderLeft: '1px solid #ddd', borderRight: '1px solid #ddd', borderBottom: '1px solid #ddd'
                                }"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2"
                                        @click="item.type === 'campaign' ? openCampaignInReadMode(item.linkedin_user_id, item.id) : openPostReadMode(item)">
                                        <div class="relative">
                                            <img 
                                                :src="getProfilePicture(userLinkedinAccounts, item.linkedin_user_id)" 
                                                height="40"
                                                width="40"
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
                                            <p class="mb-0" v-if="item.type === 'campaign'">
                                                {{ item.name }} ({{ item.postCount }})
                                            </p>
                                            <p class="mb-0" v-else>
                                                {{ translatePostType(item.post_type) }} Post
                                            </p>
                                            <p class="mb-0 fw-light text-muted">
                                                {{ getUsername(userLinkedinAccounts, item.linkedin_user_id) }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex flex-col px-1">
                                        <button 
                                            @click="item.type === 'campaign' ? deleteCampaign(item.id) : 
                                                (item.status === 'posted' ? deletePostFromLinkedIn(item) : deletePost(item))"
                                        >
                                            <i class="fa-regular fa-trash-can text-red-500 text-lg z-50 mb-1"></i>
                                        </button>

                                        <button 
                                            v-if="
                                                (item.real_type && item.real_type === 'post' && item.status !== 'posted') || 
                                                    (item.status !== 'completed' && item.type === 'campaign')"
                                            @click="item.type === 'campaign' ? openCampaignPortalInEditMode(item, item.linkedin_user_id) : 
                                                openPostPortalInEditMode(item, item.linkedin_user_id)"
                                        >
                                            <i class="fa-regular fa-pen-to-square text-blue-500 text-lg z-50"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import { useToast } from "vue-toastification";
import { getLinkedinUserByID, getProfilePicture, getUsername } from '../services/datatables';

export default {
    name: 'CalendarSection',

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
        }
    },

    emits: [
            'open-campaign-portal', 
            'open-campaign-read-mode', 
            'open-post-read-mode', 
            'open-post-portal', 
            'open-campaign-portal-edit-mode', 
            'open-post-portal-edit-mode'
        ],

    setup() {
        const toast = useToast();
        return { toast }
    },

    data() {
        return {
            selectedDay: null,
            selectedDate: new Date(),
            viewMode: 'month',
            selectedPost: null,
            selectedAccount: null,
            isAdding: false,
            isLoading: false,
            campaignPosts: [],
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

    created() {
        const today = new Date();
        this.selectedDate = new Date(today.getFullYear(), today.getMonth(), 1);
    },

    computed: {
        currentMonth() {
            return this.selectedDate.getMonth();
        },
        currentYear() {
            return this.selectedDate.getFullYear();
        },
        displayDays() {
            if (this.viewMode === 'month') {
                const daysInMonth = this.getDaysInMonth(this.currentMonth, this.currentYear);
                return Array.from({ length: daysInMonth }, (_, i) => new Date(this.currentYear, this.currentMonth, i + 1));
            } else { // week view
                const weekStart = this.getWeekStartDate(this.selectedDate);
                return this.getWeekDays(weekStart);
            }
        },
        displayBlankDays() {
            return this.viewMode === 'month' ? this.getFirstDayOfMonth(this.currentMonth, this.currentYear) : 0;
        }
    },

    methods: {
        getLinkedinUserByID,
        getProfilePicture,
        getUsername,

        selectAccount(account) {
            this.selectedAccount = account;
        },

        openCampaignPortal() {
            if (this.selectedAccount) {
                this.$emit('open-campaign-portal', { account: this.selectedAccount });
            } else {
                Swal.fire({
                    title: "<h3 class='text-black fw-semibold'>Compte?</h3>",
                    html: "<p class='text-muted fw-light text-md mb-0'>Veuillez choisir un compte avant tout</p>",
                    icon: "warning",
                });
            }
        },

        openCampaignPortalInEditMode(campaign, userLinkedinId) {
            const campaignOwner = this.getLinkedinUserByID(this.userLinkedinAccounts, userLinkedinId);

            this.$emit('open-campaign-portal-edit-mode', campaign, campaignOwner);
        },

        openPostPortal() {
            this.$emit('open-post-portal', { post: null, readMode: false });
        },

        openPostPortalInEditMode(post, userLinkedinId) {
            const postOwner = this.getLinkedinUserByID(this.userLinkedinAccounts, userLinkedinId);
            this.$emit('open-post-portal-edit-mode', { post, readMode: false, account: postOwner });
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

                if(boostResponse.status === 201) {
                    this.toast.success("La requête de Boost a été envoyé avec succès!");
                } else if(boostResponse.status === 404){
                    this.toast.error("Post non trouvé !");
                }
            } catch(error) {
                console.error(error);
                this.toast.error("Une erreur s'est produite lors de l'envoi de la requête !");
            }
        },

        handleNavigation(direction) {
            const date = new Date(this.selectedDate);
            if (this.viewMode === 'month') {
                date.setMonth(date.getMonth() + (direction === 'next' ? 1 : -1));
                date.setDate(1); // Move to the first of the month
            } else { // week view
                date.setDate(date.getDate() + (direction === 'next' ? 7 : -7));
            }
            this.selectedDate = date;
        },

        getWeekStartDate(date) {
            const d = new Date(date);
            const day = d.getDay(); // 0 = Sunday, 6 = Saturday
            d.setDate(d.getDate() - day); // Move to Sunday
            return d;
        },

        getWeekDays(startDate) {
            const days = [];
            for (let i = 0; i < 7; i++) {
                const day = new Date(startDate);
                day.setDate(startDate.getDate() + i);
                days.push(day);
            }
            return days;
        },

        displayCampaigns(date) {
            const checkDate = new Date(date);
            checkDate.setHours(0, 0, 0, 0);
            for (const campaign of this.allCampaigns) {
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

        getCampaignEndStartDates(campaignId) {
            const campaign = this.allCampaigns.find(c => c.id === campaignId);
            if (campaign) {
                return {
                    startDate: new Date(campaign.start_date),
                    endDate: new Date(campaign.end_date)
                };
            }
            return null;
        },

        getItemsForDate(date) {
            const itemsForDay = [];
            const checkDate = new Date(date);
            checkDate.setHours(0, 0, 0, 0);

            this.allCampaigns.forEach(campaign => {
                const startDate = new Date(campaign.start_date);
                startDate.setHours(0, 0, 0, 0);
                const endDate = new Date(campaign.end_date);
                endDate.setHours(0, 0, 0, 0);

                if (checkDate >= startDate && checkDate <= endDate) {
                    const postCount = this.allLinkedinPosts.filter(post => {
                        const postDate = new Date(post.scheduled_time);
                        postDate.setHours(0, 0, 0, 0);
                        return post.campaign_id === campaign.id && postDate.getTime() === checkDate.getTime();
                    }).length;

                    itemsForDay.push({
                        type: 'campaign',
                        id: campaign.id,
                        user_id: campaign.user_id,
                        name: campaign.name,
                        description: campaign.description,
                        color: campaign.color || '#e0e0e0',
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

            this.allLinkedinPosts.forEach(post => {
                if (post.campaign_id == null) {
                    const scheduledPostDate = new Date(post.scheduled_time);
                    scheduledPostDate.setHours(0, 0, 0, 0);

                    if (scheduledPostDate.getTime() === checkDate.getTime()) {
                        const content = typeof post.content === 'string' ? JSON.parse(post.content) : post.content;
                        itemsForDay.push({
                            real_type: 'post',
                            id: post.id,
                            type: post.type,
                            user_id: post.user_id,
                            linkedin_user_id: post.linkedin_user_id,
                            job_id: post.job_id,
                            status: post.status,
                            scheduledDateTime: this.toLocalISOString(new Date(post.scheduled_time)), // Match PostPortal expectation
                            post_urn: post.post_urn,
                            content: {
                                text: content.text || '',
                                caption: content.caption || '',
                                url: content.url || '',
                                title: content.title || '',
                                description: content.description || '',
                                file_path: content.file_path || '',
                                files: Array.isArray(content.files) ? [...content.files] : [],
                                original_filenames: Array.isArray(content.original_filenames) ? [...content.original_filenames] : [],
                                file: null,
                                fileName: content.original_filename || null,
                            },
                            color: '#000000',
                            tempId: `post-${post.id}-${Date.now()}` // Add tempId for consistency
                        });
                    }
                }
            });

            return itemsForDay;
        },

        getDaysInMonth(month, year) {
            return new Date(year, month + 1, 0).getDate();
        },

        getFirstDayOfMonth(month, year) {
            return new Date(year, month, 1).getDay();
        },

        getPostsForDate(date) {
            const checkDate = new Date(date);
            checkDate.setHours(0, 0, 0, 0);
            return this.allLinkedinPosts.filter(post => {
                const postDate = new Date(post.scheduled_time);
                postDate.setHours(0, 0, 0, 0);
                return postDate.getTime() === checkDate.getTime();
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
                'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
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

        translatePostType(type) {
            switch(type) {
                case 'text':
                    return 'Texte'
                case 'image':
                    return 'Image';
                case 'video':
                    return 'Vidéo';
                case 'multiimage':
                    return 'Multi-Image';
                default: return 'Type inconnu'
            }
        },

        getMediaUrl(filePath) {
            return `/linkedin/${filePath}`;
        },

        getCampaignByID(id) {
            return this.allCampaigns.find(campaign => campaign.id === id);
        },

        openCampaignInReadMode(linkedin_id, campaign_id) {
            const selectedAccount = this.getLinkedinUserByID(this.userLinkedinAccounts, linkedin_id);
            const selectedCampaign = this.getCampaignByID(campaign_id);
            if (selectedAccount && selectedCampaign) {
                this.$emit('open-campaign-read-mode', selectedAccount, selectedCampaign);
            } else {
                console.error('Account or Campaign not found');
                this.toast.error('Compte ou campagne introuvable !');
            }
        },

        openPostReadMode(post) {
            const selectedAccount = this.getLinkedinUserByID(this.userLinkedinAccounts, post.linkedin_user_id);
            if (selectedAccount) {
                const normalizedPost = {
                    id: post.id,
                    type: post.type,
                    scheduledDateTime: post.scheduledDateTime,
                    content: { ...post.content },
                    job_id: post.job_id,
                    post_urn: post.post_urn,
                    status: post.status,
                    linkedin_user_id: post.linkedin_user_id,
                    user_id: post.user_id,
                    tempId: post.tempId,
                    accountId: selectedAccount.id
                };
                console.log('Emitting normalized post:', normalizedPost); // Debug
                this.$emit('open-post-read-mode', { account: selectedAccount, post: normalizedPost, readMode: true });
            } else {
                console.error('Account not found for linkedin_user_id:', post.linkedin_user_id);
                this.toast.error('Compte introuvable !');
            }
        },

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

        async deletePost(post) {
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

            if(result.isConfirmed) {
                this.isLoading = true;
                try {
                    const response = await axios.delete("/linkedin/delete-post", {
                        data: {
                            post_id: post.id,
                            linkedin_user_id: post.linkedin_user_id,
                        },
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        },
                    });

                    if (response.status === 200) {
                        await Swal.fire({
                            title: "Post supprimé !",
                            text: "Votre campagne a été supprimé avec succès.",
                            icon: "success"
                        });
                        window.location.reload();
                    }
                } catch (error) {
                    console.error("Error deleting post:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Une erreur s'est produite lors de la suppression du post !",
                    });
                } finally {
                    this.isLoading = false;
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
                        throw new Error("Failed to delete campaign");
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
.month-text {
    background: linear-gradient(
        135deg,
        rgb(255 16 185) 0%,
        rgb(255 125 82) 100%
    );
}
</style>