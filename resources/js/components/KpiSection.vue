<template>
    <div class="w-full h-full p-4 overflow-y-scroll" style="background-color: #FEF4E5;">
        <div class="grid grid-cols-4 w-full gap-3">
            <!-- Compte le plus actif -->
            <div class="px-2 py-3 bg-pink-100 text-center text-black rounded-lg shadow-lg" v-if="topActiveAccount">
                <h1 class="text-3xl">Compte le plus actif</h1>
                <div class="flex flex-col items-center gap-3 mt-4">
                    <div class="relative">
                        <img 
                            v-if="topActiveAccount.picture"
                            :src="topActiveAccount.picture" 
                            alt="Linkedin Picture" 
                            class="rounded-full"
                            height="80" 
                            width="80"
                        />
                        <img 
                            v-else
                            src="/build/assets/images/default-profile.png"
                            alt="Linkedin Picture" 
                            class="rounded-full px-0"
                            height="80" 
                            width="80"
                        />
                        <div 
                            class="p-1 absolute rounded-full bottom-[-3px] right-[-5px]" 
                            style="background-color: rgb(23 92 179); border: 3px solid white;"
                        >
                            <img 
                                src="/build/assets/icons/linkedin.svg" 
                                alt="Linkedin Icon" 
                                height="15"
                                width="15"
                            />
                        </div>
                    </div>
                    <h3>{{ topActiveAccount.firstname }} {{ topActiveAccount.lastname }}</h3>
                </div>
            </div>

            <!-- Total des Posts -->
            <div class="px-2 py-3 bg-pink-100 text-center text-black rounded-lg shadow-lg gap-3 flex flex-col items-center justify-center">
                <h1 class="text-3xl">Totale</h1>
                <h1>{{ allLinkedinPosts.length }}</h1>
                <h1 class="text-3xl">Posts</h1>
            </div>

            <!-- Total des Likes -->
            <div class="px-2 py-3 bg-pink-100 text-center text-black rounded-lg shadow-lg gap-3 flex flex-col items-center justify-center">
                <h1 class="text-3xl">Totale</h1>
                <h1>{{ totalLikes }}</h1>
                <h1 class="text-3xl">Likes</h1>
            </div>

            <!-- Total des Commentaires -->
            <div class="px-2 py-3 bg-pink-100 text-center text-black rounded-lg shadow-lg gap-3 flex flex-col items-center justify-center">
                <h1 class="text-3xl">Totale</h1>
                <h1>{{ totalComments }}</h1>
                <h1 class="text-3xl">Commentaires</h1>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="mt-6">
            <h1 class="text-3xl mb-4">Distribution des Types de Posts</h1>
            <div class="chart-container bg-white p-2 pt-4 rounded-lg shadow-lg" style="position: relative; height: 450px;">
                <canvas ref="postTypeDistributionChart"></canvas>
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
            chartInstance: null,
            chartKey: 0,
        };
    },

    computed: {
        postTypeChartData() {
            Chart.defaults.font.size = 14;
            Chart.defaults.color = '#000';

            const types = ['Image', 'Article', 'Video', 'Text'];
            const counts = types.map(type => 
                this.allLinkedinPosts.filter(post => post.type === type.toLocaleLowerCase()).length
            );

            return {
                labels: types,
                datasets: [{
                    label: 'Distribution des Types de Posts',
                    data: counts,
                    backgroundColor: [
                        'rgba(249, 115, 22, 0.7)', 
                        'rgba(6, 182, 212, 0.7)', 
                        'rgb(107, 114, 128, 0.7)', 
                        'rgba(139, 92, 246, 0.7)'
                    ],
                    borderColor: [
                        'rgb(249, 115, 22)', 
                        'rgb(6, 182, 212)', 
                        'rgb(107, 114, 128)', 
                        'rgb(139, 92, 246)'
                    ],
                    borderWidth: 2,
                }]
            };
        },
    },

    mounted() {
        this.getTopAccount();
        this.getSocialActions();
        this.$nextTick(() => {
            this.renderPostTypeDistributionChart(this.postTypeChartData);
        });
    },

    watch: {
        postTypeChartData: {
            handler(newData) {
                this.$nextTick(() => {
                    this.renderPostTypeDistributionChart(newData);
                });
            },
            deep: true,
        },
    },

    beforeUnmount() {
        if (this.chartInstance) {
            this.chartInstance.destroy();
            this.chartInstance = null;
        }
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
                this.accountErrorMessage = 'Une erreur s\'est produite lors des récupérration des données';
                console.error('Error fetching top account:', error);
            }
        },

        filterPostsByStatus() {
            const postedPosts = this.allLinkedinPosts.filter(post => post.status === 'posted');
            return postedPosts;
        },

        async getSocialActions() {
            this.isLoading = true;
            this.errorMsg = "";
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
                            linkedin_token: linkedinUser.accessToken,
                        },
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        }
                    });

                    if (response.status === 200) {
                        const data = response.data;
                        this.totalLikes += data.likesSummary.totalLikes;
                        this.totalComments += data.commentsSummary.aggregatedTotalComments;
                    } else if (response.status === 404) {
                        continue;
                    } else {
                        this.errorMsg = "Erreur lors de la récupération des données.";
                    }
                }
            } catch (error) {
                if (error.response && error.response.status === 400) {
                    console.error("Validation errors:", error.response.data.errors);
                    this.errorMsg = "Erreur de validation : " + JSON.stringify(error.response.data.errors);
                } else if (error.response && error) {
                    console.error("Post not found:", error.response.data);
                    this.errorMsg = "Post non trouvé.";
                } else if (error.response && error.response.status === 403) {
                    this.errorMsg = `Vous avez besoin d'au moins 10 Posts publié pour activer vos KPIs`;
                } else {
                    console.error("Request error:", error);
                    this.errorMsg = "Erreur lors de la récupération des données.";
                }
            } finally {
                this.isLoading = false;
            }
        },

        getUserLinkedinInfo(linkedinUserId) {
            return this.allLinkedinAccounts.find(account => account.id === linkedinUserId);
        },

        renderPostTypeDistributionChart(chartData) {
            const canvas = this.$refs.postTypeDistributionChart;
            if (!canvas) {
                console.error('Canvas element not found');
                return;
            }

            const ctx = canvas.getContext('2d');
            if (!ctx) {
                console.error('Failed to get 2D context from canvas');
                return;
            }

            if (this.chartInstance) {
                this.chartInstance.destroy();
                this.chartInstance = null;
            }

            this.chartInstance = new Chart(ctx, {
                type: 'bar',
                data: chartData,
                options: {
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                            },
                            title: {
                                display: true,
                                text: 'Nombre de Posts',
                            },
                        },
                        x: {
                            title: {
                                display: false,
                                text: 'Types de Posts',
                            },
                        },
                    },
                },
            });
        },
    },
};
</script>