<template>
    <div class="w-full h-full p-4 overflow-y-scroll">
        <!-- style="background-color: #FEF4E5;" -->
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

        <!-- Engagement Rate Charts Section -->
        <div class="flex flex-col gap-3 mt-4">
            <h3 class="fw-semibold text-2xl">Taux d'Engagement des Posts :</h3>

            <div class="grid grid-cols-4 gap-3">
                <!-- Text Posts Engagement Chart -->
                <div class="chart-container p-2 pt-4 rounded-xl shadow-lg" style="position: relative; height: 400px; background-color: #18181b;">
                    <!-- <h1 class="text-white text-lg text-center">Texte posts</h1> -->
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
                    <!-- <h1 class="text-white text-lg text-center">Image posts</h1> -->
                    <canvas id="imageEngagementChart"></canvas>
                    <div v-if="isLoading" class="absolute inset-0 rounded-xl flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
                
                <!-- Video Posts Engagement Chart -->
                <div class="chart-container p-2 pt-4 rounded-xl shadow-lg" style="position: relative; height: 400px; background-color: #18181b;">
                    <!-- <h1 class="text-white text-lg text-center">Vidéo posts</h1> -->
                    <canvas id="videoEngagementChart"></canvas>
                    <div v-if="isLoading" class="absolute inset-0 rounded-xl flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
                
                <!-- Article Posts Engagement Chart -->
                <div class="chart-container p-2 pt-4 rounded-xl shadow-lg" style="position: relative; height: 400px; background-color: #18181b;">
                    <!-- <h1 class="text-white text-lg text-center">Articles posts</h1> -->
                    <canvas id="articleEngagementChart"></canvas>
                    <div v-if="isLoading" class="absolute inset-0 rounded-xl flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="mt-6 flex items-center gap-3">
            <!-- Bar Chart for The Posts Distribution -->
            <div class="chart-container p-2 pt-4 rounded-lg shadow-lg flex-1" style="position: relative; height: 450px; background-color: #18181b;">
                <canvas id="postTypeDistributionChart"></canvas>
            </div>

            <!-- Doughnut Chart for the Posts'Publish Success Rate -->
            <div class="chart-container p-2 pt-4 rounded-lg shadow-lg" style="position: relative; height: 450px; background-color: #18181b;">
                <canvas id="postPuslishSuccesRate" class="mx-2"></canvas>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { Chart, Legend } from 'chart.js/auto';
