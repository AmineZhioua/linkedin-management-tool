<template>
    <div class="flex justify-between align-items-center">
        <div class="flex align-items-end gap-2">
            <img 
                src="/build/assets/icons/kpi-black.svg" 
                alt="Kpi Icon"
                height="45"
                width="45"
            />
            <h1 class="fw-bold text-3xl mb-0">Tes KPIs</h1>
        </div>
        <button @click="filtrerList = !filtrerList" class="flex items-center gap-2 relative text-white fw-semibold bg-black rounded-full px-4 py-2">
            <img 
                src="/build/assets/icons/filtrer-white.svg" 
                alt="Filtrer Icon" 
            />
            <span>Filtrer</span>
            <ul 
                class="list-none absolute bg-white text-black p-0 rounded-lg shadow-lg top-[45px] left-[50%] translate-x-[-50%] flex flex-col items-center justify-center" 
                v-if="filtrerList"
                id="filtrer"
            >
                <li @click="selectType('all')" class="p-2 hover:bg-gray-200 min-w-[200px] rounded-lg transition-all duration-300">Tous les Posts</li>
                <li @click="selectType('text')" class="p-2 hover:bg-gray-200 min-w-[200px] rounded-lg transition-all duration-300">Text Posts</li>
                <li @click="selectType('image')" class="p-2 hover:bg-gray-200 min-w-[200px] rounded-lg transition-all duration-300">Image Posts</li>
                <li @click="selectType('video')" class="p-2 hover:bg-gray-200 min-w-[200px] rounded-lg transition-all duration-300">Video Posts</li>
                <li @click="selectType('article')" class="p-2 hover:bg-gray-200 min-w-[200px] rounded-lg transition-all duration-300">Article Posts</li>
            </ul>
        </button>
    </div>

    <!-- KPIs Cards -->
    <div class="grid grid-cols-4 gap-4">
        <!-- Total Posts KPIs -->
        <div class="flex flex-col justify-between align-items-center py-8 px-3 rounded-xl" style="background-color: #FFB0C6;">
            <h1>{{ displayTotalPosts }}</h1>
            <h3>Total Posts</h3>
            <p class="text-muted">Hausse de XX posts</p>
        </div>

        <div class="flex flex-col justify-between align-items-center py-8 px-3 rounded-xl" style="background-color: #FFB0C6;">
            <h1>{{ displayTotalLikes }}</h1>
            <h3>Total Likes</h3>
            <p class="text-muted">Hausse de XX likes</p>
        </div>

        <div class="flex flex-col justify-between align-items-center py-8 px-3 rounded-xl" style="background-color: #FFB0C6;">
            <h1>{{ displayTotalComments }}</h1>
            <h3>Total Comments</h3>
            <p class="text-muted">Hausse de XX comments</p>
        </div>

        <div class="flex flex-col justify-between align-items-center py-8 px-3 rounded-xl" style="background-color: #FFB0C6;">
            <h1>{{ displayEngagementRate.toFixed(2) }}</h1>
            <h3 class="text-center">Taux d'engagement global</h3>
            <p class="text-muted">interactions/post</p>
        </div>

        <div class="flex flex-col justify-between align-items-center py-8 px-3 rounded-xl" style="background-color: #FFB0C6;">
            <h1>{{ displayRatioLikeComment.toFixed(2) }}</h1>
            <h3>Ratio Like/Comment</h3>
            <p class="text-muted">Type d'interaction</p>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'KpiList',

    props: {
        allUserPosts: {
            type: Array,
            required: true
        },
        userLinkedinAccounts: {
            type: Array,
            required: true
        }
    },

    data() {
        return {
            isLoading: false,
            errorMsg: "",
            filtrerList: false,
            selectedType: 'all',
            // Total KPIs
            totalLikes: 0 ,
            totalComments: 0,
            totalPosts: 0,
            engagementRate: 0,
            ratioLikeComment: 0,
            // KPIs For Text Posts
            totalTextPosts: 0,
            totalCommentsTextPosts: 0,
            totalLikesTextPosts: 0,
            engagementRateTextPosts: 0,
            // KPIs For Image Posts
            totalImagePosts: 0,
            totalCommentsImagePosts: 0,
            totalLikesImagePosts: 0,
            engagementRateImagePosts: 0,
            // KPIs For Video Posts
            totalVideoPosts: 0,
            totalCommentsVideoPosts: 0,
            totalLikesVideoPosts: 0,
            engagementRateVideoPosts: 0,
            // KPIs For Article Posts
            totalArticlePosts: 0,
            totalCommentsArticlePosts: 0,
            totalLikesArticlePosts: 0,
            engagementRateArticlePosts: 0,
        };
    },

    computed: {
        displayTotalPosts() {
            switch (this.selectedType) {
                case 'all': return this.totalPosts;
                case 'text': return this.totalTextPosts;
                case 'image': return this.totalImagePosts;
                case 'video': return this.totalVideoPosts;
                case 'article': return this.totalArticlePosts;
                default: return 0;
            }
        },
        displayTotalLikes() {
            switch (this.selectedType) {
                case 'all': return this.totalLikes;
                case 'text': return this.totalLikesTextPosts;
                case 'image': return this.totalLikesImagePosts;
                case 'video': return this.totalLikesVideoPosts;
                case 'article': return this.totalLikesArticlePosts;
                default: return 0;
            }
        },
        displayTotalComments() {
            switch (this.selectedType) {
                case 'all': return this.totalComments;
                case 'text': return this.totalCommentsTextPosts;
                case 'image': return this.totalCommentsImagePosts;
                case 'video': return this.totalCommentsVideoPosts;
                case 'article': return this.totalCommentsArticlePosts;
                default: return 0;
            }
        },
        displayEngagementRate() {
            switch (this.selectedType) {
                case 'all': return this.engagementRate;
                case 'text': return this.engagementRateTextPosts;
                case 'image': return this.engagementRateImagePosts;
                case 'video': return this.engagementRateVideoPosts;
                case 'article': return this.engagementRateArticlePosts;
                default: return 0;
            }
        },
        displayRatioLikeComment() {
            switch (this.selectedType) {
                case 'all': return this.ratioLikeComment;
                default: return 0;
            }
        },
    },

    watch: {
        allUserPosts: {
            immediate: true,
            handler(newPosts) {
                if (newPosts.length > 0) {
                    this.fetchAllKPIs();
                }
            }
        }
    },

    methods: {
        getUserLinkedinInfo(id) {
            const user = this.userLinkedinAccounts.find(user => user.id === id);
            if (user) {
                return {
                    firstName: user.linkedin_firstname,
                    lastName: user.linkedin_lastname,
                    profilePicture: user.linkedin_picture,
                    accessToken: user.linkedin_token,
                    linkedin_id: user.linkedin_id,
                };
            } else {
                this.errorMsg = "Utilisateur non trouvé !";
                return null;
            }
        },

        getAllPostsByType(type) {
            const validTypes = ['text', 'image', 'video', 'article'];
            if (!validTypes.includes(type)) {
                this.errorMsg = `Type de post non reconnu : ${type}`;
                console.error(this.errorMsg);
                return [];
            }
            return this.allUserPosts.filter(post => post.type === type);
        },

        filterPostsByStatus() {
            const postedPosts = this.allUserPosts.filter(post => post.status === 'posted');
            // this.totalPosts = postedPosts.length;
            return postedPosts;
        },


        // Likes & Comments Summary
        async getSocialActions() {
            this.isLoading = true;
            this.errorMsg = "";
            const postedPosts = this.filterPostsByStatus();

            try {
                for(let i = 0; i < postedPosts.length; i++) {
                    if(postedPosts[i].status !== 'posted') {
                        continue;
                    }
                    const post = this.allUserPosts[i];
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

                    if(response.status = 200) {
                        const data = response.data;
                        this.totalPosts++;
                        this.totalLikes += data.likesSummary.totalLikes;
                        this.totalComments += data.commentsSummary.aggregatedTotalComments;
                        this.engagementRate = (this.totalLikes + this.totalComments) / this.totalPosts;
                        this.ratioLikeComment = this.totalLikes / this.totalComments;
                        console.log(data);
                    } else if(response.status = 404) {
                        this.totalPosts--;
                        continue;
                    } else {
                        this.errorMsg = "Erreur lors de la récupération des données.";
                    }
                }
            } catch (error) {
                if (error.response && error.response.status === 400) {
                    console.error("Validation errors:", error.response.data.errors);
                    this.errorMsg = "Erreur de validation : " + JSON.stringify(error.response.data.errors);
                } else if (error.response && error.response.status === 404) {
                    console.error("Post not found:", error.response.data);
                    this.errorMsg = "Post non trouvé.";
                }
                 else {
                    console.error("Request error:", error);
                    this.errorMsg = "Erreur lors de la récupération des données.";
                }
            } finally {
                this.isLoading = false;
            }
        },

        // Likes & Comments Summary by Post Type
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
                        this.engagementRateTextPosts = 0;
                        break;
                    case 'image':
                        this.totalImagePosts = 0;
                        this.totalLikesImagePosts = 0;
                        this.totalCommentsImagePosts = 0;
                        this.engagementRateImagePosts = 0;
                        break;
                    case 'video':
                        this.totalVideoPosts = 0;
                        this.totalLikesVideoPosts = 0;
                        this.totalCommentsVideoPosts = 0;
                        this.engagementRateVideoPosts = 0;
                        break;
                    case 'article':
                        this.totalArticlePosts = 0;
                        this.totalLikesArticlePosts = 0;
                        this.totalCommentsArticlePosts = 0;
                        this.engagementRateArticlePosts = 0;
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
                                linkedin_token: linkedinUser.accessToken,
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
                                    this.engagementRateTextPosts = this.totalTextPosts > 0 
                                        ? (this.totalLikesTextPosts + this.totalCommentsTextPosts) / this.totalTextPosts 
                                        : 0;

                                    break;
                                case 'image':
                                    this.totalImagePosts++;
                                    this.totalLikesImagePosts += response.data.likesSummary.totalLikes;
                                    this.totalCommentsImagePosts += response.data.commentsSummary.aggregatedTotalComments;
                                    this.engagementRateImagePosts = this.totalImagePosts > 0 
                                        ? (this.totalLikesImagePosts + this.totalCommentsImagePosts) / this.totalImagePosts 
                                        : 0;

                                    break;
                                case 'video':
                                    this.totalVideoPosts++;
                                    this.totalLikesVideoPosts += response.data.likesSummary.totalLikes;
                                    this.totalCommentsVideoPosts += response.data.commentsSummary.aggregatedTotalComments;
                                    this.engagementRateVideoPosts = this.totalVideoPosts > 0 
                                        ? (this.totalLikesVideoPosts + this.totalCommentsVideoPosts) / this.totalVideoPosts 
                                        : 0;

                                    break;
                                case 'article':
                                    this.totalArticlePosts++;
                                    this.totalLikesArticlePosts += response.data.likesSummary.totalLikes;
                                    this.totalCommentsArticlePosts += response.data.commentsSummary.aggregatedTotalComments;
                                    this.engagementRateArticlePosts = this.totalArticlePosts > 0 
                                        ? (this.totalLikesArticlePosts + this.totalCommentsArticlePosts) / this.totalArticlePosts 
                                        : 0;

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
            await this.getSocialActions();
            this.isLoading = false;
        },

        selectType(type) {
            this.selectedType = type;
        },
    }
};
</script>


<style>

</style>