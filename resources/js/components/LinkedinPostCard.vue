<template>
    <div class="bg-white wh-100 vh-100 relative mt-8 plateforme-card">
        <!-- Heart Sticker -->
        <div class="absolute top-[-40px] left-[40px] flex items-center">
            <img
                src="/build/assets/icons/heart-sticker.svg"
                alt="Heart Sticker"
                class="z-10"
                height="90"
                width="90"
            />
            <img
                src="/build/assets/icons/plateforme-text.svg"
                alt="Plateforme Text"
                class="hidden lg:block ml-[-25px]"
                width="300"
            />
        </div>

        <!-- Wave Image -->
        <img
            src="/build/assets/images/waves.svg"
            alt="Waves Image"
            class="waves"
        />

        <!-- Loading Overlay -->
        <loading-overlay
            :isLoading="isSubmitting"
            message="Traitement en cours..."
        />

        <!-- Success Popup -->
        <Popup
            v-if="showSuccessPopup"
            path="/build/assets/icons/heart-sticker.svg"
            @close="closeSuccessPopup"
        >
            {{ successMessage }}
        </Popup>

        <!-- Card Main Content -->
        <div class="flex flex-col items-center justify-center card-main">
            <!-- Step 1: Choose LinkedIn Account -->
            <div v-if="currentStep === 1" class="w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">
                    Sélectionner votre Compte LinkedIn
                </h2>
                <div
                    v-for="linkedinUser in linkedinUserlist"
                    :key="linkedinUser.id"
                    @click="selectAccount(linkedinUser)"
                    class="bg-white px-4 py-2 rounded-xl border mb-4 cursor-pointer"
                    :class="{
                        'border-blue-500':
                            selectedAccount &&
                            selectedAccount.id === linkedinUser.id,
                    }"
                >
                    <div class="flex flex-row items-center gap-2">
                        <img
                            :src="linkedinUser.linkedin_picture"
                            alt="Profile Picture"
                            class="rounded-full"
                            width="80"
                        />
                        <p class="mb-0">
                            {{ linkedinUser.linkedin_firstname }}
                            {{ linkedinUser.linkedin_lastname }}
                        </p>
                    </div>
                </div>
                <button
                    @click="nextStep"
                    :disabled="!selectedAccount"
                    class="w-full bg-blue-500 text-white py-2 rounded-lg mt-4 disabled:bg-gray-300"
                >
                    Next
                </button>
            </div>

            <!-- Step 2: Choose Post Type -->
            <div v-if="currentStep === 2" class="w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Choissisez le Type du Post</h2>
                <div class="grid grid-cols-3 gap-4">
                    <div
                        v-for="type in postTypes"
                        :key="type.value"
                        @click="selectPostType(type.value)"
                        class="border rounded-lg p-4 text-center cursor-pointer"
                        :class="{'bg-blue-500 text-white': selectedPostType === type.value}"
                    >
                        <i :class="type.icon" class="text-3xl mb-2"></i>
                        <p>{{ type.label }}</p>
                    </div>
                </div>
                <div class="flex justify-between mt-4">
                    <button
                        @click="prevStep"
                        class="bg-gray-300 text-black py-2 px-4 rounded-lg"
                    >
                        Back
                    </button>
                    <button
                        @click="nextStep"
                        :disabled="!selectedPostType"
                        class="bg-blue-500 text-white py-2 px-4 rounded-lg disabled:bg-gray-300"
                    >
                        Next
                    </button>
                </div>
            </div>

            <!-- Step 3: Add Content -->
            <div v-if="currentStep === 3" class="w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">
                    {{
                        selectedPostType
                            ? `Ajouter ${postTypes.find((t) => t.value === selectedPostType).label}` : "Ajouter le contenu du Post"
                    }}
                </h2>

                <!-- File Input for Image/Video/Article -->
                <div
                    v-if = "selectedPostType === 'image' || selectedPostType === 'video'"
                    class="mb-4"
                >
                    <input
                        type="file"
                        @change="handleFileUpload"
                        :accept="getAcceptedFileTypes()"
                        class="w-full border rounded-lg p-2"
                    />
                    <p v-if="uploadedFile" class="mt-2 text-sm text-gray-600">
                        Le fichier Sélectionné : {{ uploadedFile.name }}
                    </p>
                </div>

                <!-- Input For Article -->
                <div
                    class="mb-4 flex flex-col gap-2"
                    v-if = "selectedPostType == 'article'"
                >
                    <label for="article" class="text-sm text-gray-600">Article URL* :</label>
                    <input
                        type="text"
                        class="w-full border rounded-lg p-2"
                        placeholder="e.g: www.example.com"
                        v-model="articleUrl"
                    />
                    <label for="title" class="text-sm text-gray-600">Article Title :</label>
                    <input
                        type="text"
                        class="w-full border rounded-lg p-2"
                        placeholder="Official LinkedIn Blog"
                        v-model="articleTitle"
                    />
                    <label for="description" class="text-sm text-gray-600">Article Description :</label>
                    <input
                        type="text"
                        class="w-full border rounded-lg p-2"
                        placeholder="Official LinkedIn Blog - Your source for insights and information about LinkedIn."
                        v-model="articleDescription"
                    />
                </div>

                <!-- Text Input -->
                <textarea
                    v-model="postText"
                    placeholder="What do you want to express?"
                    class="w-full border rounded-lg p-2 h-32 mb-4"
                >
                </textarea>

                <!-- Error Messages -->
                <Popup
                    v-if="submissionError"
                    path="/build/assets/popups/sad-face.svg"
                    @close="submissionError = null"
                >
                    {{ submissionError }}
                </Popup>

                <div class="flex justify-between">
                    <button
                        @click="prevStep"
                        class="bg-gray-300 text-black py-2 px-4 rounded-lg"
                    >
                        Back
                    </button>
                    <button
                        @click="handlePostSubmission"
                        :disabled="!isPostSubmissionValid()"
                        class="bg-blue-500 text-white py-2 px-4 rounded-lg disabled:bg-gray-300"
                    >
                        Post
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Popup from "./Popup.vue";

