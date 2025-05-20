<template>
    <div class="w-full h-full p-4 overflow-y-scroll" style="background-color: #FEF4E5;">
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
                <h1 class="mb-0 fw-semibold">Vos KPIs</h1>
            </div>

            <p class="mt-2 text-lg text-muted">
                Vue d'ensemble détaillée de vos performances, 
                basée sur des métriques essentielles pour piloter efficacement votre activité !
            </p>
        </div>


        <div class="flex flex-col gap-3 mt-4">
            <h3 class="fw-semibold text-2xl">Taux d'Engagement :</h3>

            <div class="grid grid-cols-4 gap-3">
                <div class="chart-container bg-white p-2 pt-4 rounded-lg shadow-lg" style="position: relative; height: 450px;">
                    <canvas id="postTypeDoughnutChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="mt-6">
            <h1 class="text-3xl mb-4">Distribution des Types de Posts</h1>
            <div class="chart-container bg-white p-2 pt-4 rounded-lg shadow-lg" style="position: relative; height: 450px;">
                <canvas id="postTypeDistributionChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart Section -->
        <!-- <div class="mt-6">
            <h1 class="text-3xl mb-4">Distribution des Types de Posts</h1>
            <div class="chart-container bg-white p-2 pt-4 rounded-lg shadow-lg" style="position: relative; height: 450px;">
                <canvas id="postTypeDoughnutChart"></canvas>
            </div>
        </div> -->
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

    data() {
        return {
            topActiveAccount: null,
            totalLikes: 0,
            totalComments: 0,
            accountErrorMessage: '',
            errorMsg: '',
            selectedWeekStart: null,
            selectedWeekEnd: null,
            currentWeekOffset: 0,
            barChartInstance: null,
            doughnutChartInstance: null,
            // Text Posts Variables
            totalTextPosts: 0,
            totalLikesTextPosts: 0,
            totalCommentsTextPosts: 0,
            // Image Posts Variables
            totalImagePosts: 0,
            totalLikesImagePosts: 0,
            totalCommentsImagePosts: 0,
            // Video Posts Variables
            totalVideoPost: 0,
            totalLikesVideoPosts: 0,
            totalCommentsVideoPosts: 0,
            // Article Posts Variables
            totalArticlePosts: 0,
            totalLikesArticlePosts: 0,
            totalCommentsArticlePosts: 0,
        };
    },

    computed: {
        postTypeDistributionData() {
            const types = ['text', 'image', 'video', 'article'];
            const typeCounts = types.reduce((acc, type) => ({ ...acc, [type]: 0 }), {});
            this.allLinkedinPosts.forEach(post => {
                if(post.type) {
                    typeCounts[post.type]++;
                }
            });
            return typeCounts;
        },
    },

    mounted() {
        this.getTopAccount();
        this.fetchAllKPIs();
        this.renderCharts();
    },

    watch: {
        allLinkedinPosts: {
            handler() {
                this.renderCharts();
            },
            deep: true,
        },
    },

    methods: {
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

                if (response.status === 200) {
                    this.topActiveAccount = response.data.top_account;
                }
            } catch (error) {
                this.accountErrorMessage = 'Une erreur s\'est produite lors de la récupération des données';
                console.error('Error fetching top account:', error);
            }
        },

        filterPostsByStatus() {
            return this.allLinkedinPosts.filter(post => post.status === 'posted');
        },

        getUserLinkedinInfo(linkedinUserId) {
            return this.allLinkedinAccounts.find(account => account.id === linkedinUserId);
        },

        renderCharts() {
            this.renderBarChart();
            this.renderDoughnutChart();
        },

        // FUNCTION USED TO CALL THE DATA AND RENDER THE BAR CHART
        renderBarChart() {
            const canvas = document.getElementById('postTypeDistributionChart');
            if (!canvas) {
                console.error('Bar chart canvas not found');
                return;
            }

            if (this.barChartInstance) {
                this.barChartInstance.destroy();
            }

            const ctx = canvas.getContext('2d');
            this.barChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(this.postTypeDistributionData),
                    datasets: [{
                        label: 'Types de Posts',
                        data: Object.values(this.postTypeDistributionData),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.5)', // Text
                            'rgba(54, 162, 235, 0.5)', // Image
                            'rgba(255, 206, 86, 0.5)', // Video
                            'rgba(255, 114, 240, 0.5)' // Article
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(255, 114, 240, 1)'
                        ],
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Nombre de Posts',
                            },
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Type de Post',
                            },
                        },
                    },
                },
            });
        },

        // FUNCTION USED TO CALL THE DATA AND RENDER THE DOUGHNUT CHART
        renderDoughnutChart() {
            const canvas = document.getElementById('postTypeDoughnutChart');
            if (!canvas) {
                console.error('Doughnut chart canvas not found');
                return;
            }
            if (this.doughnutChartInstance) {
                this.doughnutChartInstance.destroy();
            }
            const ctx = canvas.getContext('2d');
            this.doughnutChartInstance = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: Object.keys(this.postTypeDistributionData),
                    datasets: [{
                        label: 'Types de Posts',
                        data: Object.values(this.postTypeDistributionData),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.5)', // Text
                            'rgba(54, 162, 235, 0.5)', // Image
                            'rgba(255, 206, 86, 0.5)', // Video
                            'rgba(255, 114, 240, 0.5)' // Article
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(255, 114, 240, 1)'
                        ],
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            position: 'bottom',
                            text: 'Distribution des Types de Posts',
                        },
                    },
                },
            });
        },


        async getSocialActions() {
            this.isLoading = true;
            this.errorMsg = '';
            const postedPosts = this.filterPostsByStatus();

            try {
                for (let i = 0; i < postedPosts.length; i++) {
                    const post = postedPosts[i];
                    const linkedinUser = this.getUserLinkedinInfo(post.linkedin_user_id);
                    const response = await axios.get('/linkedin/get-social-actions', {
                        params: {
                            post_id: post.id,
                            urn: encodeURIComponent(post.post_urn),
                            linkedin_user_id: post.linkedin_user_id,
                            linkedin_token: linkedinUser.linkedin_token,
                        },
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                    });

                    if (response.status === 200) {
                        const data = response.data;
                        this.totalLikes += data.likesSummary.totalLikes;
                        this.totalComments += data.commentsSummary.aggregatedTotalComments;
                    } else if (response.status === 404) {
                        continue;
                    } else {
                        this.errorMsg = 'Erreur lors de la récupération des données.';
                    }
                }
            } catch (error) {
                if (error.response && error.response.status === 400) {
                    console.error('Validation errors:', error.response.data.errors);
                    this.errorMsg = 'Erreur de validation : ' + JSON.stringify(error.response.data.errors);
                } else if (error.response) {
                    console.error('Post not found:', error.response.data);
                    this.errorMsg = 'Post non trouvé.';
                } else if (error.response && error.response.status === 403) {
                    this.errorMsg = `Vous avez besoin d'au moins 10 Posts publié pour activer vos KPIs`;
                } else {
                    console.error('Request error:', error);
                    this.errorMsg = 'Erreur lors de la récupération des données.';
                }
            } finally {
                this.isLoading = false;
            }
        },


        async getSocialActionsByType(type) {
            this.isLoading = true;
            this.errorMsg = "";
            const postedPosts = this.filterPostsByStatus();

            const validTypes = ['text', 'image', 'video', 'article'];
            if (!validTypes.includes(type)) {
                this.errorMsg = `Type de post non reconnu : ${type}`;
                console.error(this.errorMsg);
                this.isLoading = false;
                return;
            }

            const matchingPosts = postedPosts.filter(post => post.type === type);
            if (matchingPosts.length === 0) {
                this.errorMsg = `Aucun post de type ${type} trouvé`;
                console.warn(this.errorMsg);
                switch (type) {
                    case 'text':
                        this.totalTextPosts = 0;
                        this.totalLikesTextPosts = 0;
                        this.totalCommentsTextPosts = 0;
                        break;
                    case 'image':
                        this.totalImagePosts = 0;
                        this.totalLikesImagePosts = 0;
                        this.totalCommentsImagePosts = 0;
                        break;
                    case 'video':
                        this.totalVideoPosts = 0;
                        this.totalLikesVideoPosts = 0;
                        this.totalCommentsVideoPosts = 0;
                        break;
                    case 'article':
                        this.totalArticlePosts = 0;
                        this.totalLikesArticlePosts = 0;
                        this.totalCommentsArticlePosts = 0;
                        break;
                }
                this.isLoading = false;
                return;
            }

            try {
                for (let i = 0; i < postedPosts.length; i++) {
                    if (postedPosts[i].type === type) {
                        const post = postedPosts[i];
                        const linkedinUser = this.getUserLinkedinInfo(post.linkedin_user_id);

                        if (!linkedinUser) {
                            console.error(`No LinkedIn user found for linkedin_user_id: ${post.linkedin_user_id}`);
                            this.errorMsg = `Utilisateur LinkedIn non trouvé pour le post ${post.id}`;
                            continue;
                        }

                        const response = await axios.get('/linkedin/get-social-actions', {
                            params: {
                                post_id: post.id,
                                urn: encodeURIComponent(post.post_urn),
                                linkedin_user_id: post.linkedin_user_id,
                                linkedin_token: linkedinUser.linkedin_token,
                            },
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            }
                        });

                        if (response.status === 200) {
                            switch (type) {
                                case 'text':
                                    this.totalTextPosts++;
                                    this.totalLikesTextPosts += response.data.likesSummary.totalLikes;
                                    this.totalCommentsTextPosts += response.data.commentsSummary.aggregatedTotalComments;

                                    break;
                                case 'image':
                                    this.totalImagePosts++;
                                    this.totalLikesImagePosts += response.data.likesSummary.totalLikes;
                                    this.totalCommentsImagePosts += response.data.commentsSummary.aggregatedTotalComments;

                                    break;
                                case 'video':
                                    this.totalVideoPosts++;
                                    this.totalLikesVideoPosts += response.data.likesSummary.totalLikes;
                                    this.totalCommentsVideoPosts += response.data.commentsSummary.aggregatedTotalComments;

                                    break;
                                case 'article':
                                    this.totalArticlePosts++;
                                    this.totalLikesArticlePosts += response.data.likesSummary.totalLikes;
                                    this.totalCommentsArticlePosts += response.data.commentsSummary.aggregatedTotalComments;

                                    break;
                            }
                            console.log(`Social actions for post ${post.id}:`, response.data);
                        } else {
                            this.errorMsg = `Erreur lors de la récupération des données pour le post ${post.id}`;
                            console.error(`Non-200 status for post ${post.id}:`, response.status);
                        }
                    }
                }
            } catch (error) {
                if (error.response && error.response.status === 400) {
                    console.error("Validation errors:", error.response.data.errors);
                    this.errorMsg = `Erreur de validation : ${JSON.stringify(error.response.data.errors)}`;
                } else if(error.response.status === 403) {
                    this.errorMsg = `Vous avez besoin d'au moins 10 Posts publié pour activer vos KPIs`;
                } else {
                    console.error("Request error:", error);
                    this.errorMsg = "Erreur lors de la récupération des données.";
                }
            } finally {
                this.isLoading = false;
            }
        },


        async fetchAllKPIs() {
            this.isLoading = true;
            const types = ['text', 'image', 'video', 'article'];
            for (const type of types) {
                await this.getSocialActionsByType(type);
            }
            // await this.getSocialActions();
            this.isLoading = false;
        },

    },
};
</script>