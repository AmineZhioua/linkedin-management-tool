<template>
    <div class="bg-black bg-opacity-50 inset-0 h-full w-full absolute"></div>
    <div class="flex items-center w-full p-4 justify-center gap-2 absolute top-[50%] left-[50%] translate-y-[-50%] translate-x-[-50%]">
        <!-- Post Fields -->
        <div class="bg-white p-4 rounded-lg relative"> 
            <!-- Close Button & Title -->
            <div class="flex w-full justify-between mb-2">
                <h3 class="text-xl mb-0">{{ selectedPost ? 'Modifier le Post' : 'Créer un Post' }}</h3>
                <button @click="handleClose">
                    <img src="/build/assets/icons/close.svg" alt="Close Icon" height="20" width="20" />
                </button>
            </div>

            <div class="flex flex-col justify-center gap-4">
                <!-- Heading -->
                <div class="flex items-center justify-between">
                    <!-- Select Account to Post With -->
                    <div v-if="!selectedPost" class="relative">
                        <button 
                            @click="linkedinAccountDropdown = !linkedinAccountDropdown"
                            class="flex items-center gap-2 p-1 border rounded-full"
                        >
                            <img 
                                :src="selectedAccount.linkedin_picture" 
                                alt="Profile Picture"
                                height="40"
                                width="40" 
                                class="rounded-full"
                            />
                            <div class="flex">
                                <span>{{ selectedAccount.linkedin_firstname }} {{ selectedAccount.linkedin_lastname }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
                                    <path d="M480-360 280-560h400L480-360Z"/>
                                </svg>
                            </div>
                        </button>
                        <ul 
                            v-if="linkedinAccountDropdown"
                            class="absolute right-0 bg-white border rounded-md px-0 z-50">
                            <li 
                                v-for="linkedinAccount in allLinkedinAccounts"
                                class="flex items-center gap-2 px-3 py-2 hover:bg-gray-200 rounded-md cursor-pointer"
                                @click="selectLinkedinAccount(linkedinAccount)"
                            >
                                <img 
                                    :src="linkedinAccount.linkedin_picture" 
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
                    <!-- Select Post Type -->
                    <select 
                        name="post-type" 
                        id="post-type"
                        class="py-2 px-3 min-w-[100px] rounded-lg"
                        v-model="newPost.type"
                        v-if="!selectedPost"
                    >
                        <option 
                            v-for="type in postTypes"
                            :key="type.value"
                            :value="type.value"
                        >
                            {{ type.label }}
                        </option>
                    </select>
                </div>

                <!-- LinkedIn Icon & Textarea -->
                <div class="w-full flex gap-2 relative">
                    <div 
                        class="p-2 rounded-full h-fit" 
                        style="background-color: rgb(23 92 179); border: 3px solid white;"
                    >
                        <img 
                            src="/build/assets/icons/linkedin.svg" 
                            alt="Linkedin Icon" 
                            height="15"
                            width="15"
                        />
                    </div>

                    <div class="flex flex-col flex-1 gap-3">
                        <!-- For Type of Text Posts -->
                        <textarea 
                            v-if="newPost.type === 'text'"
                            v-model="newPost.content.text"
                            name="caption" 
                            id="caption" 
                            class="border rounded-md w-full p-2 min-h-[300px]" 
                            placeholder="Ecrire quelque chose ou utiliser votre Assistant IA"
                        ></textarea>

                        <!-- File Input for Image & Video Posts -->
                        <div 
                            class="w-full"
                            v-if="newPost.type === 'image' || newPost.type === 'video'"
                        >
                            <div v-if="newPost.content.file_path">
                                <p>Current file:</p>
                                <img v-if="newPost.type === 'image'" :src="getMediaUrl(newPost.content.file_path)" alt="Current Image" style="max-width: 100px;" />
                                <video v-if="newPost.type === 'video'" controls style="max-width: 100px;">
                                    <source :src="getMediaUrl(newPost.content.file_path)" type="video/mp4">
                                </video>
                            </div>
                            <input 
                                type="file" 
                                @change="handleFileUpload"
                                class="border w-full rounded-md" 
                                :accept="newPost.type === 'image' ? 'image/*' : 'video/*'"
                            />
                        </div>
                        
                        <!-- For Type of Article Posts -->
                        <div class="flex flex-col gap-2" v-if="newPost.type === 'article'">
                            <div>
                                <label class="text-sm text-black mb-2">URL de l'Article <span style="color: red;">*</span> :</label>
                                <input
                                    type="text"
                                    v-model="newPost.content.url"
                                    class="w-full border rounded-lg p-2"
                                    placeholder="e.g: www.example.com"
                                />
                            </div>

                            <div>
                                <label class="text-sm text-black mb-2">Titre de l'Article <span style="color: red;">*</span> :</label>
                                <input
                                    type="text"
                                    v-model="newPost.content.title"
                                    class="w-full border rounded-lg p-2"
                                    placeholder="Official LinkedIn Blog"
                                />
                            </div>

                            <div>
                                <label class="text-sm text-black mb-2">Description de l'Article :</label>
                                <input
                                    type="text"
                                    v-model="newPost.content.description"
                                    class="w-full border rounded-lg p-2"
                                    placeholder="Official LinkedIn Blog - Your source for insights and information about LinkedIn."
                                />
                            </div>
                        </div>

                        <textarea 
                            v-if="newPost.type !== 'text'"
                            v-model="newPost.content.caption"
                            name="caption" 
                            id="caption" 
                            class="border rounded-md w-full p-2 min-h-[300px]" 
                            placeholder="Ecrire quelque chose ou utiliser votre Assistant IA"
                        ></textarea>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex flex-row-reverse items-center justify-between gap-4">
                    <div class="flex flex-row-reverse items-center gap-2">
                        <button 
                            @click="submitSinglePost"
                            class="bg-blue-600 text-white py-2 px-3 rounded-md text-md fw-semibold"
                        >
                            Planifier
                        </button>

                        <button class="bg-transparent text-black border-gray-600 border py-2 px-3 rounded-md text-md fw-semibold">
                            Ajouter au Brouillons
                        </button>
                    </div>

                    <!-- DateTime Input -->
                    <div class="flex flex-col">
                        <input 
                            type="datetime-local" 
                            v-model="newPost.scheduledDateTime"
                            class="border" 
                            :min="currentDateTime"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Post Preview Section -->
        <div class="bg-white p-3 rounded-lg min-w-[400px] max-w-[450px] min-h-[400px]">
            <div class="flex items-center">
                <h3 class="text-black text-lg">Aperçu LinkedIn</h3>
            </div>

            <div class="border rounded-md mt-4">
                <!-- Heading of the LinkedIn Post Preview Card -->
                <div class="flex items-center gap-2 p-2">
                    <img 
                        v-if="selectedAccount.linkedin_picture"
                        :src="selectedAccount.linkedin_picture" 
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
                        <h4 v-if="selectedAccount" class="fw-bold mb-0" style="font-size: 15px;">
                            {{ selectedAccount.linkedin_firstname }} {{ selectedAccount.linkedin_lastname }}
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

                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" class="cursor-pointer" fill="#585a5d">
                        <path d="M240-400q-33 0-56.5-23.5T160-480q0-33 23.5-56.5T240-560q33 0 56.5 23.5T320-480q0 33-23.5 56.5T240-400Zm240 0q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Zm240 0q-33 0-56.5-23.5T640-480q0-33 23.5-56.5T720-560q33 0 56.5 23.5T800-480q0 33-23.5 56.5T720-400Z"/>
                    </svg>
                </div>

                <!-- Preview Content -->
                <!-- Text Type Preview -->
                <div v-if="newPost.type === 'text'" class="px-2 my-2">
                    <p class="mb-0 text-black">{{ newPost.content.text }}</p>
                </div>

                <!-- Image Type Preview -->
                <div v-if="newPost.type === 'image'" class="w-full h-auto">
                    <p class="my-2 px-2">{{ newPost.content.caption }}</p>
                    <img 
                        v-if="imagePreviewUrl || newPost.content.file_path"
                        :src="imagePreviewUrl || getMediaUrl(newPost.content.file_path)" 
                        class="object-fill w-full" 
                        alt="Image Preview"
                    />
                </div>

                <!-- Video Type Preview -->
                <div v-if="newPost.type === 'video'" class="w-full h-auto">
                    <p class="my-2 px-2">{{ newPost.content.caption || 'Votre légende ici...' }}</p>
                    <video 
                        v-if="videoPreviewUrl || newPost.content.file_path" 
                        controls 
                        class="w-full"
                    >
                        <source :src="videoPreviewUrl || getMediaUrl(newPost.content.file_path)" type="video/mp4">
                        Votre navigateur ne supporte pas la lecture de vidéos.
                    </video>
                </div>

                <!-- Article Type Preview -->
                <div v-if="newPost.type === 'article'" class="w-full h-auto">
                    <p class="my-2 px-2">{{ newPost.content.caption || 'Votre légende ici...' }}</p>
                    <div class="border p-2 rounded-md">
                        <a :href="newPost.content.url || '#'" target="_blank" class="text-blue-600 hover:underline">
                            {{ newPost.content.title || 'Titre de l\'article' }}
                        </a>
                        <p class="text-gray-500">{{ newPost.content.description || 'Description de l\'article' }}</p>
                    </div>
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
        </div>

        <!-- Exit Popup -->
        <div 
            v-if="showConfirmExit"
            class="absolute w-full h-full inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" 
            @click.self="showConfirmExit = false"
        > 
            <div class="absolute bg-white p-4 rounded-lg">
                <h3 class="text-xl fw-bold mb-4">Annuler les Modifications ?</h3>
                <p style="font-size: 15px;">Vous perdrez définitivement toutes les modifications que vous avez apportées</p>
                <div class="flex justify-end gap-2 mt-4">
                    <button @click="showConfirmExit = false" class="bg-gray-300 text-black px-4 py-2 rounded text-sm">Continuer à éditer</button>
                    <button @click="confirmExit" class="bg-red-500 text-white px-4 py-2 rounded text-sm">Ignorer les modifications</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { useToast } from "vue-toastification";

export default {
    name: 'PostPortal',
    props: {
        allLinkedinAccounts: {
            type: Array,
            required: true,
        },
        selectedPost: {
            type: Object,
            default: null,
        },
    },

    emits: ['close'],

    setup() {
        const toast = useToast();
        return { toast };
    },

    data() {
        const today = new Date();
        return {
            selectedAccount: this.allLinkedinAccounts[0],
            linkedinAccountDropdown: false,
            currentDateTime: this.formatDateTime(today),
            imagePreviewUrl: null,
            videoPreviewUrl: null,
            postTypes: [
                { value: "text", label: "Text", icon: "fas fa-align-left" },
                { value: "image", label: "Image", icon: "fas fa-image" },
                { value: "video", label: "Video", icon: "fas fa-video" },
                { value: "article", label: "Article", icon: "fas fa-file-alt" },
            ],
            newPost: {
                scheduledDateTime: this.formatDateTime(today),
                type: "text",
                content: {
                    text: "",
                    file: null,
                    file_path: "",
                    caption: "",
                    url: "",
                    title: "",
                    description: "",
                },
            },
            showConfirmExit: false,
        };
    },

    watch: {
        selectedPost: {
            handler(newPost) {
                if (newPost) {
                    const parsedContent = JSON.parse(newPost.content || '{}');
                    this.newPost = {
                        scheduledDateTime: newPost.scheduled_date,
                        type: newPost.type,
                        content: {
                            text: parsedContent.text || '',
                            file: null,
                            file_path: parsedContent.file_path || '',
                            caption: parsedContent.caption || '',
                            url: parsedContent.url || '',
                            title: parsedContent.title || '',
                            description: parsedContent.description || '',
                        },
                    };
                    this.selectedAccount = this.allLinkedinAccounts.find(account => account.id === newPost.linkedin_user_id);
                    if (newPost.type === 'image') {
                        this.imagePreviewUrl = this.getMediaUrl(parsedContent.file_path);
                    } else if (newPost.type === 'video') {
                        this.videoPreviewUrl = this.getMediaUrl(parsedContent.file_path);
                    }
                } else {
                    const today = new Date();
                    this.newPost = {
                        scheduledDateTime: this.formatDateTime(today),
                        type: "text",
                        content: {
                            text: "",
                            file: null,
                            file_path: "",
                            caption: "",
                            url: "",
                            title: "",
                            description: "",
                        },
                    };
                    this.imagePreviewUrl = null;
                    this.videoPreviewUrl = null;
                    this.selectedAccount = this.allLinkedinAccounts[0];
                }
            },
            immediate: true,
        },
    },

    methods: {
        selectLinkedinAccount(account) {
            this.selectedAccount = account;
            this.linkedinAccountDropdown = false;
        },

        formatDateTime(date) {
            const pad = (num) => String(num).padStart(2, '0');
            const year = date.getFullYear();
            const month = pad(date.getMonth() + 1);
            const day = pad(date.getDate());
            const hours = pad(date.getHours());
            const minutes = pad(date.getMinutes());
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        },

        getMediaUrl(filePath) {
            return filePath ? `/linkedin/${filePath}` : '';
        },

        handleFileUpload(event) {
            const file = event.target.files && event.target.files[0];
            if (file) {
                this.newPost.content.file = file;
                this.newPost.content.file_path = '';
                if (this.newPost.type === 'image') {
                    this.imagePreviewUrl = URL.createObjectURL(file);
                } else if (this.newPost.type === 'video') {
                    this.videoPreviewUrl = URL.createObjectURL(file);
                }
            } else {
                this.newPost.content.file = null;
                this.imagePreviewUrl = this.newPost.content.file_path ? this.getMediaUrl(this.newPost.content.file_path) : null;
                this.videoPreviewUrl = this.newPost.content.file_path ? this.getMediaUrl(this.newPost.content.file_path) : null;
            }
        },

        async submitSinglePost() {
            try {
                if (!this.isTokenValid()) {
                    return;
                }

                const postData = new FormData();
                postData.append("linkedin_id", this.selectedAccount.id);
                postData.append("type", this.newPost.type);
                postData.append("scheduled_date", this.newPost.scheduledDateTime);

                if (this.selectedPost) {
                    postData.append("post_id", this.selectedPost.id);
                }

                switch (this.newPost.type) {
                    case "text":
                        postData.append("content[text]", this.newPost.content.text.trim());
                        break;
                    case "image":
                    case "video":
                        if (this.newPost.content.file) {
                            postData.append("content[file]", this.newPost.content.file);
                            postData.append("content[original_filename]", this.newPost.content.file.name);
                        } else if (this.newPost.content.file_path) {
                            postData.append("content[file_path]", this.newPost.content.file_path);
                        }
                        postData.append("content[caption]", this.newPost.content.caption.trim());
                        break;
                    case "article":
                        postData.append("content[url]", this.newPost.content.url);
                        postData.append("content[title]", this.newPost.content.title);
                        postData.append("content[description]", this.newPost.content.description);
                        postData.append("content[caption]", this.newPost.content.caption.trim());
                        break;
                    default:
                        throw new Error("Type de publication invalide");
                }

                const postResponse = await axios.post("/linkedin/schedule-single-post", postData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    },
                });

                if (postResponse.status === 201 || postResponse.status === 200) {
                    this.toast.success("Post planifié avec succès !", {
                        position: "bottom-right",
                        timeout: 2000,
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

                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            } catch (error) {
                console.error("Error submitting post:", error);
                if (error.response?.status === 401 || 
                    (error.response?.data?.error && error.response?.data?.error.toLowerCase().includes("token")) ||
                    (error.message && error.message.toLowerCase().includes("token"))) {
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
                } else if (error.response?.data?.error) {
                    console.error(error.response.data.error);
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
                } else if (error.response?.data?.message) {
                    console.error(error.response.data.message);
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
                } else if (error.message) {
                    console.error(error.message);
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
                } else {
                    console.error("Une erreur s'est produite lors de la publication des posts");
                    this.toast.error(`Une erreur s'est produite lors de la publication des posts`, {
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
                }
            }
        },

        isTokenValid() {
            const tokenExpirationDate = new Date(this.selectedAccount.expire_at);
            const now = new Date();
            return tokenExpirationDate > now;
        },

        handleClose() {
            this.showConfirmExit = true;
        },

        confirmExit() {
            this.$emit('close');
            this.showConfirmExit = false;
        },
    }
};
</script>