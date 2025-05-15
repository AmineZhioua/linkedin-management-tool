<template>
    <div class="bg-white rounded-lg h-fit py-2 relative shadow-md">
        <!-- Header of the Post Card -->
        <div class="flex items-center gap-2 px-3 py-2">
            <img 
                v-if="linkedinUser && linkedinUser.linkedin_picture"
                :src="linkedinUser.linkedin_picture" 
                alt="Profile Picture" 
                class="rounded-full"
                height="50" 
                width="50"
            />
            <img 
                v-else
                src="/build/assets/images/default-profile.png" 
                alt="Default Profile Picture" 
                class="rounded-full"
                height="50" 
                width="50"
            />
            <div class="flex flex-col flex-1">
                <h4 v-if="linkedinUser" class="fw-bold mb-0" style="font-size: 15px;">
                    {{ linkedinUser.linkedin_firstname }} {{ linkedinUser.linkedin_lastname }}
                </h4>
                <h4 v-else class="fw-bold mb-0" style="font-size: 15px;">
                    Utilisateur Inconnu
                </h4>
                <div class="flex items-center gap-2">
                    <p class="text-gray-500 text-sm mb-0">1h</p>
                    <p class="text-muted mb-0">·</p>
                    <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 -960 960 960" width="18px" fill="#B7B7B7">
                        <path d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm-40-82v-78q-33 0-56.5-23.5T360-320v-40L168-552q-3 18-5.5 36t-2.5 36q0 121 79.5 212T440-162Zm276-102q41-45 62.5-100.5T800-480q0-98-54.5-179T600-776v16q0 33-23.5 56.5T520-680h-80v80q0 17-11.5 28.5T400-560h-80v80h240q17 0 28.5 11.5T600-440v120h40q26 0 47 15.5t29 40.5Z"/>
                    </svg>
                </div>
            </div>

            <!-- 3 Dots -->
            <div class="cursor-pointer relative">
                <svg 
                    @click="postDropdown = !postDropdown" 
                    xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#585a5d"
                >
                    <path d="M240-400q-33 0-56.5-23.5T160-480q0-33 23.5-56.5T240-560q33 0 56.5 23.5T320-480q0 33-23.5 56.5T240-400Zm240 0q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Zm240 0q-33 0-56.5-23.5T640-480q0-33 23.5-56.5T720-560q33 0 56.5 23.5T800-480q0 33-23.5 56.5T720-400Z"/>
                </svg>

                <ul 
                    v-if="postDropdown"
                    class="absolute border top-[30px] right-0 min-w-[150px] rounded-lg px-0 bg-white z-50"
                >
                    <li class="w-full text-center transition-all duration-200 px-4 py-3 hover:bg-gray-200">
                        <button 
                            @click="deletePost(post.id)"
                            class="text-red-600 flex items-center gap-2"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#EA3323">
                                <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/>
                            </svg>
                            <span>Supprimer</span>
                        </button>
                    </li>
                    <li class="w-full text-center transition-all duration-200 px-4 py-3 hover:bg-gray-200">
                        <button 
                            @click="isEditing"
                            class="text-blue-500 flex items-center gap-2"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#3f73f9">
                                <path d="M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z"/>
                            </svg>
                            <span>Modifier</span> 
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Content of the post Card -->
        <!-- Text Type Post -->
        <div v-if="post.type === 'text'" class="px-2 my-2">
            <p v-if="parsedContent" class="mb-0 text-black">
                {{ displayedText }}
                <span v-if="isTextLong && !isExpanded" class="text-blue-500 cursor-pointer" @click="toggleExpand">
                    voir plus
                </span>
                <span v-if="isTextLong && isExpanded" class="text-blue-500 cursor-pointer" @click="toggleExpand">
                    voir moins
                </span>
            </p>
        </div>

        <!-- Video Type Post -->
        <div v-if="post.type === 'video'" class="w-full h-auto">
            <p v-if="parsedContent" class="my-2 px-2">
                {{ displayedCaption }}
                <span v-if="isCaptionLong && !isExpanded" class="text-blue-500 cursor-pointer" @click="toggleExpand">
                    voir plus
                </span>
                <span v-if="isCaptionLong && isExpanded" class="text-blue-500 cursor-pointer" @click="toggleExpand">
                    voir moins
                </span>
            </p>
            <video controls>
                <source src="/build/assets/images/video.webm" type="video/mp4">
            </video>
        </div>

        <!-- Image Type Post -->
        <div v-if="post.type === 'image'" class="w-full h-auto">
            <p v-if="parsedContent" class="my-2 px-2">
                {{ displayedCaption }}
                <span v-if="isCaptionLong && !isExpanded" class="text-blue-500 cursor-pointer" @click="toggleExpand">
                    voir plus
                </span>
                <span v-if="isCaptionLong && isExpanded" class="text-blue-500 cursor-pointer" @click="toggleExpand">
                    voir moins
                </span>
            </p>
            <img 
                v-if="parsedContent"
                :src="getMediaUrl(parsedContent.file_path)" 
                class="object-fill" 
                alt="Picture"
            />
        </div>

        <!-- Likes & Comments & Repost Button -->
        <div class="w-full flex items-center justify-between gap-2 px-4 py-2 mt-3" style="border-top: 1px solid #ddd;">
            <!-- Like -->
            <div class="flex flex-col items-center gap-1 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" style="transform: scaleX(-1);" height="24px" viewBox="0 -960 960 960" width="24px" fill="#404040">
                    <path d="M720-120H280v-520l280-280 50 50q7 7 11.5 19t4.5 23v14l-44 174h258q32 0 56 24t24 56v80q0 7-2 15t-4 15L794-168q-9 20-30 34t-44 14Zm-360-80h360l120-280v-80H480l54-220-174 174v406Zm0-406v406-406Zm-80-34v80H160v360h120v80H80v-520h200Z"/>
                </svg>
                <span class="mb-0 text-sm">Like</span>
            </div>
            <!-- Comment -->
            <div class="flex flex-col items-center gap-1 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#404040">
                    <path d="M240-400h480v-80H240v80Zm0-120h480v-80H240v80Zm0-120h480v-80H240v80ZM880-80 720-240H160q-33 0-56.5-23.5T80-320v-480q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v720ZM160-320h594l46 45v-525H160v480Zm0 0v-480 480Z"/>
                </svg>
                <span class="mb-0 text-sm">Comment</span>
            </div>
            <!-- Repost -->
            <div class="flex flex-col items-center gap-1 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#404040">
                    <path d="M280-80 120-240l160-160 56 58-62 62h406v-160h80v240H274l62 62-56 58Zm-80-440v-240h486l-62-62 56-58 160 160-160 160-56-58 62-62H280v160h-80Z"/>
                </svg>
                <span class="mb-0 text-sm">Repost</span>
            </div>
            <!-- Send -->
            <div class="flex flex-col items-center gap-1 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" style="transform: rotate(-45deg);" height="24px" viewBox="0 -960 960 960" width="24px" fill="#404040">
                    <path d="M120-160v-640l760 320-760 320Zm80-120 474-200-474-200v140l240 60-240 60v140Zm0 0v-400 400Z"/>
                </svg>
                <span class="mb-0 text-sm">Send</span>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'LinkedinPost',

    props: {
        userLinkedinAccounts: {
            type: Array,
            required: true,
        },
        post: {
            type: Object,
            required: true,
        },
    },

    emits: ['isEditing'],

    data() {
        return {
            linkedinUser: null,
            parsedContent: null,
            isExpanded: false,
            maxLength: 90,
            postDropdown: false,
        };
    },

    mounted() {
        this.getLinkedinUserbyPost(this.post);
        this.parseContent();
    },

    computed: {
        isTextLong() {
            return this.post.type === 'text' && this.parsedContent && this.parsedContent.text && this.parsedContent.text.length > this.maxLength;
        },
        isCaptionLong() {
            return (this.post.type === 'video' || this.post.type === 'image') && this.parsedContent && this.parsedContent.caption && this.parsedContent.caption.length > this.maxLength;
        },
        displayedText() {
            if (this.post.type === 'text' && this.parsedContent && this.parsedContent.text) {
                return this.isExpanded ? this.parsedContent.text : this.parsedContent.text.slice(0, this.maxLength) + '...';
            }
            return '';
        },
        displayedCaption() {
            if ((this.post.type === 'video' || this.post.type === 'image') && this.parsedContent && this.parsedContent.caption) {
                return this.isExpanded ? this.parsedContent.caption : this.parsedContent.caption.slice(0, this.maxLength) + '...';
            }
            return '';
        }
    },

    methods: {
        getLinkedinUserbyPost(post) {
            const linkedinUserArray = this.userLinkedinAccounts.filter(
                account => account.id === post.linkedin_user_id
            );
            this.linkedinUser = linkedinUserArray.length > 0 ? linkedinUserArray[0] : null;
        },

        parseContent() {
            if (this.post.content) {
                try {
                    this.parsedContent = JSON.parse(this.post.content);
                } catch (error) {
                    console.error('Failed to parse post content:', error);
                    this.parsedContent = { text: 'Contenu Invalide' };
                }
            } else {
                this.parsedContent = { text: 'Contenu non disponible' };
            }
        },

        toggleExpand() {
            this.isExpanded = !this.isExpanded;
        },

        getMediaUrl(filePath) {
            return filePath ? `/linkedin/${filePath}` : '';
        },

        async deletePost(postId) {
            this.postDropdown = false;
            this.isLoading = true;
            try {
                const response = await axios.delete("/linkedin/delete-post", {
                    data: {
                        post_id: postId,
                        linkedin_user_id: this.post.linkedin_user_id,
                    },
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    },
                });

                if (response.status === 200) {
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

        isEditing() {
            this.postDropdown = false;
            this.$emit('isEditing', this.post);
        },
    },  
}
</script>