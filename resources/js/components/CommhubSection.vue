<template>
    <div class="w-full h-full p-4 overflow-y-scroll">
        <!-- Title & Paragraph -->
        <div class="flex flex-col">
            <div class="flex items-center gap-2 text-black">
                <svg xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px" fill="#000000">
                    <path d="M0-240v-63q0-43 44-70t116-27q13 0 25 .5t23 2.5q-14 21-21 44t-7 48v65H0Zm240 0v-65q0-32 17.5-58.5T307-410q32-20 76.5-30t96.5-10q53 0 97.5 10t76.5 30q32 20 49 46.5t17 58.5v65H240Zm540 0v-65q0-26-6.5-49T754-397q11-2 22.5-2.5t23.5-.5q72 0 116 26.5t44 70.5v63H780Zm-455-80h311q-10-20-55.5-35T480-370q-55 0-100.5 15T325-320ZM160-440q-33 0-56.5-23.5T80-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T160-440Zm640 0q-33 0-56.5-23.5T720-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T800-440Zm-320-40q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T600-600q0 50-34.5 85T480-480Zm0-80q17 0 28.5-11.5T520-600q0-17-11.5-28.5T480-640q-17 0-28.5 11.5T440-600q0 17 11.5 28.5T480-560Zm1 240Zm-1-280Z"/>
                </svg>
                <h1 class="mb-0 fw-semibold">CommHub</h1>
            </div>
            <p class="mt-2 text-lg text-muted">
                Votre espace centralisé pour gérer et interagir avec tous les commentaires sur vos publications LinkedIn !
            </p>
        </div>

        <!-- Main Section -->
        <div class="grid comment-container grid-cols-3 gap-4 mt-2 max-h-[800px]">
            <!-- Comments List -->
            <div class="h-[800px] flex flex-col relative items-center gap-2 overflow-y-scroll p-2 rounded-xl" style="background-color: #18181b;">
                <!-- Published Posts List -->
                <div 
                    v-if="publishedPosts.length === 0" 
                    class="h-full text-white absolute top-1/2"
                >
                    <h1 class="fw-semibold text-xl">Aucun Posts Publiés</h1>
                </div>

                <div 
                    v-else
                    class="flex items-center w-full rounded-lg gap-3 cursor-pointer text-white" 
                    v-for="post in publishedPosts"
                    @click="changeSelectedPost(post)"
                    :class="selectedPost.id === post.id ? 'selected-post' : 'bg-black'"
                    :key="post.id"
                >
                    <!-- Media Display Based on Type -->
                    <div v-if="post.type === 'image' || (post.type === 'multiimage' && parseContent(post.content, post.type).file_paths?.length > 0)" 
                        class="w-[200px] h-auto"
                    >
                        <img 
                            :src="`/linkedin/${post.type === 'image' ? parseContent(post.content, post.type).file_path : parseContent(post.content, post.type).file_paths[0]}`" 
                            alt="Post Image" 
                            class="object-fill image-border"
                        />
                    </div>
                    <div v-else-if="post.type === 'video'" class="w-[200px] h-auto" style="background-color: #18181b;">
                        <video controls class="image-border">
                            <source :src="`/linkedin/${parseContent(post.content, post.type).file_path}`" type="video/mp4">
                            Votre navigateur ne supporte pas la vidéo.
                        </video>
                    </div>
                    <div v-if="post.type === 'text'" class="w-[200px] h-auto p-2">
                        <ScrollPanel style="width: 100%; height: 100px;">
                            <p>{{ parseContent(post.content, post.type).text }}</p>
                        </ScrollPanel>
                    </div>

                    <!-- Post Info -->
                    <div class="flex flex-col gap-2 flex-1">
                        <div class="flex items-center gap-2">
                            <img 
                                :src="getProfilePicture(userLinkedinAccounts, post.linkedin_user_id)" 
                                alt="Profile Picture" 
                                height="30"
                                width="30"
                                class="rounded-full"
                            />
                            <h4 class="text-sm mb-0">{{ getUsername(userLinkedinAccounts, post.linkedin_user_id) }}</h4>
                        </div>
                        <p class="mb-0 text-gray-300 text-sm cursor-pointer"><u>Cliquez pour afficher les détails</u></p>
                    </div>

                    <!-- Post Status -->
                    <div class="flex flex-col gap-2 items-end mr-4">
                        <p class="mb-0 text-white fw-semibold text-xs line-clamp-1">
                            {{ formatDateForEngagement(post.scheduled_time) }}
                        </p>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="rgb(0 176 72)">
                            <path d="m424-296 282-282-56-56-226 226-114-114-56 56 170 170Zm56 216q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Published Post Preview -->
            <div class="h-[800px] relative flex flex-col items-center gap-2 p-2 rounded-xl" style="background-color: #18181b;">
                <div 
                    v-if="publishedPosts.length === 0" 
                    class="h-full text-white absolute top-1/2"
                >
                    <h1 class="fw-semibold text-xl">Aucun Post selectionné</h1>
                </div>

                <div v-else>
                    <linkedin-post 
                        :key="selectedPost?.id" 
                        :user-linkedin-accounts="userLinkedinAccounts" 
                        :post="selectedPost"
                    />
                </div>
            </div>

            <!-- Comment & Engage Section -->
            <div class="h-[800px] flex flex-col gap-2 overflow-y-scroll p-2 rounded-xl" style="background-color: #18181b;">
                <div class="flex flex-col h-full justify-between">
                    <!-- Account to Comment with Section -->
                    <div class="bg-black flex items-center gap-4 p-2 rounded-lg shadow-md">
                        <div class="flex items-center gap-2 text-white">
                            <i class="fa-solid fa-comments text-xl"></i>
                            <p class="mb-0 fw-semibold">Commentez en Tant que : </p>
                        </div>
                        <div class="relative text-white">
                            <button 
                                @click="linkedinAccountDropdown = !linkedinAccountDropdown"
                                class="flex items-center gap-2 p-1 border rounded-full"
                            >
                                <img 
                                    :src="selectedAccount.linkedin_picture ?? '/build/assets/images/default-profile.png'"
                                    alt="Profile Picture"
                                    height="40"
                                    width="40" 
                                    class="rounded-full"
                                />
                                <div class="flex">
                                    <span>{{ selectedAccount.linkedin_firstname }} {{ selectedAccount.linkedin_lastname }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#FFFFFF">
                                        <path d="M480-360 280-560h400L480-360Z"/>
                                    </svg>
                                </div>
                            </button>
                            <ul 
                                v-if="linkedinAccountDropdown"
                                class="absolute right-0 bg-black rounded-md px-0 z-50">
                                <li 
                                    v-for="linkedinAccount in userLinkedinAccounts"
                                    class="flex items-center gap-2 px-3 py-2 hover:bg-gray-500 rounded-md cursor-pointer"
                                    @click="selectLinkedinAccount(linkedinAccount)"
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

                    <!-- All Post Comment Section -->
                    <div class="flex-1 mt-4 px-2">
                        <div v-if="isLoading" class="flex items-center justify-center h-full text-white">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                        <div v-else-if="commentsError" class="flex flex-col items-center justify-center gap-2 h-full text-red-500">
                            <i class="fa-solid fa-ban text-3xl"></i>
                            <p class="mb-0">{{ commentsError }}</p>
                        </div>
                        <div v-else-if="postComments.length === 0" class="flex items-center justify-center h-full text-white">
                            Pas de commentaires pour ce post.
                        </div>
                        <div v-else>
                            <div v-for="(commentData, index) in postComments" :key="index" class="mb-4">
                                <!-- Top-level Comment -->
                                <div class="flex gap-2">
                                    <img 
                                        :src="commentData.commenter?.profilePicture || '/build/assets/images/default-profile.png'" 
                                        alt="Profile picture"
                                        height="50"
                                        width="50"
                                        class="rounded-full"
                                    />
                                    <div>
                                        <div class="bg-black text-white flex flex-col gap-1 p-2 rounded-lg">
                                            <h5 class="mb-0 text-sm fw-semibold">
                                                {{ commentData.commenter?.firstName }} {{ commentData.commenter?.lastName }}
                                            </h5>
                                            <p class="mb-0 text-sm">
                                                {{ commentData.comment.message.text }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2 mt-2 px-2 text-white text-xs">
                                            <p 
                                                class="mb-0 cursor-pointer hover:text-green-500"
                                                @click="selectCommentToReplyTo(commentData)"
                                            >
                                                Répondre
                                            </p>
                                            <p class="mb-0">|</p>
                                            <p 
                                                v-if="isCommentEditable(commentData)"
                                                class="mb-0 cursor-pointer hover:text-blue-500"
                                                @click="startEditing(commentData)"
                                            >
                                                Modifier
                                            </p>
                                            <p class="mb-0">|</p>
                                            <p 
                                                @click="deleteComment(commentData, commentData.post)"
                                                class="mb-0 cursor-pointer hover:text-red-500"
                                            >
                                                Supprimer
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Nested Comments -->
                                <div v-if="commentData.nestedComments && commentData.nestedComments.length > 0" class="ml-8 mt-2">
                                    <div v-for="(nestedCommentData, nestedIndex) in commentData.nestedComments" :key="nestedIndex" class="flex gap-2 mb-2">
                                        <img 
                                            :src="nestedCommentData.nestedCommenter?.profilePicture || '/build/assets/images/default-profile.png'" 
                                            alt="Profile picture"
                                            height="40"
                                            width="40"
                                            class="rounded-full"
                                        />
                                        <div>
                                            <div class="bg-gray-800 text-white flex flex-col gap-1 p-2 rounded-lg">
                                                <h5 class="mb-0 text-sm fw-semibold">
                                                    {{ nestedCommentData.nestedCommenter?.firstName }} {{ nestedCommentData.nestedCommenter?.lastName }}
                                                </h5>
                                                <p class="mb-0 text-sm">
                                                    {{ nestedCommentData.nestedComment.message.text }}
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-2 mt-2 px-2 text-white text-xs">
                                                <p class="mb-0 cursor-pointer hover:text-green-500">Répondre</p>
                                                <p class="mb-0">|</p>
                                                <p class="mb-0 cursor-pointer hover:text-red-500">Supprimer</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comment Input Section -->
                    <div class="flex items-center justify-between gap-2 bg-black rounded-lg p-2 shadow-md">
                        <input 
                            type="text" 
                            id="comment-input" 
                            name="comment-input" 
                            v-model="newComment"
                            placeholder="Ecrire votre commentaire..." 
                            class="bg-black border w-full py-2 px-3 rounded-lg text-white"
                        />
                        <div v-if="rateLimitTimeout" class="bg-transparent py-2 px-3 rounded-lg border text-center text-white w-[100px]">
                            {{ countdown }}
                        </div>
                        <div v-else class="flex items-center gap-1">
                            <div v-if="isCreatingLoading" class="bg-transparent px-3 py-2 border rounded-lg text-center w-[100px]">
                                <progress-spinner 
                                    style="width: 15px; height: 15px" strokeWidth="10" fill="transparent" 
                                    animationDuration=".5s" 
                                    aria-label="Custom ProgressSpinner" 
                                />
                            </div>
                            <div v-else class="flex items-center gap-1">
                                <!-- AI Assistant -->
                                <button 
                                    @click="openAICommentPortal"
                                    class="bg-red-500 py-2 px-3 rounded-lg text-center"
                                >
                                    <i class="fa-solid fa-robot text-white"></i>
                                </button>
                                <button 
                                    @click="editingComment ? editComment(editingComment, selectedPost, newComment) : createComment()"
                                    class="bg-blue-500 py-2 px-3 rounded-lg text-center"
                                    :disabled="isLoading || !newComment || rateLimitTimeout"
                                >
                                    <i class="fa-solid fa-paper-plane text-white"></i>
                                    {{ editingComment ? 'Mettre à jour' : '' }}
                                </button>
                                <button 
                                    v-if="editingComment"
                                    @click="cancelEditing"
                                    class="bg-gray-500 py-2 px-3 rounded-lg text-center"
                                >
                                    Annuler
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from 'sweetalert2';
import { useToast } from "vue-toastification";
import { getProfilePicture, getLinkedinUserByID, getUsername, formatDate, formatDateForEngagement } from '../services/datatables';
import axios from 'axios';

export default {
    name: 'CommHub',

    props: {
        userLinkedinAccounts: {
            type: Array,
            required: true,
        },
        allLinkedinPosts: {
            type: Array,
            required: true,
        },
    },

    emits: ['open-ai-comment-portal'],

    setup() {
        const toast = useToast();
        return { toast };
    },

    data() {
        return {
            selectedPost: this.allLinkedinPosts.filter(post => post.status === 'posted')[0] ?? null,
            linkedinAccountDropdown: false,
            selectedAccount: this.userLinkedinAccounts[0],
            commentToReplyTo: null,
            isLoading: false,
            postComments: [],
            newComment: '',
            commentToEdit: '',
            editingComment: null,
            commentsError: null,
            rateLimitTimeout: false,
            isCreatingLoading: false,
            countdown: 0,
            countdownInterval: null,
        };
    },

    computed: {
        publishedPosts() {
            return this.allLinkedinPosts.filter(post => post.status === 'posted');
        },
    },

    mounted() {
        if (this.selectedPost) {
            this.getCommentsOnPost(this.selectedPost);
        }
    },

    beforeUnmount() {
        if (this.countdownInterval) {
            clearInterval(this.countdownInterval);
        }
    },

    methods: {
        getProfilePicture,
        getLinkedinUserByID,
        getUsername,
        formatDate,
        formatDateForEngagement,

        parseContent(content, type) {
            try {
                return JSON.parse(content);
            } catch (error) {
                console.error('Failed to parse post content:', error);
                switch (type) {
                    case 'text':
                        return { text: 'Contenu Invalide' };
                    case 'image':
                        return { file_path: null, caption: 'Image Invalide' };
                    case 'video':
                        return { file_path: null, caption: 'Vidéo Invalide' };
                    case 'multiimage':
                        return { file_paths: [], caption: 'Images Invalides' };
                    default:
                        return { text: 'Contenu Invalide' };
                }
            }
        },

        changeSelectedPost(post) {
            this.selectedPost = post;
            this.postComments = [];
            this.getCommentsOnPost(post);
            this.commentsError = null;
            this.cancelEditing();
        },

        selectLinkedinAccount(account) {
            this.selectedAccount = account;
            this.linkedinAccountDropdown = false;
        },

        selectCommentToReplyTo(commentData) {
            this.commentToReplyTo = commentData.comment;
            const commenterName = `${commentData.commenter.firstName} ${commentData.commenter.lastName}`;
            this.newComment = `@${commenterName} `;
        },

        openAICommentPortal() {
            this.$emit('open-ai-comment-portal');
        },


        async getCommentsOnPost(post) {
            try {
                this.isLoading = true;
                this.postComments = [];
                const linkedinAccount = this.getLinkedinUserByID(this.userLinkedinAccounts, post.linkedin_user_id);

                if (!linkedinAccount) {
                    this.showErrorToast("Compte LinkedIn non trouvé !");
                    return;
                }

                const commentsResponse = await axios.get('/post/comments', {
                    params: {
                        linkedin_id: post.linkedin_user_id,
                        linkedin_token: linkedinAccount.linkedin_token,
                        post_urn: encodeURIComponent(post.post_urn),
                    },
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (commentsResponse.status === 200 && commentsResponse.data.data.elements) {
                    for (const comment of commentsResponse.data.data.elements) {
                        const commenter = await this.getCommenterByURN(comment.actor, linkedinAccount);
                        const nestedComments = await this.getCommentsOnComment(comment.id, comment.object, linkedinAccount, post);

                        this.postComments.push({ comment, commenter, nestedComments, post });
                    }
                } else {
                    console.error("An error occurred: ", commentsResponse);
                    this.showErrorToast('Une erreur s\'est produite lors de la récupération des commentaires !');
                }
            } catch (error) {
                console.error('Error while fetching comments: ', error);
                this.showErrorToast("Une erreur s'est produite lors de la récupération des commentaires");
                if (error.response?.data?.status === 401) {
                    this.commentsError = "Non autorisé ! Vérifiez votre compte Linkedin tout d'abord !";
                    this.showErrorToast('Vous n\'êtes pas autorisé pour accéder aux commentaires de ce compte');
                } else if (error.response?.data?.status === 403) {
                    this.commentsError = "Erreur interne du Linkedin API ! Réessayer plus tard";
                    this.showErrorToast('Erreur interne du Linkedin API ! Réessayer plus tard');
                }
            } finally {
                this.isLoading = false;
            }
        },

        async getCommenterByURN(personUrn, linkedinUser) {
            try {
                const urn = personUrn.split(":")[3];
                const url = `https://api.linkedin.com/v2/people/(id:${urn})?projection=(id,localizedFirstName,localizedLastName,profilePicture(displayImage~:playableStreams))`;
                const response = await axios.get(url, {
                    headers: {
                        "Content-Type": "application/json",
                        "X-Restli-Protocol-Version": "2.0.0",
                        "Authorization": `Bearer ${linkedinUser.linkedin_token}`,
                        "LinkedIn-Version": "202501"
                    },
                });

                if (response.status === 200) {
                    const data = response.data;
                    return {
                        firstName: data.localizedFirstName,
                        lastName: data.localizedLastName,
                        profilePicture: data.profilePicture?.["displayImage~"]?.elements[0]?.identifiers[0]?.identifier || null,
                    };
                } else {
                    console.error("Failed to fetch commenter profile:", response);
                    this.showErrorToast("Échec de la récupération du compte !");
                    return null;
                }
            } catch (error) {
                console.error("Error while fetching commenter profile data: ", error);
                this.showErrorToast("Échec de la récupération des données du compte !");
                return null;
            }
        },

        async getCommentsOnComment(commentUrn, activityUrn, linkedinUser, post) {
            try {
                const response = await axios.get('/posts/comments/nested-comments', {
                    params: {
                        linkedin_id: post.linkedin_user_id,
                        linkedin_token: linkedinUser.linkedin_token,
                        activity_urn: activityUrn,
                        comment_urn: commentUrn,
                    },
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.status === 200 && response.data.data.elements) {
                    const nestedComments = [];
                    for (const nestedComment of response.data.data.elements) {
                        const nestedCommenter = await this.getCommenterByURN(nestedComment.actor, linkedinUser);
                        nestedComments.push({
                            nestedComment: nestedComment,
                            nestedCommenter: nestedCommenter
                        });
                    }
                    return nestedComments;
                } else {
                    console.error("An error occurred: ", response);
                    this.showErrorToast('Une erreur s\'est produite lors de la récupération des commentaires !');
                    return [];
                }
            } catch (error) {
                console.error('Error while fetching nested comments: ', error);
                this.showErrorToast("Une erreur s'est produite lors de la récupération des commentaires");
                if (error.response?.data?.status === 401) {
                    this.commentsError = "Non autorisé ! Vérifiez votre compte Linkedin tout d'abord !";
                    this.showErrorToast('Vous n\'êtes pas autorisé pour accéder aux commentaires de ce compte');
                } else if (error.response?.data?.status === 403) {
                    this.commentsError = "Erreur interne du Linkedin API ! Réessayer plus tard";
                    this.showErrorToast('Erreur interne du Linkedin API ! Réessayer plus tard');
                }
                return [];
            }
        },

        async createComment() {
            if (!this.newComment.trim()) {
                this.showErrorToast("Le commentaire ne peut pas être vide");
                return;
            } else if (this.newComment.trim().length >= 3000) {
                this.showErrorToast("Le commentaire ne doit pas dépasser 3000 caractères");
                return;
            }

            try {
                this.isCreatingLoading = true;
                const linkedinAccount = this.selectedAccount;
                let parentCommentUrn = null;

                if (this.commentToReplyTo) {
                    parentCommentUrn = this.commentToReplyTo.parentComment;
                }

                const response = await axios.post('/post/comment/create', {
                    linkedin_id: linkedinAccount.linkedin_id,
                    linkedin_token: linkedinAccount.linkedin_token,
                    content: this.newComment,
                    post_urn: encodeURIComponent(this.selectedPost.post_urn),
                    parent_comment_urn: parentCommentUrn
                }, 
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.status === 201) {
                    this.newComment = '';
                    this.getCommentsOnPost(this.selectedPost);
                    this.toast.success("Commentaire publié avec succès !");
                    this.rateLimitTimeout = true;
                    this.startCountdown();
                }
            } catch (error) {
                console.error("Error posting comment:", error);
                this.showErrorToast("Échec de la publication du commentaire");
            } finally {
                this.isCreatingLoading = false;
            }
        },

        async deleteComment(commentData, post) {
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
                    this.isLoading = true;
                    const linkedinUser = this.getLinkedinUserByID(this.userLinkedinAccounts, post.linkedin_user_id);

                    const deletionResponse = await axios.delete('/post/comment/delete', {
                        data: {
                            linkedin_token: linkedinUser.linkedin_token,
                            comment_urn: commentData.id,
                            post_urn: post.post_urn,
                            person_urn: commentData.actor
                        },
                        headers: {
                            "Authorization": `Bearer ${linkedinUser.linkedin_token}`,
                            "Linkedin-Version": "202501",
                            "X-Restli-Protocol-Version": "2.0.0",
                        }
                    });

                    if (deletionResponse.status === 200) {
                        await Swal.fire({
                            title: "Supprimé !",
                            text: "Votre commentaire a été supprimé.",
                            icon: "success"
                        });
                        this.getCommentsOnPost(this.selectedPost);
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Une erreur s'est produite lors de la suppression!",
                        });
                        console.error(deletionResponse);
                    }
                } catch (error) {
                    console.error("Error deleting comment:", error);
                    await Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Une erreur s'est produite lors de la suppression du commentaire !",
                    });
                } finally {
                    this.isLoading = false;
                }
            }
        },

        async editComment(comment, post, updatedContent) {
            if (!updatedContent.trim()) {
                this.showErrorToast('Le commentaire ne peut pas être vide');
                return;
            } else if (updatedContent.trim().length >= 3000) {
                this.showErrorToast('Le commentaire ne doit pas dépasser 3000 caractères');
                return;
            }

            try {
                this.isLoading = true;
                const linkedinUser = this.getLinkedinUserByID(this.userLinkedinAccounts, post.linkedin_user_id);
                
                const updateResponse = await axios.post('/post/comment/update', {
                    linkedin_id: linkedinUser.linkedin_id,
                    linkedin_token: linkedinUser.linkedin_token,
                    content: updatedContent,
                    comment_urn: comment.id,
                    person_urn: comment.actor,
                    post_urn: post.post_urn,
                }, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                });

                if (updateResponse.status === 201) {
                    this.toast.success("Votre commentaire a été modifié avec succès");
                    this.getCommentsOnPost(post);
                    this.editingComment = null; // Reset editing state
                    this.newComment = ''; // Clear input
                }
            } catch (error) {
                console.error("Error updating comment:", error);
                this.showErrorToast("Échec de la modification du commentaire");
            } finally {
                this.isLoading = false;
            }
        },

        isCommentEditable(commentData) {
            const userUrn = `urn:li:person:${this.selectedAccount.linkedin_id}`;
            return commentData.comment.actor === userUrn;
        },

        startEditing(commentData) {
            this.editingComment = commentData.comment;
            this.newComment = commentData.comment.message.text;
        },

        cancelEditing() {
            this.editingComment = null;
            this.newComment = '';
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

        startCountdown() {
            this.countdown = 60;
            if (this.countdownInterval) {
                clearInterval(this.countdownInterval);
            }
            this.countdownInterval = setInterval(() => {
                if (this.countdown > 0) {
                    this.countdown--;
                } else {
                    this.rateLimitTimeout = false;
                    clearInterval(this.countdownInterval);
                    this.countdownInterval = null;
                }
            }, 1000);
        },
    },
};
</script>

<style scoped>
.image-border {
    border-top-left-radius: 5px;
    border-bottom-left-radius: 5px;
}

.selected-post {
    background-color: rgb(59, 58, 58);
    cursor: pointer;
}
</style>