export default {
    components: {
        Popup,
    },
    props: {
        linkedinUserlist: {
            type: Array,
            required: true,
            default: [],
        },
    },
    data() {
        return {
            currentStep: 1,
            selectedAccount: null,
            selectedPostType: null,
            isSubmitting: false,
            submissionError: null,
            showSuccessPopup: false,
            successMessage: "",
            postTypes: [
                {
                    value: "text",
                    label: "Text",
                    icon: "fas fa-align-left",
                },
                {
                    value: "image",
                    label: "Image",
                    icon: "fas fa-image",
                },
                {
                    value: "video",
                    label: "Video",
                    icon: "fas fa-video",
                },
                {
                    value: "article",
                    label: "Article",
                    icon: "fas fa-file-alt",
                },
            ],
            uploadedFile: null,
            postText: "",
            articleTitle: "",
            articleDescription: "",
            articleUrl: "",
        };
    },
    methods: {
        selectAccount(account) {
            this.selectedAccount = account;
        },

        selectPostType(type) {
            this.selectedPostType = type;
        },

        nextStep() {
            if (this.currentStep < 3) {
                this.currentStep++;
            }
        },

        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },

        handleFileUpload(event) {
            this.uploadedFile = event.target.files[0];
        },

        getAcceptedFileTypes() {
            switch (this.selectedPostType) {
                case "image":
                    return "image/*";
                case "video":
                    return "video/*";
                default:
                    return "";
            }
        },

        isPostSubmissionValid() {
            if (!this.selectedAccount) return false;
            if (!this.postText.trim()) {
                return false;
            };

            switch (this.selectedPostType) {
                case "text":
                    return true;
                case "image":
                case "video":
                    return !!this.uploadedFile;
                case "article":
                    return !!this.articleUrl;
                default:
                    return true;
            }
        },

        handlePostSubmission() {
            this.submissionError = null;

            switch (this.selectedPostType) {
                case "text":
                    this.submitPost();
                    break;
                case "image":
                case "video":
                    this.registerAndShareMedia();
                    break;
                case "article":
                    this.shareArticle();
                    break;
                default:
                    this.submitPost(); // Default to text post
            }
        },


        async submitPost() {
            this.isSubmitting = true;
            this.submissionError = null;

            try {
                const postData = {
                    token: this.selectedAccount.linkedin_token,
                    linkedin_id: this.selectedAccount.linkedin_id,
                    caption: this.postText.trim(),
                };

                const response = await axios.post("/linkedin/publish", postData, {
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        },
                    }
                );

                // Handle successful post
                if (
                    response.data.status === 201 ||
                    response.data.status === 200
                ) {
                    this.showSuccessPopup = true;
                    this.successMessage = "Votre post a été publié avec succès sur LinkedIn!";
                    this.resetForm();
                } else {
                    this.submissionError = "Une erreur s'est produite lors de la publication du post";
                }
            } catch (error) {
                this.submissionError = error.response?.data?.message || "Une erreur s'est produite lors de la publication du post";
            } finally {
                this.isSubmitting = false;
            }
        },
        async registerAndShareMedia() {
            this.isSubmitting = true;
            this.submissionError = null;

            try {
                const formData = new FormData();
                formData.append("token", this.selectedAccount.linkedin_token);
                formData.append("linkedin_id", this.selectedAccount.linkedin_id);
                formData.append("type", this.selectedPostType);
                formData.append("media", this.uploadedFile);
                formData.append("caption", this.postText.trim());

                // Register the media
                const registerResponse = await axios.post("/linkedin/registermedia", formData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        },
                    }
                );

                // Check if media registration was successful
                if (registerResponse.data.status === 200) {
                    const binaryUploadFormData = new FormData();
                    binaryUploadFormData.append("token", this.selectedAccount.linkedin_token);
                    binaryUploadFormData.append("upload_url", registerResponse.data.uploadUrl);
                    binaryUploadFormData.append("media", this.uploadedFile);

                    await axios.post("/linkedin/upload-media-binary", binaryUploadFormData,{
                        headers: {
                            "Content-Type": "multipart/form-data",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        },
                        }
                    );

                    const shareResponse = await this.shareMediaPost(registerResponse.data.asset, this.postText.trim(),
                        this.selectedPostType === "image" ? "IMAGE" : "VIDEO"
                    );

                    this.showSuccessPopup = true;
                    this.successMessage = "Votre post a été publié avec succès sur LinkedIn!";
                    this.resetForm();
                } else {
                    console.log(registerResponse.data.error); // for debugging
                    throw new Error(registerResponse.data.error || "Media registration failed");
                }
            } catch (error) {
                this.submissionError = "Une erreur s'est produite lors de la publication du post!";
            } finally {
                this.isSubmitting = false;
            }
        },
        async shareMediaPost(asset, caption, mediaType) {
            try {
                const shareData = {
                    token: this.selectedAccount.linkedin_token,
                    linkedin_id: this.selectedAccount.linkedin_id,
                    asset: asset,
                    caption: caption,
                    media_type: mediaType,
                };

                const response = await axios.post("/linkedin/share-media", shareData, {
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        },
                    }
                );

                return response;
            } catch (error) {
                this.submissionError = "Une erreur s'est produite lors de la publication de votre post!";
                console.log(error); // for debugging
                throw error;
            }
        },
        async shareArticle() {
            this.isSubmitting = true;
            this.submissionError = null;

            try {
                const articleData = {
                    token: this.selectedAccount.linkedin_token,
                    linkedin_id: this.selectedAccount.linkedin_id,
                    article_url: this.articleUrl,
                    article_title: this.articleTitle,
                    article_description: this.articleDescription,
                    caption: this.postText.trim(),
                };

                const response = await axios.post( "/linkedin/share-article", articleData, {
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        },
                    }
                );

                if (response.data.status === "success") {
                    this.showSuccessPopup = true;
                    this.successMessage = "Votre Article a été publié avec succès!";
                    this.resetForm();
                } else {
                    console.log(response.data.error); // for debugging
                    throw new Error(response.data.message || "Failed to share article");
                }
            } catch (error) {
                this.submissionError = "Une erreur s'est produite lors de la publication de votre article!";
            } finally {
                this.isSubmitting = false;
            }
        },
        resetForm() {
            this.currentStep = 1;
            this.selectedAccount = null;
            this.selectedPostType = null;
            this.uploadedFile = null;
            this.postText = "";
            this.articleUrl = "";
            this.articleTitle = "";
            this.articleDescription = "";
        },
        closeSuccessPopup() {
            this.showSuccessPopup = false;
        },
    },
};
</script>

<style scoped>
.waves {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 100%;
    z-index: 1;
}

.card-main {
    position: relative;
    z-index: 10;
    height: 100%;
    padding: 20px;
}
</style>