import { color } from 'chart.js/helpers';

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
            // Bar Charts
            barChartInstance: null,
            // Doughnut Charts
            doughnutChartInstance: null,
            // Publish Success Rate
            doughnutSuccessRateChartInstance: null,
            // Engagement Charts
            textEngagementChartInstance: null,
            imageEngagementChartInstance: null,
            videoEngagementChartInstance: null,
            articleEngagementChartInstance: null,
            isLoading: false,
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
            // Error tracking per post type
            typeErrors: {
                text: '',
                image: '',
                video: '',
                article: ''
            }
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

        postStatusDistributionData() {
            const allStatus = ['posted', 'failed', 'queued', 'draft'];
            const statusCounts = allStatus.reduce((acc, type) => ({...acc, [type]: 0}), {});
            this.allLinkedinPosts.forEach(post => {
                if(post.status) {
                    statusCounts[post.status]++;
                }
            });

            return statusCounts;
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
            // this.renderDoughnutChart();
            this.renderEngagementCharts();
            this.renderPublishSuccessRateChart();
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
                    labels: ['Texte', 'Image', 'Vidéo', 'Article'],
                    datasets: [{
                        label: 'Nombre des Posts',
                        data: Object.values(this.postTypeDistributionData),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.3)', // Text
                            'rgba(54, 162, 235, 0.3)', // Image
                            'rgba(255, 206, 86, 0.3)', // Video
                            'rgba(255, 114, 240, 0.3)' // Article
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
                                font: {
                                    size: 16,
                                },
                                color: 'rgb(255, 255, 255, 1)',
                            },
                        },
                        x: {

                            title: {
                                display: true,
                                text: 'Type de Post',
                                font: {
                                    size: 16
                                },
                                color: 'rgb(255, 255, 255, 1)',
                            },
                        },
                    },
                },
            });
        },

        renderPublishSuccessRateChart() {
            const canvas = document.getElementById('postPuslishSuccesRate');
            if (!canvas) {
                console.error('Bar chart canvas not found');
                return;
            }

            if (this.doughnutSuccessRateChartInstance) {
                this.doughnutSuccessRateChartInstance.destroy();
            }

            const ctx = canvas.getContext('2d');
            this.doughnutSuccessRateChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    // labels: Object.keys(this.postStatusDistributionData),
                    labels: ['Publié', "Echoué", "En attente", "Brouillons"],
                    datasets: [{
                        label: 'Statut des Posts',
                        data: Object.values(this.postStatusDistributionData),
                        backgroundColor: [
                            'rgba(0, 255, 38, 0.5)', // posted
                            'rgba(255, 0, 21, 0.5)', // failed
                            'rgba(0, 119, 255, 0.5)', // queued
                            'rgba(255, 145, 0, 0.5)' // draft
                        ],
                        borderColor: [
                            'rgba(0, 255, 38, 1)',
                            'rgba(255, 0, 21, 1)',
                            'rgba(0, 119, 255, 1)',
                            'rgba(255, 145, 0, 1)'
                        ],
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: true,
                            position: 'top',
                            text: 'Distribution des statuts de Posts',
                            color: 'rgb(255, 255, 255, 1)'
                        },
                    },
                },
            });
        },

        // FUNCTION USED TO CALL THE DATA AND RENDER THE DOUGHNUT CHART
        // renderDoughnutChart() {
        //     const canvas = document.getElementById('postTypeDoughnutChart');
        //     if (!canvas) {
        //         console.error('Doughnut chart canvas not found');
        //         return;
        //     }
        //     if (this.doughnutChartInstance) {
        //         this.doughnutChartInstance.destroy();
        //     }
        //     const ctx = canvas.getContext('2d');
        //     this.doughnutChartInstance = new Chart(ctx, {
        //         type: 'pie',
        //         data: {
        //             labels: Object.keys(this.postTypeDistributionData),
        //             datasets: [{
        //                 label: 'Types de Posts',
        //                 data: Object.values(this.postTypeDistributionData),
        //                 backgroundColor: [
        //                     'rgba(255, 99, 132, 0.5)', // Text
        //                     'rgba(54, 162, 235, 0.5)', // Image
        //                     'rgba(255, 206, 86, 0.5)', // Video
        //                     'rgba(255, 114, 240, 0.5)' // Article
        //                 ],
        //                 borderColor: [
        //                     'rgba(255, 99, 132, 1)',
        //                     'rgba(54, 162, 235, 1)',
        //                     'rgba(255, 206, 86, 1)',
        //                     'rgba(255, 114, 240, 1)'
        //                 ],
        //                 borderWidth: 1,
        //             }],
        //         },
        //         options: {
        //             responsive: true,
        //             maintainAspectRatio: false,
        //             plugins: {
        //                 legend: {
        //                     position: 'top',
        //                 },
        //                 title: {
        //                     display: true,
        //                     position: 'bottom',
        //                     text: 'Distribution des Types de Posts',
        //                 },
        //             },
        //         },
        //     });
        // },

        // NEW FUNCTION TO RENDER ALL ENGAGEMENT CHARTS
        renderEngagementCharts() {
            this.renderTypeEngagementChart('text');
            this.renderTypeEngagementChart('image');
            this.renderTypeEngagementChart('video');
            this.renderTypeEngagementChart('article');
        },

        // FUNCTION TO RENDER ENGAGEMENT CHART FOR EACH POST TYPE
        renderTypeEngagementChart(type) {
            const capitalizedType = type.charAt(0).toUpperCase() + type.slice(1);
            const canvasId = `${type}EngagementChart`;
            const chartInstanceProperty = `${type}EngagementChartInstance`;
            
            const canvas = document.getElementById(canvasId);
            if (!canvas) {
                console.error(`${capitalizedType} engagement chart canvas not found`);
                return;
            }

            if (this[chartInstanceProperty]) {
                this[chartInstanceProperty].destroy();
            }

            const totalLikes = this[`totalLikes${capitalizedType}Posts`] || 0;
            const totalComments = this[`totalComments${capitalizedType}Posts`] || 0;
            const totalPosts = this[`total${capitalizedType}Posts`] || 0;

            if (totalPosts === 0) {
                this.typeErrors[type] = `Aucun post ${type} trouvé ou données non disponibles`;
                
                const ctx = canvas.getContext('2d');
                this[chartInstanceProperty] = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Aucune donnée'],
                        datasets: [{
                            data: [1],
                            backgroundColor: ['rgba(50, 200, 200, 0.5)'],
                            borderColor: ['rgba(200, 200, 200, 1)'],
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false,
                                
                            },
                            title: {
                                display: true,
                                text: 'Aucune donnée disponible',
                                position: 'bottom',
                            },
                        },
                    },
                });
                return;
            }

            // Create data for pie chart
            const ctx = canvas.getContext('2d');
            this[chartInstanceProperty] = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Likes', 'Commentaires'],
                    datasets: [{
                        data: [totalLikes, totalComments],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.5)', // Likes
                            'rgba(255, 206, 86, 0.5)'  // Comments
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
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
                            labels: {
                                color: 'rgba(255, 255, 255, 1)'
                            },
                        },
                        title: {
                            display: true,
                            position: 'bottom',
                            text: `${totalPosts} post(s) - ${totalLikes + totalComments} interactions`,
                            color: 'rgba(255, 255, 255, 1)'
                        },
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
            // Set loading state for this type
            this.isLoading = true;
            this.typeErrors[type] = "";
            
            const validTypes = ['text', 'image', 'video', 'article'];
            if (!validTypes.includes(type)) {
                this.typeErrors[type] = `Type de post non reconnu : ${type}`;
                console.error(this.typeErrors[type]);
                this.isLoading = false;
                return;
            }

            const postedPosts = this.filterPostsByStatus();
            console.log("Total posted posts:", postedPosts.length);
            
            const matchingPosts = postedPosts.filter(post => post.type === type);
            console.log(`Posts of type ${type}:`, matchingPosts.length);
            
            // Reset counters for this type
            const capitalizedType = type.charAt(0).toUpperCase() + type.slice(1);
            this[`total${capitalizedType}Posts`] = 0;
            this[`totalLikes${capitalizedType}Posts`] = 0;
            this[`totalComments${capitalizedType}Posts`] = 0;
            
            // Early return if no matching posts
            if (matchingPosts.length === 0) {
                this.typeErrors[type] = `Aucun post de type ${type} trouvé`;
                console.warn(this.typeErrors[type]);
                this.isLoading = false;
                // Render empty chart
                this.renderTypeEngagementChart(type);
                return;
            }

            try {
                // Process only matching posts instead of filtering in the loop
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
                                linkedin_token: linkedinUser.linkedin_token,
                            },
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            }
                        });

                        if (response.status === 200) {
                            this[`total${capitalizedType}Posts`]++;
                            this[`totalLikes${capitalizedType}Posts`] += response.data.likesSummary.totalLikes;
                            this[`totalComments${capitalizedType}Posts`] += response.data.commentsSummary.aggregatedTotalComments;
                            
                            console.log(`Social actions for post ${post.id}:`, response.data);
                        } else {
                            this.typeErrors[type] = `Erreur lors de la récupération des données pour le post ${post.id}`;
                            console.error(`Non-200 status for post ${post.id}:`, response.status);
                        }
                    } catch (innerError) {
                        if (innerError.response) {
                            if (innerError.response.status === 401) {
                                this.typeErrors[type] = `Token expiré ou invalide`;
                            } else if (innerError.response.status === 403) {
                                this.typeErrors[type] = `Permissions insuffisantes pour accéder aux données`;
                            } else if (innerError.response.status === 404) {
                                console.warn(`Post ${post.id} non trouvé, peut-être supprimé`);
                                // We don't set error for 404s as these are expected for deleted posts
                            } else {
                                this.typeErrors[type] = `Erreur: ${innerError.response.data?.error || 'Problème inconnu'}`;
                            }
                        } else {
                            console.error(`Error processing post ${post.id}:`, innerError);
                            this.typeErrors[type] = `Problème de connexion au serveur`;
                        }
                    }
                }
            } catch (error) {
                if (error.response) {
                    if (error.response.status === 400) {
                        console.error("Validation errors:", error.response.data.errors);
                        this.typeErrors[type] = `Erreur de validation : ${JSON.stringify(error.response.data.errors)}`;
                    } else if (error.response.status === 403) {
                        this.typeErrors[type] = `Vous avez besoin d'au moins 10 Posts publié pour activer vos KPIs`;
                    } else {
                        console.error("Request error status:", error.response.status);
                        this.typeErrors[type] = `Erreur lors de la récupération des données. Status: ${error.response.status}`;
                    }
                } else {
                    console.error("Request error:", error);
                    this.typeErrors[type] = "Erreur lors de la récupération des données.";
                }
            } finally {
                this.isLoading = false;
                // Update the engagement chart for this type
                this.renderTypeEngagementChart(type);
            }
        },

        async fetchAllKPIs() {
            this.isLoading = true;
            const types = ['text', 'image', 'video', 'article'];
            
            // Clear all errors
            this.errorMsg = '';
            types.forEach(type => {
                this.typeErrors[type] = '';
            });
            
            // Fetch data for each type
            for (const type of types) {
                await this.getSocialActionsByType(type);
            }
            
            this.isLoading = false;
        },
    },
};
</script>