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
                    style="background-color: #18181b;"
                    :class="{ 'text-red-500 border': this.selectedAccount === linkedinAccount }"
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
            <calendar-navigation v-model:currentMonth="currentMonth" v-model:currentYear="currentYear" />
            <loading-overlay :isLoading="isLoading" message="Traitement en cours..." />
            <div class="grid grid-cols-7 text-center font-medium mb-2">
                <div class="py-2">Dim</div>
                <div class="py-2">Lun</div>
                <div class="py-2">Mar</div>
                <div class="py-2">Mer</div>
                <div class="py-2">Jeu</div>
                <div class="py-2">Ven</div>
                <div class="py-2">Sam</div>
            </div>
            <div class="grid grid-cols-7 gap-1">
                <template v-for="blankDay in Array.from({ length: getFirstDayOfMonth(currentMonth, currentYear) }, (_, i) => i)" :key="'blank-' + blankDay">
                    <div class="h-32 bg-gray-100 rounded"></div>
                </template>
                <template v-for="day in getDaysInMonth(currentMonth, currentYear)" :key="day">
                    <div 
                        class="border h-32 rounded overflow-hidden relative p-1"
                        :class="{
                            'cursor-pointer hover:bg-gray-200 transition-all duration-200': displayCampaigns(day).isActive && getPostsForDate(day).length > 0,
                            'cursor-not-allowed': !displayCampaigns(day).isActive
                        }"
                    >
                        <div class="text-right text-sm">{{ day }}</div>
                        <div class="overflow-y-auto" style="max-height: 100px;">
                            <div 
                                v-for="(campaignData, index) in getCampaignsForDate(day)"
                                :key="index"
                                class="text-sm py-3 px-2 fw-semibold mb-1 rounded truncate cursor-pointer"
                                :style="{ backgroundColor: campaignData.color }"
                                @click="openCampaignInReadMode(campaignData.linkedin_user_id, campaignData.id)"
                            >
                                {{ campaignData.name }} ({{ campaignData.postCount }})
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
import { getLinkedinUserByID } from '../services/datatables';

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

    emits: ['open-campaign-portal', 'open-campaign-read-mode'],

    setup() {
        const toast = useToast();
        return { toast }
    },

    data() {
        return {
            selectedDay: null,
            showPopover: false,
            currentMonth: null,
            currentYear: null,
            selectedPost: null,
            selectedAccount: null,
            editedPost: null,
            isOpen: false,
            isEditing: false,
            isAdding: false,
            isLoading: false,
            isLoadingPosts: false,
            localScheduledDateTime: '',
            popoverPosts: [],
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
        this.currentMonth = today.getMonth();
        this.currentYear = today.getFullYear();
    },

    methods: {
        selectAccount(account) {
            this.selectedAccount = account;
        },

        openCampaignPortal() {
            if (this.selectedAccount) {
                this.$emit('open-campaign-portal', this.selectedAccount);
            } else {
                Swal.fire({
                    title: "<h3 class='text-black fw-semibold'>Compte?</h3>",
                    html: "<p class='text-muted fw-light text-md mb-0'>Veuillez choisir un compte avant tout</p>",
                    icon: "warning",
                });
            }
        },

        openPostPortal() {
            this.$emit('open-post-portal', { post: null, readMode: false });
        },

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
                    this.toast.success("La requête de Boost a été envoyé avec succès!");
                } else if(boostResponse.status === 404){
                    this.toast.error("Post non trouvé !");
                }
            } catch(error) {
                console.error(error);
                this.toast.error("Une erreur s'est produite lors de l'envoi de la requête !");
            }
        },

        displayCampaigns(day) {
            const checkDate = new Date(this.currentYear, this.currentMonth, day);
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

        getCampaignPost(campaign) {
            return this.allLinkedinPosts.filter(post => post.campaign_id === campaign.id);
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

        async getCampaignPostsForDate(campaign, day) {
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
            }
        },

        getCampaignsForDate(day) {
            const campaignsForDay = [];
            const checkDate = new Date(this.currentYear, this.currentMonth, day);
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
            return this.allLinkedinPosts.filter(post => {
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
};
</script>