<template>
    <div class="w-full h-full p-4 overflow-y-scroll">
        <!-- Title & Paragraph -->
        <div class="flex flex-col">
            <div class="flex items-center gap-2 text-black">
                <img 
                    src="/build/assets/icons/kpi-black.svg" 
                    alt="KPI Icon" 
                    class="mb-1"
                    height="50"
                    width="50"
                />
                <h1 class="mb-0 fw-semibold">Vos Statistiques</h1>
            </div>
            <p class="mt-2 text-lg text-muted">
                Vue d'ensemble détaillée de vos performances, 
                basée sur des métriques essentielles pour piloter efficacement votre activité !
            </p>
        </div>

        <!-- CARDS SECTION -->
        <div class="grid grid-cols-3 gap-4 mt-4">
            <!-- TOTAL POSTS CARD -->
            <div class="flex justify-between border shadow-md p-4 rounded-lg" style="background-color: #18181b;">
                <div class="flex flex-col gap-2">
                    <h1 class="text-white text-xl">Posts</h1>
                    <h1 class="mb-0 text-white">= {{ allLinkedinPosts.length }}</h1>
                    <p class="mb-0 text-gray-300 text-sm">Nombre des posts depuis la création de votre compte</p>
                </div>
                <div class="py-2 px-3 text-center rounded-xl h-fit flex items-center justify-center" style="background-color: #8280ff;">
                    <i class="fa-solid fa-paper-plane text-3xl text-purple-1000"></i>
                </div>
            </div>

            <!-- AVERAGE POSTS PER DAY CARD -->
            <div class="flex justify-between border shadow-md p-4 rounded-lg" style="background-color: #18181b;">
                <div class="flex flex-col gap-2">
                    <h1 class="text-white text-xl">Moyenne Posts / Jour</h1>
                    <h1 class="mb-0 text-white">= {{ allLinkedinPosts.length }}</h1>
                    <p class="mb-0 text-gray-300 text-sm">Nombre des posts depuis la création de votre compte</p>
                </div>
                <div class="py-2 px-3 text-center rounded-xl h-fit flex items-center justify-center" style="background-color: #ffc53d;">
                    <i class="fa-solid fa-calendar-check text-3xl text-purple-1000"></i>
                </div>
            </div>

            <!-- BEST POST TYPE CARD -->
            <div class="flex justify-between border shadow-md p-4 rounded-lg" style="background-color: #18181b;">
                <div class="flex flex-col gap-2">
                    <h1 class="text-white text-xl">Meilleure type de Post</h1>
                    <h1 class="mb-1 text-white text-3xl fw-light">Image</h1>
                    <p class="mb-0 text-gray-300 text-sm">Nombre des posts depuis la création de votre compte</p>
                </div>
                <div class="py-2 px-3 text-center rounded-xl h-fit flex items-center justify-center" style="background-color: #da4dd0;">
                    <i class="fa-solid fa-image text-3xl text-pink-1000"></i>
                </div>
            </div>
        </div>

        <!-- Engagement Rate Charts Section -->
        <div class="flex flex-col gap-3 mt-4">
            <div class="flex items-center justify-between">
                <h3 class="fw-semibold text-2xl">Taux d'Engagement des Posts :</h3>
                <div class="relative">
                    <button 
                        @click="chooseAccountDropdown = !chooseAccountDropdown"
                        class="flex items-center gap-2 p-1 border rounded-xl text-white"
                        style="background-color: #18181b;"
                    >
                        <div class="flex p-2">
                            <span>Tous les Comptes</span>
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff">
                                <path d="M480-360 280-560h400L480-360Z"/>
                            </svg>
                        </div>
                    </button>
                    <ul 
                        v-if="chooseAccountDropdown"
                        class="absolute right-0 shadow-lg rounded-md px-0 z-50 text-white"
                        style="background-color: #18181b;"
                    >
                        <li 
                            class="flex justify-center gap-2 px-3 py-2 hover:bg-slate-500 rounded-md cursor-pointer"
                            @click="selectAccount(null)"
                        >
                            <p class="mb-0">Tous les Comptes</p>
                        </li>
                        <li 
                            v-for="linkedinAccount in allLinkedinAccounts"
                            class="flex items-center gap-2 px-3 py-2 hover:bg-slate-500 rounded-md cursor-pointer"
                            @click="selectAccount(linkedinAccount)"
                        >
                            <img 
                                :src="linkedinAccount.linkedin_picture ?? '/build/assets/images/default-profile.png'" 
                                alt="Profile Picture"
                                height="40"
                                width="40" 
                                class="rounded-full"
                            />
                            <span class="line-clamp-1">
                                {{ linkedinAccount.linkedin_firstname }} {{ linkedinAccount.linkedin_lastname }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- ALL ENGAGEMENT CHARTS FOR THE SOCIAL ACTIONS (LIKES & COMMENTS) -->
            <div class="grid grid-cols-4 gap-3">
                <!-- Text Posts Engagement Chart -->
                <div class="chart-container p-2 pt-4 rounded-xl shadow-lg" style="position: relative; height: 400px; background-color: #18181b;">
                    <canvas id="textEngagementChart"></canvas>
                    <div v-if="isLoading" class="absolute inset-0 rounded-xl flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                    <div v-if="errorMsg && !isLoading" class="text-center text-danger mt-2">{{ errorMsg }}</div>
                </div>
                
                <!-- Image Posts Engagement Chart -->
                <div class="chart-container p-2 pt-4 rounded-xl shadow-lg" style="position: relative; height: 400px; background-color: #18181b;">
                    <canvas id="imageEngagementChart"></canvas>
                    <div v-if="isLoading" class="absolute inset-0 rounded-xl flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
                
                <!-- Video Posts Engagement Chart -->
                <div class="chart-container p-2 pt-4 rounded-xl shadow-lg" style="position: relative; height: 400px; background-color: #18181b;">
                    <canvas id="videoEngagementChart"></canvas>
                    <div v-if="isLoading" class="absolute inset-0 rounded-xl flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
                
                <!-- Article Posts Engagement Chart -->
                <div class="chart-container p-2 pt-4 rounded-xl shadow-lg" style="position: relative; height: 400px; background-color: #18181b;">
                    <canvas id="articleEngagementChart"></canvas>
                    <div v-if="isLoading" class="absolute inset-0 rounded-xl flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col mt-4 gap-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000">
                        <path d="M120-240q-33 0-56.5-23.5T40-320q0-33 23.5-56.5T120-400h10.5q4.5 0 9.5 2l182-182q-2-5-2-9.5V-600q0-33 23.5-56.5T400-680q33 0 56.5 23.5T480-600q0 2-2 20l102 102q5-2 9.5-2h21q4.5 0 9.5 2l142-142q-2-5-2-9.5V-640q0-33 23.5-56.5T840-720q33 0 56.5 23.5T920-640q0 33-23.5 56.5T840-560h-10.5q-4.5 0-9.5-2L678-420q2 5 2 9.5v10.5q0 33-23.5 56.5T600-320q-33 0-56.5-23.5T520-400v-10.5q0-4.5 2-9.5L420-522q-5 2-9.5 2H400q-2 0-20-2L198-340q2 5 2 9.5v10.5q0 33-23.5 56.5T120-240Z"/>
                    </svg>
                    <h3 class="fw-semibold text-2xl mb-0">Engagement par Période :</h3>
                </div>

                <div class="flex items-center gap-2">
                    <!-- SELECT PERIOD ELEMENT -->
                    <select name="datetime-engagement" 
                        id="datetime-engagement" 
                        class="p-2 text-black bg-white rounded-lg border"
                        v-model="selectedDateRange"                        
                    >
                        <option value="3">Dernier 3 Jours</option>
                        <option value="14">Dernier 14 Jours</option>
                        <option value="30">Dernier 30 Jours</option>
                        <option value="90">Dernier 3 Mois</option>
                        <option value="all">Tous les Temps</option>
                    </select>
                    <!-- SELECT ACCOUNT FOR PERIOD FETCHING -->
                    <div class="relative">
                        <button 
                            @click="engagementPerPeriodAccountDropdown = !engagementPerPeriodAccountDropdown"
                            class="flex items-center gap-2 p-1 border rounded-xl text-white"
                            style="background-color: #18181b;"
                        >
                            <div class="flex p-2">
                                <span>Tous les Comptes</span>
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff">
                                    <path d="M480-360 280-560h400L480-360Z"/>
                                </svg>
                            </div>
                        </button>
                        <ul 
                            v-if="engagementPerPeriodAccountDropdown"
                            class="absolute right-0 shadow-lg rounded-md px-0 z-50 text-white"
                            style="background-color: #18181b;"
                        >
                            <li 
                                class="flex justify-center gap-2 px-3 py-2 hover:bg-slate-500 rounded-md cursor-pointer"
                                @click="selectAccountForEngagementPerPeriod(null)"
                            >
                                <p class="mb-0">Tous les Comptes</p>
                            </li>
                            <li 
                                v-for="linkedinAccount in allLinkedinAccounts"
                                class="flex items-center gap-2 px-3 py-2 hover:bg-slate-500 rounded-md cursor-pointer"
                                @click="selectAccountForEngagementPerPeriod(linkedinAccount)"
                            >
                                <img 
                                    :src="linkedinAccount.linkedin_picture ?? '/build/assets/images/default-profile.png'" 
                                    alt="Profile Picture"
                                    height="40"
                                    width="40" 
                                    class="rounded-full"
                                />
                                <span class="line-clamp-1">
                                    {{ linkedinAccount.linkedin_firstname }} {{ linkedinAccount.linkedin_lastname }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ENGAGEMENT PER PERIOD CHART -->
            <div class="chart-container p-2 pt-4 rounded-lg shadow-lg flex-1" style="position: relative; height: 450px; background-color: #18181b;">
                <canvas id="engagementPerPeriodChart" style="height: 450px;"></canvas>
                <div v-if="isLoading" class="absolute inset-0 rounded-lg flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
                <div v-if="noData && !isLoading" class="absolute inset-0 flex flex-col gap-3 items-center justify-center text-white text-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px" fill="#BB271A">
                        <path d="m336-280 144-144 144 144 56-56-144-144 144-144-56-56-144 144-144-144-56 56 144 144-144 144 56 56ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/>
                    </svg>
                    <p class="mb-0 fw-light">Pas de données pour ce compte</p>
                </div>
                <div v-if="engagementPeriodError && !isLoading" class="text-center text-danger mt-2">{{ engagementPeriodError }}</div>
            </div>
        </div>

        <!-- POSTS DISTRIBUTION BAR & DOUGHNUT CHARTS SECTIONS -->
        <div class="mt-6 flex items-center gap-3">
            <div class="chart-container p-2 pt-4 rounded-lg shadow-lg flex-1" style="position: relative; height: 450px; background-color: #18181b;">
                <canvas id="postTypeDistributionChart"></canvas>
            </div>
            <div class="chart-container p-2 pt-4 rounded-lg shadow-lg" style="position: relative; height: 450px; background-color: #18181b;">
                <canvas id="postPuslishSuccesRate" class="mx-2"></canvas>
            </div>
        </div>

        <!-- CAMPAIGNS STATS SECTION -->
        <div class="mt-8">
            <!-- Title & Icon -->
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" height="42px" viewBox="0 -960 960 960" width="42px" fill="#000000">
                    <path d="M720-440v-80h160v80H720Zm48 280-128-96 48-64 128 96-48 64Zm-80-480-48-64 128-96 48 64-128 96ZM200-200v-160h-40q-33 0-56.5-23.5T80-440v-80q0-33 23.5-56.5T160-600h160l200-120v480L320-360h-40v160h-80Zm240-182v-196l-98 58H160v80h182l98 58Zm120 36v-268q27 24 43.5 58.5T620-480q0 41-16.5 75.5T560-346ZM300-480Z"/>
                </svg>
                <h1 class="mb-0 fw-semibold text-2xl">Détails de Campagnes :</h1>
            </div>

            <!-- Campaigns Social Actions Chart -->
            <div class="mt-6">
                <div class="chart-container p-2 pt-4 rounded-lg shadow-lg" style="position: relative; height: 450px; background-color: #18181b;">
                    <canvas id="campaignSocialActionsChart"></canvas>
                    <div v-if="isLoading" class="absolute inset-0 rounded-xl flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-3">
                <!-- Post Volume by Campaign HORIZONTAL Chart -->
                <div class="chart-container px-4 p-2 pt-4 rounded-lg shadow-lg flex-1" style="position: relative; height: 450px; background-color: #18181b;">
                    <canvas id="postVolumeByCampaignChart"></canvas>
                    <div v-if="isLoading" class="absolute inset-0 rounded-xl flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
                <!-- Campaign Status Distribution DOUGHNUT Chart -->
                <div class="chart-container p-2 pt-4 rounded-lg shadow-lg" style="position: relative; height: 450px; background-color: #18181b;">
                    <canvas id="campaignStatusDistributionChart" class="mx-2"></canvas>
                    <div v-if="isLoading" class="absolute inset-0 rounded-xl flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mb-1" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000">
                            <path d="M340-160q-125 0-212.5-87.5T40-460q0-125 87.5-212.5T340-760q52 0 98 16.5t84 45.5l42-42 56 56-42 42q29 38 45.5 84.5T640-460q0 125-87.5 212.5T340-160Zm400 0v-488l-44 44-56-56 140-140 140 140-57 56-43-43v487h-80ZM240-800v-80h200v80H240Zm100 560q92 0 156-64t64-156q0-92-64-156t-156-64q-92 0-156 64t-64 156q0 92 64 156t156 64Zm-40-180h80v-200h-80v200Zm40-40Z"/>
                        </svg>
                        <h1 class="fw-semibold text-2xl mb-0">Campagnes longues</h1>
                    </div>

                    <h1 class="fw-bold text-3xl mb-0">VS</h1>

                    <div class="flex items-center gap-2 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mb-1" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000">
                            <path d="M340-160q-125 0-212.5-87.5T40-460q0-125 87.5-212.5T340-760q52 0 98 16.5t84 45.5l42-42 56 56-42 42q29 38 45.5 84.5T640-460q0 125-87.5 212.5T340-160Zm440 0L640-300l56-56 44 44v-488h80v487l43-43 57 56-140 140ZM240-800v-80h200v80H240Zm100 560q92 0 156-64t64-156q0-92-64-156t-156-64q-92 0-156 64t-64 156q0 92 64 156t156 64Zm-40-180h80v-200h-80v200Zm40-40Z"/>
                        </svg>
                        <h1 class="fw-semibold text-2xl mb-0">Campagnes courtes :</h1>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { Chart } from 'chart.js/auto';

export default {
    name: 'KpiSection',
    props: {
        allLinkedinAccounts: { 
            type: Array, 
            required: true 
        },
        allLinkedinPosts: { 
            type: Array, 
            required: true 
        },
        allCampaigns: { 
            type: Array, 
            required: true 
        },
    },


    data() {
        return {
            topActiveAccount: null,
            totalLikes: 0,
            totalComments: 0,
            accountErrorMessage: '',
            errorMsg: '',
            selectedWeekStart: null,
            selectedWeekEnd: null,
            chooseAccountDropdown: false,
            chosenAccount: null,
            // Engagement Per Period Variables
            engagementPerPeriodAccountDropdown: false,
            selectedAccountForEngagementPerPeriod: null,
            selectedDateRange: 'all',
            engagementPeriodTotalLikes: 0,
            engagementPeriodTotalComments: 0,
            engagementPeriodError: '',
            engagementByDate: {}, // New property for per-date data
            engagementPerPeriodChartInstance: null, // New property for chart instance
            noData: false,
            // Chart Instances
            barChartInstance: null,
            doughnutSuccessRateChartInstance: null,
            postVolumeByCampaignChartInstance: null,
            campaignStatusDistributionChart: null,
            textEngagementChartInstance: null,
            imageEngagementChartInstance: null,
            videoEngagementChartInstance: null,
            articleEngagementChartInstance: null,
            campaignSocialActionsChartInstance: null,
            isLoading: false,
            // Post Type Variables
            totalTextPosts: 0,
            totalLikesTextPosts: 0,
            totalCommentsTextPosts: 0,
            totalImagePosts: 0,
            totalLikesImagePosts: 0,
            totalCommentsImagePosts: 0,
            totalVideoPost: 0,
            totalLikesVideoPosts: 0,
            totalCommentsVideoPosts: 0,
            totalArticlePosts: 0,
            totalLikesArticlePosts: 0,
            totalCommentsArticlePosts: 0,
            // Campaign Social Actions
            campaignLikes: {},
            campaignComments: {},
            typeErrors: { text: '', image: '', video: '', article: '' }
        };
    },


    computed: {
        postTypeDistributionData() {
            const types = ['text', 'image', 'video', 'article'];
            const typeCounts = types.reduce((acc, type) => ({ ...acc, [type]: 0 }), {});
            this.allLinkedinPosts.forEach(post => {
                if (post.type) typeCounts[post.type]++;
            });
            return typeCounts;
        },

        postStatusDistributionData() {
            const allStatus = ['posted', 'failed', 'queued', 'draft'];
            const statusCounts = allStatus.reduce((acc, type) => ({ ...acc, [type]: 0 }), {});
            this.allLinkedinPosts.forEach(post => {
                if (post.status) statusCounts[post.status]++;
            });
            return statusCounts;
        },

        postVolumeByCampaign() {
            const postCountsByCampaign = {};
            this.allLinkedinPosts.forEach(post => {
                if (post.campaign_id) {
                    postCountsByCampaign[post.campaign_id] = (postCountsByCampaign[post.campaign_id] || 0) + 1;
                }
            });
            let newestCampaigns = [];
            if(this.allCampaigns.length < 6) {
                newestCampaigns = this.allCampaigns;
            } else {
                newestCampaigns = this.allCampaigns.slice(-6);
            }

            const labels = newestCampaigns.map(campaign => campaign.name);
            const data = newestCampaigns.map(campaign => postCountsByCampaign[campaign.id] || 0);

            return {
                labels,
                datasets: [{
                    label: 'Posts per Campaign',
                    data,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            };
        },

        campaignStatusDistributionData() {
            const allStatus = ['scheduled', 'failed', 'completed', 'draft'];
            const statusCounts = allStatus.reduce((acc, status) => ({ ...acc, [status]: 0 }), {});

            this.allCampaigns.forEach(campaign => {
                if (campaign.status) statusCounts[campaign.status]++;
            });
            return statusCounts;
        },
    },


    mounted() {
        this.getTopAccount();
        this.fetchAllKPIs();
        this.renderCharts();
        this.getSocialActionsByPeriod();
    },


    watch: {
        allLinkedinPosts: { 
            handler() { 
                this.renderCharts(); 
            }, 
            deep: true 
        },
        allCampaigns: { 
            handler() { 
                this.renderCharts(); 
            }, 
            deep: true 
        },
        selectedDateRange() {
            this.getSocialActionsByPeriod();
            this.noData = false;
        },
        selectedAccountForEngagementPerPeriod() {
            this.getSocialActionsByPeriod();
        },
    },


    methods: {
        selectAccount(account) {
            this.chosenAccount = account;
            this.chooseAccountDropdown = false;
            this.fetchAllKPIs();
        },

        selectAccountForEngagementPerPeriod(account) {
            this.selectedAccountForEngagementPerPeriod = account;
            this.engagementPerPeriodAccountDropdown = false;
            this.noData = false;
        },

        async getTopAccount() {
            try {
                const accountsArray = this.allLinkedinAccounts.map(account => ({ id: account.id }));
                const response = await axios.post('/linkedin/top-account', {
                    accounts: accountsArray,
                }, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                });
                if (response.status === 200) this.topActiveAccount = response.data.top_account;
            } catch (error) {
                this.accountErrorMessage = 'Une erreur s\'est produite lors de la récupération des données';
                console.error('Error fetching top account:', error);
            }
        },

        filterPostsByStatus(account = null) {
            let posts = this.allLinkedinPosts.filter(post => post.status === 'posted');
            if (account) posts = posts.filter(post => post.linkedin_user_id === account.id);
            return posts;
        },

        getUserLinkedinInfo(linkedinUserId) {
            return this.allLinkedinAccounts.find(account => account.id === linkedinUserId);
        },

        renderCharts() {
            this.renderBarChart();
            this.renderEngagementCharts();
            this.renderPublishSuccessRateChart();
            this.renderPostVolumeByCampaignChart();
            this.renderCampaignStatusDistributionChart();
            this.renderCampaignSocialActionsChart();
        },

        renderBarChart() {
            const canvas = document.getElementById('postTypeDistributionChart');
            if (!canvas) return console.error('Bar chart canvas not found');
            if (this.barChartInstance) this.barChartInstance.destroy();
            const ctx = canvas.getContext('2d');
            this.barChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Texte', 'Image', 'Vidéo', 'Article'],
                    datasets: [{
                        label: 'Nombre des Posts',
                        data: Object.values(this.postTypeDistributionData),
                        backgroundColor: ['rgba(255, 99, 132, 0.3)', 'rgba(54, 162, 235, 0.3)', 'rgba(255, 206, 86, 0.3)', 'rgba(255, 114, 240, 0.3)'],
                        borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(255, 114, 240, 1)'],
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { 
                            display: false 
                        },
                        title: {
                            display: true,
                            text: "Nombre des Posts par Type",
                            font: {
                                size: 16,
                            },
                            color: 'white'
                        } 
                    },
                    scales: {
                        y: { 
                            ticks: { 
                                color: 'rgb(255, 255, 255, 1)' 
                            }, 
                            grid: { 
                                color: 'rgb(255, 255, 255, 0.2)' 
                            }, 
                            beginAtZero: true, 
                            title: { 
                                display: true, 
                                text: 'Nombre de Posts', 
                                font: { size: 16 }, 
                                color: 'rgb(255, 255, 255, 1)' 
                            } 
                        },
                        x: { 
                            title: { 
                                display: true, 
                                text: 'Type de Post', 
                                font: { 
                                    size: 16 
                                }, 
                                color: 'rgb(255, 255, 255, 1)' 
                            } 
                        },
                    },
                },
            });
        },

        renderPublishSuccessRateChart() {
            const canvas = document.getElementById('postPuslishSuccesRate');
            if (!canvas) return console.error('Bar chart canvas not found');
            if (this.doughnutSuccessRateChartInstance) this.doughnutSuccessRateChartInstance.destroy();
            const ctx = canvas.getContext('2d');
            this.doughnutSuccessRateChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Publié', "Echoué", "En attente", "Brouillons"],
                    datasets: [{
                        label: 'Statut des Posts',
                        data: Object.values(this.postStatusDistributionData),
                        backgroundColor: ['rgba(0, 255, 38, 0.5)', 'rgba(255, 0, 21, 0.5)', 'rgba(0, 119, 255, 0.5)', 'rgba(255, 145, 0, 0.5)'],
                        borderColor: ['rgba(0, 255, 38, 1)', 'rgba(255, 0, 21, 1)', 'rgba(0, 119, 255, 1)', 'rgba(255, 145, 0, 1)'],
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { color: 'rgb(255, 255, 255)', font: { size: 12 } } },
                        title: { display: true, position: 'top', text: 'Distribution des statuts de Posts', color: 'rgb(255, 255, 255, 1)' },
                    },
                },
            });
        },
        
        renderCampaignStatusDistributionChart() {
            const canvas = document.getElementById('campaignStatusDistributionChart');
            if (!canvas) return console.error('Bar chart canvas not found');
            if (this.campaignStatusDistributionChart) this.campaignStatusDistributionChart.destroy();
            const ctx = canvas.getContext('2d');
            this.campaignStatusDistributionChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Complété', "Échoué", "En cours", "Brouillons"],
                    datasets: [{
                        label: 'Statut des Campagnes',
                        data: Object.values(this.campaignStatusDistributionData),
                        backgroundColor: ['rgba(0, 255, 38, 0.5)', 'rgba(255, 0, 21, 0.5)', 'rgba(0, 119, 255, 0.5)', 'rgba(255, 145, 0, 0.5)'],
                        borderColor: ['rgba(0, 255, 38, 1)', 'rgba(255, 0, 21, 1)', 'rgba(0, 119, 255, 1)', 'rgba(255, 145, 0, 1)'],
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { color: 'rgb(255, 255, 255)', font: { size: 12 } } },
                        title: { display: true, position: 'top', text: 'Distribution des statuts de Campagnes', color: 'rgb(255, 255, 255, 1)' },
                    },
                },
            });
        },

        renderEngagementCharts() {
            this.renderTypeEngagementChart('text');
            this.renderTypeEngagementChart('image');
            this.renderTypeEngagementChart('video');
            this.renderTypeEngagementChart('article');
        },

        renderTypeEngagementChart(type) {
            const capitalizedType = type.charAt(0).toUpperCase() + type.slice(1);
            const canvasId = `${type}EngagementChart`;
            const chartInstanceProperty = `${type}EngagementChartInstance`;
            const canvas = document.getElementById(canvasId);
            if (!canvas) return console.error(`${capitalizedType} engagement chart canvas not found`);
            if (this[chartInstanceProperty]) this[chartInstanceProperty].destroy();
            const totalLikes = this[`totalLikes${capitalizedType}Posts`] || 0;
            const totalComments = this[`totalComments${capitalizedType}Posts`] || 0;
            const totalPosts = this[`total${capitalizedType}Posts`] || 0;
            if (totalPosts === 0) {
                this.typeErrors[type] = `Aucun post ${type} trouvé ou données non disponibles`;
                const ctx = canvas.getContext('2d');
                this[chartInstanceProperty] = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: [`Aucune donnée pour ${type} posts`],
                        datasets: [{ data: [1], backgroundColor: ['rgba(50, 200, 200, 0.5)'], borderColor: ['rgba(200, 200, 200, 1)'] }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            title: { display: true, text: `${type.charAt(0).toUpperCase()}${type.slice(1)} Posts: Aucune donnée disponible`, position: 'bottom', color: 'rgb(255, 255, 255, 1)' },
                        },
                    },
                });
                return;
            }
            const ctx = canvas.getContext('2d');
            this[chartInstanceProperty] = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Likes', 'Commentaires'],
                    datasets: [{
                        data: [totalLikes, totalComments],
                        backgroundColor: ['rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)'],
                        borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)'],
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { color: 'rgba(255, 255, 255, 1)' } },
                        title: { display: true, position: 'bottom', text: `${totalPosts} ${type} post(s) - ${totalLikes + totalComments} interactions`, color: 'rgba(255, 255, 255, 1)' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = totalLikes + totalComments;
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                },
            });
        },

        renderPostVolumeByCampaignChart() {
            const canvas = document.getElementById('postVolumeByCampaignChart');
            if (!canvas) return console.error('Post Volume by Campaign chart canvas not found');
            if (this.postVolumeByCampaignChartInstance) this.postVolumeByCampaignChartInstance.destroy();
            const ctx = canvas.getContext('2d');
            this.postVolumeByCampaignChartInstance = new Chart(ctx, {
                type: 'bar',
                data: this.postVolumeByCampaign,
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { 
                            display: false 
                        },
                        title: {
                            display: true,
                            text: 'Nombres des Posts par Campagnes',
                            color: 'white',
                            font: {
                                size: 16
                            }
                        }
                    },
                    scales: {
                        x: { beginAtZero: true, title: { display: true, text: 'Nombre des Posts', color: 'rgb(255, 255, 255, 1)' }, ticks: { color: 'white' } },
                        y: { title: { display: false, text: 'Campaigns', color: 'white' }, ticks: { color: 'white', autoSkip: false, maxRotation: 0, minRotation: 0 }, grid: { color: 'rgb(255, 255, 255, 0.2)' } }
                    }
                }
            });
        },

        async getSocialActionsByPeriod() {
            this.isLoading = true;
            this.engagementPeriodError = '';

            const now = new Date();
            let startDate;

            if (this.selectedDateRange === 'all') {
                startDate = null;
            } else {
                const days = parseInt(this.selectedDateRange, 10);
                startDate = new Date(now.setDate(now.getDate() - days));
                startDate.setHours(0, 0, 0, 0);
            }

            const endDate = new Date();
            endDate.setHours(0, 0, 0, 0);

            const selectedAccount = this.selectedAccountForEngagementPerPeriod;

            let posts = this.allLinkedinPosts.filter(post => post.status === 'posted');
            if (selectedAccount) {
                posts = posts.filter(post => post.linkedin_user_id === selectedAccount.id);
            }
            if (startDate) {
                posts = posts.filter(post => {
                    const postDate = new Date(post.scheduled_time);
                    postDate.setHours(0, 0, 0, 0);
                    return postDate >= startDate && postDate <= endDate;
                });
            }

            // if(posts.length === 0) {
            //     this.noData = true;
            //     return;
            // }

            const engagementByDate = {};
            for (const post of posts) {
                const postDate = new Date(post.scheduled_time);
                postDate.setHours(0, 0, 0, 0);
                const dateKey = postDate.toISOString().split('T')[0];
                if (!engagementByDate[dateKey]) {
                    engagementByDate[dateKey] = { likes: 0, comments: 0 };
                }
                try {
                    const linkedinUser = this.getUserLinkedinInfo(post.linkedin_user_id);
                    const response = await axios.get('/linkedin/get-social-actions', {
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
                    });
                    if (response.status === 200) {
                        engagementByDate[dateKey].likes += response.data.likesSummary?.totalLikes || 0;
                        engagementByDate[dateKey].comments += response.data.commentsSummary?.aggregatedTotalComments || 0;
                    }
                } catch (error) {
                    console.error(`Error fetching social actions for post ${post.id}:`, error);
                }
            }

            this.engagementByDate = engagementByDate;
            this.isLoading = false;
            this.renderEngagementPerPeriodChart();
        },

        async getSocialActionsByType(type) {
            this.isLoading = true;
            this.typeErrors[type] = "";

            const validTypes = ['text', 'image', 'video', 'article'];
            
            if (!validTypes.includes(type)) {
                this.typeErrors[type] = `Type de post non reconnu : ${type}`;
                console.error(this.typeErrors[type]);
                this.isLoading = false;
                return;
            }

            const postedPosts = this.filterPostsByStatus(this.chosenAccount);
            const matchingPosts = postedPosts.filter(post => post.type === type);
            const capitalizedType = type.charAt(0).toUpperCase() + type.slice(1);

            this[`total${capitalizedType}Posts`] = 0;
            this[`totalLikes${capitalizedType}Posts`] = 0;
            this[`totalComments${capitalizedType}Posts`] = 0;

            if (matchingPosts.length === 0) {
                this.typeErrors[type] = `Aucun post de type ${type} trouvé`;
                this.isLoading = false;
                this.renderTypeEngagementChart(type);
                return;
            }

            try {
                for (let i = 0; i < matchingPosts.length; i++) {
                    const post = matchingPosts[i];
                    const linkedinUser = this.getUserLinkedinInfo(post.linkedin_user_id);
                    if (!linkedinUser) {
                        console.error(`No LinkedIn user found for linkedin_user_id: ${post.linkedin_user_id}`);
                        this.typeErrors[type] = `Utilisateur LinkedIn non trouvé pour le post ${post.id}`;
                        continue;
                    }
                    try {
                        const response = await axios.get('/linkedin/get-social-actions', {
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
                        });

                        if (response.status === 200) {
                            this[`total${capitalizedType}Posts`]++;
                            this[`totalLikes${capitalizedType}Posts`] += response.data.likesSummary.totalLikes;
                            this[`totalComments${capitalizedType}Posts`] += response.data.commentsSummary.aggregatedTotalComments;
                        } else {
                            this.typeErrors[type] = `Erreur lors de la récupération des données pour le post ${post.id}`;
                        }
                    } catch (innerError) {
                        if (innerError.response) {
                            if (innerError.response.status === 401) this.typeErrors[type] = `Token expiré ou invalide`;
                            else if (innerError.response.status === 403) this.typeErrors[type] = `Permissions insuffisantes pour accéder aux données`;
                            else if (innerError.response.status === 404) console.warn(`Post ${post.id} non trouvé, peut-être supprimé`);
                            else this.typeErrors[type] = `Erreur: ${innerError.response.data?.error || 'Problème inconnu'}`;
                        } else {
                            console.error(`Error processing post ${post.id}:`, innerError);
                            this.typeErrors[type] = `Problème de connexion au serveur`;
                        }
                    }
                }
            } catch (error) {
                if (error.response) {
                    if (error.response.status === 400) this.typeErrors[type] = `Erreur de validation : ${JSON.stringify(error.response.data.errors)}`;
                    else if (error.response.status === 403) this.typeErrors[type] = `Vous avez besoin d'au moins 10 Posts publié pour activer vos KPIs`;
                    else this.typeErrors[type] = `Erreur lors de la récupération des données. Status: ${error.response.status}`;
                } else {
                    console.error("Request error:", error);
                    this.typeErrors[type] = "Erreur lors de la récupération des données.";
                }
            } finally {
                this.isLoading = false;
                this.renderTypeEngagementChart(type);
            }
        },

        async getSocialActionsByCampaign() {
            this.isLoading = true;
            for (const campaign of this.allCampaigns) {
                let totalLikes = 0;
                let totalComments = 0;

                const campaignPosts = this.allLinkedinPosts.filter(post => {
                    return post.campaign_id === campaign.id && post.status === 'posted'
                });

                if(campaignPosts.length === 0) {
                    return;
                }

                for (const post of campaignPosts) {
                    try {
                        const linkedinUser = this.getUserLinkedinInfo(post.linkedin_user_id);

                        const response = await axios.get('/linkedin/get-social-actions', {
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
                        });
                        if (response.status === 200) {
                            totalLikes += response.data.likesSummary?.totalLikes || 0;
                            totalComments += response.data.commentsSummary?.aggregatedTotalComments || 0;
                        }
                    } catch (error) {
                        if (error.response) {
                            const status = error.response.status;
                            if (status === 404) {
                                console.warn(`Post ${post.id} not found, skipping`);
                            } else if (status === 401) {
                                console.error('Token expired or invalid for post', post.id);
                            } else if (status === 403) {
                                console.warn('Insufficient permissions for post', post.id);
                            } else {
                                console.error(`Error fetching social actions for post ${post.id}:`, error.response.data);
                            }
                        } else {
                            console.error('Network error for post', post.id, error);
                        }
                    }
                }
                this.campaignLikes[campaign.id] = totalLikes;
                this.campaignComments[campaign.id] = totalComments;
            }
            this.isLoading = false;
            this.renderCampaignSocialActionsChart();
        },

        renderCampaignSocialActionsChart() {
            const canvas = document.getElementById('campaignSocialActionsChart');
            if (!canvas) {
                console.error('Campaign Social Actions chart canvas not found');
                return;
            }

            if (this.campaignSocialActionsChartInstance) {
                this.campaignSocialActionsChartInstance.destroy();
            }

            // Validate campaign data
            if (!this.allCampaigns || !Array.isArray(this.allCampaigns) || this.allCampaigns.length === 0) {
                console.warn('No campaigns available to render');
                canvas.parentElement.innerHTML = 'Pas de données pour le moment';
                return;
            }

            const ctx = canvas.getContext('2d');
            const labels = this.allCampaigns.map(campaign => campaign.name);
            const likesData = this.allCampaigns.map(campaign => this.campaignLikes[campaign.id] || 0);
            const commentsData = this.allCampaigns.map(campaign => this.campaignComments[campaign.id] || 0);

            this.campaignSocialActionsChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Likes', data: likesData, backgroundColor: 'rgba(54, 162, 235, 0.5)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 1 },
                        { label: 'Commentaires', data: commentsData, backgroundColor: 'rgba(255, 206, 86, 0.5)', borderColor: 'rgba(255, 206, 86, 1)', borderWidth: 1 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            title: { 
                                display: true, 
                                text: 'Nombre d\'Interactions', 
                                color: 'white' 
                            }, 
                            ticks: { 
                                color: 'white' 
                            },
                            grid: {
                                color: 'rgb(255, 255, 255, 0.2)'
                            }
                        },
                        x: { title: { display: true, text: 'Campagnes', color: 'white' }, ticks: { color: 'white' } }
                    },
                    plugins: { 
                        legend: { 
                            position: 'top', 
                            labels: { 
                                color: 'white' 
                            } 
                        },
                        title: {
                            display: true,
                            text: 'Interactions par Campagne',
                            color: 'white',
                            font: {
                                size: 16,
                            },
                        } 
                    }
                }
            });
        },

        renderEngagementPerPeriodChart() {
            const canvas = document.getElementById('engagementPerPeriodChart');
            if (!canvas) return console.error('Engagement Per Period chart canvas not found');
            if (this.engagementPerPeriodChartInstance) this.engagementPerPeriodChartInstance.destroy();

            const dates = Object.keys(this.engagementByDate).sort();
            if (dates.length === 0) {
                this.noData = true;
                return;
            }

            const likesData = dates.map(date => this.engagementByDate[date].likes);
            const commentsData = dates.map(date => this.engagementByDate[date].comments);

            const ctx = canvas.getContext('2d'); 
            this.engagementPerPeriodChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dates,
                    datasets: [
                        { 
                            label: 'Likes', 
                            data: likesData, 
                            backgroundColor: 'rgba(54, 162, 235, 0.5)', 
                            borderColor: 'rgba(54, 162, 235, 1)', 
                            borderWidth: 1 
                        },
                        { 
                            label: 'Commentaires', 
                            data: commentsData, 
                            backgroundColor: 'rgba(255, 206, 86, 0.5)', 
                            borderColor: 'rgba(255, 206, 86, 1)', 
                            borderWidth: 1 
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            title: { display: true, text: 'Nombre d\'Interactions', color: 'white' }, 
                            ticks: { color: 'white' },
                            grid: { color: 'rgb(255, 255, 255, 0.2)', } 
                        },
                        x: { 
                            title: { display: true, text: 'Date', color: 'white' }, 
                            ticks: { color: 'white' } 
                        }
                    },
                    plugins: { 
                        legend: { position: 'top', labels: { color: 'white' } },
                        title: { 
                            display: true, 
                            text: 'Engagement par Période', 
                            color: 'white', 
                            font: { size: 16 } 
                        }
                    }
                }
            });
        },

        async fetchAllKPIs() {
            this.isLoading = true;
            const types = ['text', 'image', 'video', 'article'];
            this.errorMsg = '';
            types.forEach(type => {
                this.typeErrors[type] = '';
                const capitalizedType = type.charAt(0).toUpperCase() + type.slice(1);
                this[`total${capitalizedType}Posts`] = 0;
                this[`totalLikes${capitalizedType}Posts`] = 0;
                this[`totalComments${capitalizedType}Posts`] = 0;
            });
            for (const type of types) {
                await this.getSocialActionsByType(type);
            }
            await this.getSocialActionsByCampaign();
            this.isLoading = false;
        },
    },
};
</script>