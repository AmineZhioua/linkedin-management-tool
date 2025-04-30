<template>
    <div class="grid grid-cols-4 gap-4">
        <div class="flex flex-col justify-between align-items-center py-8 px-3 rounded-xl" style="background-color: #E6DFFF;">
            <h1 :v-model="totalLikes">
                {{ totalLikes }}
            </h1>
            <h3>Total likes</h3>
            <p class="text-muted">Hausse de XX likes</p>
            <button class="text-white fw-semibold bg-black rounded-full px-4 py-2">En savoir plus</button>
        </div>

        <div class="flex flex-col justify-between align-items-center py-8 px-3 rounded-xl" style="background-color: #C4EDDB;">
            <h1 :v-model="totalComments">
                {{ totalComments }}
            </h1>
            <h3>Total comments</h3>
            <p class="text-muted">Hausse de XX likes</p>
            <button class="text-white fw-semibold bg-black rounded-full px-4 py-2">En savoir plus</button>
        </div>

        <div class="flex flex-col justify-between align-items-center py-8 px-3 rounded-xl" style="background-color: #FFB0C6;">
            <h1 :v-model="totalPosts">
                {{ totalPosts }}
            </h1>
            <h3>Total Posts</h3>
            <p class="text-muted">Hausse de XX likes</p>
            <button class="text-white fw-semibold bg-black rounded-full px-4 py-2">En savoir plus</button>
        </div>

        <div class="flex flex-col justify-between align-items-center py-8 px-3 rounded-xl" style="background-color: #FFE4CF;">
            <h1 :v-model="engagementRate">
                {{ engagementRate }}
            </h1>
            <h3 class="text-center">Taux d'engagenment<br>global</h3>
            <p class="text-muted">Interactions/post</p>
            <button class="text-white fw-semibold bg-black rounded-full px-4 py-2">En savoir plus</button>
        </div>

        <div class="flex flex-col justify-between align-items-center py-8 px-3 rounded-xl" style="background-color: #C7EDEB;">
            <h1 :v-model="ratioLikeComment">
                {{ ratioLikeComment }}
            </h1>
            <h3>Ratio like/comment</h3>
            <p class="text-muted">Type d'interaction</p>
            <button class="text-white fw-semibold bg-black rounded-full px-4 py-2">En savoir plus</button>
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
        console.log(this.getTotalLikes());
        return {
            isLoading: false,
            errorMsg: "",
            totalLikes: 0 ,
            totalComments: 0,
            totalShares: 0,
            totalPosts: this.allUserPosts.length,
            engagementRate: 0,
            ratioLikeComment: 0,
        };
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


        async getTotalLikes() {
            this.isLoading = true;
            this.errorMsg = "";

            try {
                for(let i = 0; i < this.allUserPosts.length; i++) {
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
                        this.totalLikes += data.likesSummary.totalLikes;
                        this.totalComments += data.commentsSummary.aggregatedTotalComments;
                        this.engagementRate = (this.totalLikes + this.totalComments) / this.totalPosts;
                        this.ratioLikeComment = this.totalLikes / this.totalComments;
                        console.log(data);
                    } else {
                        this.errorMsg = "Erreur lors de la récupération des données.";
                    }
                }
            } catch (error) {
                if (error.response && error.response.status === 400) {
                    console.error("Validation errors:", error.response.data.errors);
                    this.errorMsg = "Erreur de validation : " + JSON.stringify(error.response.data.errors);
                } else {
                    console.error("Request error:", error);
                    this.errorMsg = "Erreur lors de la récupération des données.";
                }
            } finally {
                this.isLoading = false;
            }
        },
    }
};
</script>


<style>

</style>