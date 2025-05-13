<template>
    <div class="w-full h-full p-4 overflow-y-scroll">
        <!-- Heading Section -->
        <div class="flex items-center justify-between">
            <!-- Accounts Buttons Sections -->
            <div class="grid grid-cols-3 gap-2">
                <div v-for="linkedinAccount in userLinkedinAccounts">
                    <div class="flex items-center justify-end gap-2 py-2 px-4 bg-blue-50 rounded-xl cursor-pointer">
                        <div class="relative">
                            <img 
                                :src="linkedinAccount.linkedin_picture" 
                                alt="Linkedin Picture" 
                                class="rounded-full"
                                height="55" 
                                width="55"
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

                        <div class="flex flex-col ml-2">
                            <h3 class="fw-bold text-xl mb-0">{{ linkedinAccount.linkedin_firstname }} {{ linkedinAccount.linkedin_lastname }}</h3>
                            <p class="mb-0 text-sm text-muted">Compte personnel</p>
                        </div>

                        <button class="cursor-pointer ml-2">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
                                <path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm70-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- New Post & Campaign Buttons Section -->
            <div class="flex items-center gap-2">
                <button @click="showPortal = true" class="cursor-pointer bg-blue-700 text-white px-3 py-2 rounded-md flex items-center gap-2">
                    <img src="/build/assets/icons/add-white.svg" alt="Add Icon" />
                    <span>Nouveau Post</span>
                </button>
                <button class="cursor-pointer bg-blue-700 text-white px-4 py-2 rounded-md flex items-center gap-2">
                    <img src="/build/assets/icons/add-white.svg" alt="Add Icon" />
                    <span>Nouveau Campagne</span>
                </button>
            </div>
        </div>

        <!-- En Attente & Brouillons & Publié & Echoué Buttons -->
        <div class="flex items-center justify-between px-2 mt-2" style="border-bottom: 1px solid #888;">
            <div class="grid grid-cols-4 gap-5">
                <button 
                    @click="filterPostsByStatus('queued')"
                    class="flex items-center justify-center py-4 px-1 gap-2 text-black"
                >
                    <span>En Attente</span>
                    <span class="px-2 bg-gray-300 rounded-full">{{ queuedPostsCount }}</span>
                </button>

                <button 
                    @click="filterPostsByStatus('draft')"
                    class="flex items-center justify-center gap-2 py-4 px-1 text-black">
                    <span>Brouillons</span>
                    <span class="px-2 bg-gray-300 rounded-full">{{ draftPostsCount }}</span>
                </button>

                <button 
                    @click="filterPostsByStatus('posted')"
                    class="flex items-center justify-center gap-2 py-4 px-1 text-black"
                >
                    <span>Publié</span>
                    <span class="px-2 bg-gray-300 rounded-full">{{ postedPostsCount }}</span>
                </button>

                <button 
                    @click="filterPostsByStatus('failed')"
                    class="flex items-center justify-center gap-2 py-4 px-1 text-black"
                >
                    <span>Echoué</span>
                    <span class="px-2 bg-gray-300 rounded-full">{{ failedPostsCount }}</span>
                </button>
            </div>

            <div class="flex items-center gap-2">
                <button>Filtrer</button>
                <button>Adjust</button>
            </div>
        </div>

        <!-- Posts Cards Container -->
        <div class="grid grid-cols-4 gap-3 w-full h-full mt-4">
            <linkedin-post 
                v-for="post in filteredPosts" 
                :key="post.id" 
                :user-linkedin-accounts="userLinkedinAccounts" 
                :post="post" 
                @isEditing="handleEditPost"
            />
        </div>
        <!-- Post Portal Component -->
        <post-portal 
            v-if="showPortal"
            :all-linkedin-accounts="userLinkedinAccounts" 
            :selected-post="selectedPost"
            @close="showPortal = false; selectedPost = null"
        />
    </div>
</template>

<script>
export default {
    name: 'UserPostsCard',

    props: {
        userLinkedinAccounts: {
            type: Array,
            required: true,
        },
        userLinkedinPosts: {
            type: Array,
            required: true,
        },
    },

    mounted() {
        console.log(this.userLinkedinAccounts);
        console.log(this.userLinkedinPosts);
    },

    data() {
        return {
            postStatus: "all",
            showPortal: false,
            selectedPost: null,
        };
    },

    computed: {
        filteredPosts() {
            if (this.postStatus === "all") {
                return this.userLinkedinPosts;
            }
            return this.userLinkedinPosts.filter(post => post.status === this.postStatus);
        },
        queuedPostsCount() {
            return this.userLinkedinPosts.filter(post => post.status === 'queued').length;
        },
        draftPostsCount() {
            return this.userLinkedinPosts.filter(post => post.status === 'draft').length;
        },
        postedPostsCount() {
            return this.userLinkedinPosts.filter(post => post.status === 'posted').length;
        },
        failedPostsCount() {
            return this.userLinkedinPosts.filter(post => post.status === 'failed').length;
        },
    },

    methods: {
        filterPostsByStatus(status) {
            this.postStatus = status;
        },
        handleEditPost(post) {
            this.selectedPost = post;
            this.showPortal = true;
        },
    },
}
</script>