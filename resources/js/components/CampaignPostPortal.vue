<template>
    <div class="bg-black bg-opacity-50 inset-0 h-full w-full absolute"></div>
    <div class="flex items-center w-full h-[600px] p-4 justify-center gap-2 absolute top-[50%] left-[50%] translate-y-[-50%] translate-x-[-50%]">
        <!-- AI Assistant Section -->
        <div class="flex flex-col justify-between gap-4 h-full p-4 bg-white rounded-lg min-w-[450px]">
            <!-- Heading -->
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-robot text-2xl"></i>
                <h3 class="mb-0 mt-1 fw-semibold text-xl">Assistant AI</h3>
            </div>

            <!-- Textarea Input -->
            <div class="flex flex-col gap-2">
                <p class="mb-2 ml-1 fw-semibold text-black text-md">Quel est le sujet que vous souhaitez écrire ?</p>
                <textarea 
                    name="ai-input" 
                    id="ai-input" 
                    placeholder="Eg: Faites la promotion de ma nouvelle formation en marketing digitale pour attirer de nouvelles inscriptions. Les inscriptions se terminent dans 5 jours."
                    class="p-2 bg-white border rounded-md w-full min-h-[300px]"
                    v-model="prompt"
                ></textarea>
            </div>

            <div class="w-full flex justify-end">
                <div 
                    v-if="isGenerating" 
                    class="py-2 px-3 w-[100px] flex items-center justify-center rounded-md text-white" 
                    style="border: 2px solid pink;"
                >
                    <progress-spinner 
                        style="width: 15px; height: 15px" strokeWidth="10" fill="transparent" 
                        animationDuration=".5s" 
                        aria-label="Custom ProgressSpinner" 
                    />
                </div>
                <button 
                    v-else
                    class="flex items-center gap-2 py-2 px-3 rounded-md border w-fit text-white generate-btn"
                    @click="generateAiContent"
                >
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <p class="mb-0 fw-semibold">Générer</p>
                </button>
            </div>
        </div>


        <!-- Post Fields -->
        <div class="bg-white flex flex-col justify-between p-4 rounded-lg relative h-full min-w-[500px] overflow-y-scroll"> 
            <!-- Close Button & Title -->
            <div class="flex w-full justify-between mb-2 pt-2">
                <h3 class="text-xl mb-0">{{ postToEdit ? 'Modifier le Post' : 'Créer un Post' }}</h3>
                <button @click="handleClose">
                    <img src="/build/assets/icons/close.svg" alt="Close Icon" height="20" width="20" />
                </button>
            </div>

            <div class="flex flex-col justify-center gap-4">
                <!-- Heading -->
                <div class="flex items-center justify-end">
                    <!-- Select Post Type -->
                    <select 
                        name="post-type" 
                        id="post-type"
                        class="py-2 px-3 min-w-[100px] rounded-lg bg-gray-200"
                        v-model="postToEdit.type"
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

                <!-- Error Messages -->
                <p 
                    v-if="campaignPostError" 
                    class="bg-red-200 text-red-500 py-2 px-3 rounded-lg mb-0"
                >
                    <i class="fa-solid fa-circle-exclamation mr-2 text-xl" style="color: red;"></i>
                    <span class="text-md">{{ campaignPostError }}</span>
                </p>

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
                            v-if="postToEdit.type === 'text'"
                            v-model="postToEdit.content.caption"
                            name="caption" 
                            id="caption" 
                            class="border rounded-md w-full p-2 min-h-[300px] bg-white" 
                            placeholder="Ecrire quelque chose ou utiliser votre Assistant IA"
                        ></textarea>

                        <!-- File Input for Image & Video Posts -->
                        <div 
                            class="w-full"
                            v-if="postToEdit.type === 'image' || postToEdit.type === 'video'"
                        >
                            <div v-if="postToEdit.content.file_path">
                                <p>Current file:</p>
                                <img v-if="postToEdit.type === 'image'" :src="getMediaUrl(postToEdit.content.file_path)" alt="Current Image" style="max-width: 100px;" />
                                <video v-if="postToEdit.type === 'video'" controls style="max-width: 100px; z-index: 10;">
                                    <source :src="getMediaUrl(postToEdit.content.file_path)" type="video/mp4">
                                </video>
                            </div>
                            <input 
                                type="file" 
                                @change="handleFileUpload"
                                class="border w-full rounded-md" 
                                :accept="postToEdit.type === 'image' ? 'image/*' : 'video/*'"
                            />
                            <p v-if="postToEdit.content.file" class="mt-2 mb-0 text-sm text-gray-600">
                                Fichier sélectionné : {{ postToEdit.content.fileName || (postToEdit.content.file && postToEdit.content.file.name) }}
                            </p>
                        </div>

                        <!-- Multi File Input for Multi Images Posts -->
                        <div 
                            class="w-full"
                            v-if="postToEdit.type === 'multiimage'"
                        >
                            <div class="max-h-[300px] max-w-full overflow-scroll">
                                <FileUpload 
                                    name="demo[]" 
                                    :multiple="true" 
                                    accept="image/*" 
                                    :maxFileSize="90000000" 
                                    :showUploadButton="false" 
                                    :showCancelButton="false"
                                    style="background-color: white; border: none;"
                                    @select="onMultiImageSelect"
                                    @remove="onMultiImageRemove"
                                >
                                    <template #empty>
                                        <span>Faire glisser et déposez les fichiers ici.</span>
                                    </template>
                                </FileUpload>
                            </div>
                        </div>
                        
                        <!-- For Type of Article Posts -->
                        <div class="flex flex-col gap-2" v-if="postToEdit.type === 'article'">
                            <div>
                                <label class="text-sm text-black mb-2">URL de l'Article <span style="color: red;">*</span> :</label>
                                <input
                                    type="text"
                                    v-model="postToEdit.content.url"
                                    class="w-full border rounded-lg p-2"
                                    placeholder="e.g: www.example.com"
                                />
                            </div>

                            <div>
                                <label class="text-sm text-black mb-2">Titre de l'Article <span style="color: red;">*</span> :</label>
                                <input
                                    type="text"
                                    v-model="postToEdit.content.title"
                                    class="w-full border rounded-lg p-2"
                                    placeholder="Official LinkedIn Blog"
                                />
                            </div>

                            <div>
                                <label class="text-sm text-black mb-2">Description de l'Article :</label>
                                <input
                                    type="text"
                                    v-model="postToEdit.content.description"
                                    class="w-full border rounded-lg p-2"
                                    placeholder="Official LinkedIn Blog - Your source for insights and information about LinkedIn."
                                />
                            </div>
                        </div>

                        <textarea 
                            v-if="postToEdit.type !== 'text'"
                            v-model="postToEdit.content.caption"
                            name="caption" 
                            id="caption" 
                            class="border rounded-md w-full p-2 min-h-[300px] bg-white" 
                            placeholder="Ecrire quelque chose ou utiliser votre Assistant IA"
                        ></textarea>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex flex-row-reverse items-center justify-between gap-4">
                    <div class="flex flex-row-reverse items-center gap-2">
                        <!-- Update Button -->
                        <button 
                            v-if="postToEdit"
                            @click="savePostChanges"
                            class="bg-blue-600 text-white py-2 px-3 rounded-md text-md fw-semibold"
                        >
                            Enregistrer
                        </button>
                    </div>

                    <!-- DateTime Input -->
                    <div class="flex flex-col">
                        <input 
                            type="datetime-local" 
                            v-model="postToEdit.scheduledDateTime"
                            class="border bg-white p-2 rounded-lg" 
                            :min="campaignStartDate"
                            :max="campaignEndDate"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Post Preview Section -->
        <div class="bg-white p-3 h-full overflow-y-scroll rounded-lg min-w-[400px] max-w-[450px] min-h-[400px]">
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
                <div v-if="postToEdit.type === 'text'" class="px-2 my-2 max-h-[300px] overflow-y-scroll">
                    <p class="mb-0 text-black">{{ postToEdit.content.caption }}</p>
                </div>

                <!-- Image Type Preview -->
                <div v-if="postToEdit.type === 'image'" class="w-full h-auto">
                    <p class="my-2 px-2">{{ postToEdit.content.caption }}</p>
                    <img 
                        v-if="imagePreviewUrl || postToEdit.content.file_path"
                        :src="imagePreviewUrl || getMediaUrl(postToEdit.content.file_path)" 
                        class="object-fill w-full" 
                        alt="Image Preview"
                    />
                </div>

                <!-- Multi Image Type Preview -->
                <div v-if="postToEdit.type === 'multiimage'" class="w-full h-auto">
                    <p class="my-2 px-2">{{ postToEdit.content.caption }}</p>
                    <div v-if="imageLayout.images.length === 1" class="w-full">
                        <img :src="imageLayout.images[0]" class="object-fill w-full" alt="Preview" />
                    </div>
                    <div v-else-if="imageLayout.images.length === 2" class="grid grid-cols-2">
                        <img v-for="(image, index) in imageLayout.images" :key="index" :src="image" class="h-[150px] w-full object-cover" alt="Preview" />
                    </div>
                    <div v-else-if="imageLayout.images.length === 3" class="grid grid-cols-3">
                        <img v-for="(image, index) in imageLayout.images" :key="index" :src="image" class="h-[150px] w-full object-cover" alt="Preview" />
                    </div>
                    <div v-else-if="imageLayout.images.length >= 4" class="grid grid-cols-1">
                        <img :src="imageLayout.images[0]" class="max-h-[200px] w-full object-cover" alt="Preview" />
                        <div class="grid grid-cols-3">
                            <div v-for="(image, index) in imageLayout.images.slice(1)" :key="index" class="relative">
                                <img :src="image" class="h-[150px] w-full object-cover" alt="Preview" />
                                <div v-if="imageLayout.showOverlay && index === 2" class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                    <span class="text-white text-2xl">+{{ imageLayout.additionalCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Video Type Preview -->
                <div v-if="postToEdit.type === 'video'" class="w-full h-auto">
                    <p class="my-2 px-2">{{ postToEdit.content.caption }}</p>
                    <video 
                        v-if="videoPreviewUrl || postToEdit.content.file_path" 
                        controls 
                        class="w-full"
                    >
                        <source :src="videoPreviewUrl || getMediaUrl(postToEdit.content.file_path)" type="video/mp4">
                        Votre navigateur ne supporte pas la lecture de vidéos.
                    </video>
                </div>

                <!-- Article Type Preview -->
                <div v-if="postToEdit.type === 'article'" class="w-full h-auto">
                    <p class="my-2 px-2">{{ postToEdit.content.caption }}</p>
                    <div class="border p-2 rounded-md">
                        <a :href="postToEdit.content.url || '#'" target="_blank" class="text-blue-600 hover:underline">
                            {{ postToEdit.content.title }}
                        </a>
                        <p class="text-gray-500">{{ postToEdit.content.description || 'Description de l\'article' }}</p>
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
import { useToast } from 'vue-toastification';

export default {
    name: 'CampaignPostPortal',

    props: {
        allLinkedinAccounts: {
            type: Array,
            required: true,
        },

        selectedAccount: {
            type: Object,
            required: true,
        },

        selectedPost: {
            type: Object,
            required: true,
        },

        campaignStartDate: {
            type: String,
        },

        campaignEndDate: {
            type: String,
        },

        campaignPostError: {
            type: String,
            required: true,
        },

        onSave: Function,
    },

    emits: ['close'],

    setup() {
        const toast = useToast();
        return { toast };
    },

    data() {
        const today = new Date();

        return {
            postToEdit: this.deepCopyPost(this.selectedPost),
            postError: this.campaignPostError, 
            imagePreviewUrl: null,
            videoPreviewUrl: null,
            postTypes: [
                { value: "text", label: "Text", icon: "fas fa-align-left" },
                { value: "image", label: "Image", icon: "fas fa-image" },
                { value: "multiimage", label: "Multi-Image", icon: "fas fa-image" },
                { value: "video", label: "Video", icon: "fas fa-video" },
                { value: "article", label: "Article", icon: "fas fa-file-alt" },
            ],
            showConfirmExit: false,
            multiImagePreviews: [],
            isGenerating: false,
            prompt: '',
        };
    },

    computed: {
        imageLayout() {
            const previews = this.multiImagePreviews;
            if (!previews || previews.length === 0) {
                return { images: [], showOverlay: false, additionalCount: 0 };
            }
            if (previews.length === 1) {
                return { images: [previews[0]], showOverlay: false, additionalCount: 0 };
            }
            if (previews.length <= 3) {
                return { images: previews, showOverlay: false, additionalCount: 0 };
            }
            const displayImages = previews.slice(0, 4);
            const additionalCount = previews.length - 4;
            return { images: displayImages, showOverlay: additionalCount > 0, additionalCount };
        }
    },

    watch: {
        selectedPost: {
            handler(newPost) {
                this.postToEdit = this.deepCopyPost(newPost);
                if (newPost.type === 'multiimage' && newPost.content.files && newPost.content.files.length > 0) {
                    this.multiImagePreviews = newPost.content.files.map(file => {
                        if (file instanceof File) {
                            return URL.createObjectURL(file);
                        } else if (typeof file === 'string') {
                            return this.getMediaUrl(file);
                        }
                        return null;
                    }).filter(url => url !== null);
                } else {
                    this.multiImagePreviews = [];
                }
                if (newPost.content.file) {
                    if (newPost.type === 'image') {
                        this.imagePreviewUrl = URL.createObjectURL(newPost.content.file);
                    } else if (newPost.type === 'video') {
                        this.videoPreviewUrl = URL.createObjectURL(newPost.content.file);
                    }
                } else if (newPost.content.file_path) {
                    if (newPost.type === 'image') {
                        this.imagePreviewUrl = this.getMediaUrl(newPost.content.file_path);
                    } else if (newPost.type === 'video') {
                        this.videoPreviewUrl = this.getMediaUrl(newPost.content.file_path);
                    }
                } else {
                    this.imagePreviewUrl = null;
                    this.videoPreviewUrl = null;
                }
            },
            immediate: true,
        },
    },

    beforeDestroy() {
        this.multiImagePreviews.forEach(url => {
            if (url && url.startsWith('blob:')) {
                URL.revokeObjectURL(url);
            }
        });
        this.multiImagePreviews = [];
    },

    methods: {
        async generateAiContent() {
            const openaiKey = import.meta.env.VITE_OPENAI_API_KEY;
            this.isGenerating = true;

            try {
                const response = await axios.post('https://api.openai.com/v1/responses', {
                    model: 'gpt-4.1',
                    input: `${this.prompt}` 
                }, {
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": `Bearer ${openaiKey}`
                    }
                });

                if (response.status === 200) {
                    const output = response.data.output;
                    if (Array.isArray(output) && output.length > 0 && 
                        Array.isArray(output[0].content) && output[0].content.length > 0) {
                        const generatedText = output[0].content[0].text;
                        this.prompt = generatedText;
                    } else {
                        console.error('Invalid response structure: output or content missing');
                        this.prompt = 'une erreur s\'est produite';
                    }
                } else {
                    this.prompt = 'une erreur s\'est produite';
                }

            } catch(error) {
                console.error(error);
                this.showErrorToast("Une erreur s'est produite! Veuillez réessayer.");

            } finally {
                this.isGenerating = false;
            }
        },
        deepCopyPost(post) {
            if (!post) return null;
            const copy = {
                id: post.id,
                tempId: post.tempId || `temp-${Date.now()}`,
                job_id: post.job_id,
                scheduledDateTime: post.scheduledDateTime,
                type: post.type || 'text',
                content: {
                    text: post.content?.text || '',
                    caption: post.content?.caption || '',
                    url: post.content?.url || '',
                    title: post.content?.title || '',
                    description: post.content?.description || '',
                    file_path: post.content?.file_path || '',
                    files: Array.isArray(post.content?.files) ? post.content.files.map(file => file) : [], // Preserve File objects
                    original_filenames: Array.isArray(post.content?.original_filenames) ? [...post.content.original_filenames] : [],
                },
            };
            if (post.content?.file) {
                copy.content.file = post.content.file; // Preserve File object
                copy.content.fileName = post.content.fileName || post.content.file.name;
            } else {
                copy.content.file = null;
                copy.content.fileName = null;
            }
            return copy;
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

        utcToLocalForInput(utcString) {
            if (!utcString) return this.formatDateTime(new Date());
            const date = new Date(utcString);
            if (isNaN(date.getTime())) {
                console.error("Invalid date format:", utcString);
                return this.formatDateTime(new Date());
            }
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        },

        onMultiImageSelect(event) {
            const files = event.files;
            this.postToEdit.content.files = files;
            this.multiImagePreviews.forEach(url => {
                if (url && url.startsWith('blob:')) {
                    URL.revokeObjectURL(url);
                }
            });
            this.multiImagePreviews = files.map(file => URL.createObjectURL(file));
        },

        onMultiImageRemove(event) {
            const fileToRemove = event.file;
            this.postToEdit.content.files = this.postToEdit.content.files.filter(file => file !== fileToRemove);
            this.multiImagePreviews.forEach(url => {
                if (url && url.startsWith('blob:')) {
                    URL.revokeObjectURL(url);
                }
            });
            this.multiImagePreviews = this.postToEdit.content.files.map(file => {
                if (file instanceof File) {
                    return URL.createObjectURL(file);
                } else if (typeof file === 'string') {
                    return this.getMediaUrl(file);
                }
                return null;
            }).filter(url => url !== null);
        },

        handleFileUpload(event) {
            const file = event.target.files && event.target.files[0];
            if (file) {
                this.postToEdit.content.file = file;
                this.postToEdit.content.file_path = '';
                if (this.postToEdit.type === 'image') {
                    this.imagePreviewUrl = URL.createObjectURL(file);
                } else if (this.postToEdit.type === 'video') {
                    this.videoPreviewUrl = URL.createObjectURL(file);
                }
            } else {
                this.postToEdit.content.file = null;
                this.imagePreviewUrl = this.postToEdit.content.file_path ? this.getMediaUrl(this.postToEdit.content.file_path) : null;
                this.videoPreviewUrl = this.postToEdit.content.file_path ? this.getMediaUrl(this.postToEdit.content.file_path) : null;
            }
        },

        savePostChanges() {
            const postToSave = this.deepCopyPost(this.postToEdit);
            this.onSave(postToSave);
        },

        handleClose() {
            this.showConfirmExit = true;
        },

        confirmExit() {
            this.$emit('close');
            this.showConfirmExit = false;
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
    },
};
</script>


<style scoped>
.generate-btn {
    background: linear-gradient(
        135deg,
        rgb(255 16 185) 0%,
        rgb(255 125 82) 100%
    );
    border: none;
}
</style>