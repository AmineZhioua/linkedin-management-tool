<template>
    <div class="bg-white wh-100 vh-100 relative mt-8">
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
            <h2 v-if="currentStep === 1" class="text-xl font-bold mb-4">
                Sélectionner votre Compte LinkedIn
            </h2>
            <div v-if="currentStep === 1" class="w-full max-w-md">
                <div class="grid grid-cols-2 gap-4">
                    <div
                        v-for="linkedinUser in linkedinUserlist"
                        :key="linkedinUser.id"
                        @click="selectAccount(linkedinUser)"
                        class="py-4 rounded-xl border mb-4 cursor-pointer max-w-[250px]"
                        :class="{
                            'bg-blue-500 text-white':
                                selectedAccount &&
                                selectedAccount.id === linkedinUser.id,
                            'bg-white': !(
                                selectedAccount &&
                                selectedAccount.id === linkedinUser.id
                            ),
                        }"
                    >
                        <div
                            class="flex flex-col align-items-center px-2 gap-2"
                        >
                            <img
                                :src="
                                    linkedinUser.linkedin_picture
                                        ? linkedinUser.linkedin_picture
                                        : '/build/assets/images/default-profile.png'
                                "
                                alt="Profile Picture"
                                class="rounded-full"
                                width="100"
                            />
                            <p class="mb-0">
                                {{ linkedinUser.linkedin_firstname }}
                                {{ linkedinUser.linkedin_lastname }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Slot for the "Ajouter" Button in the Blade Template -->
                <slot></slot>
                <button
                    @click="nextStep"
                    :disabled="!selectedAccount"
                    class="w-full bg-blue-500 text-white py-2 rounded-lg mt-4 disabled:bg-gray-300"
                >
                    Commencer votre Campagne
                </button>
            </div>

            <!-- Step 2 : Date Debut & Date de Fin -->
            <div v-if="currentStep === 2" class="w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Durée du Campagne</h2>
                <!-- Date de Debut -->
                <div>
                    <label for="startDate" class="text-md mb-2">
                        Date de Début
                        <span style="color: red">*</span> :
                    </label>
                    <input
                        type="date"
                        id="startDate"
                        v-model="startDate"
                        class="w-full border rounded-lg p-2 mb-4"
                        :min="todayDate"
                    />
                </div>

                <!-- Date de Fin -->
                <div>
                    <label for="endDate" class="text-md mb-2">
                        Date de Fin <span style="color: red">*</span> :
                    </label>
                    <input
                        type="date"
                        id="endDate"
                        v-model="endDate"
                        class="w-full border rounded-lg p-2 mb-4"
                        :min="startDate"
                    />
                </div>

                <!-- Previous & Next Buttons -->
                <div class="flex justify-between mt-4">
                    <button
                        @click="prevStep"
                        class="bg-gray-300 text-black py-2 px-4 rounded-lg"
                    >
                        Back
                    </button>
                    <button
                        @click="nextStep"
                        :disabled="!startDate || !endDate"
                        class="bg-blue-500 text-white py-2 px-4 rounded-lg disabled:bg-gray-300"
                    >
                        Next
                    </button>
                </div>
            </div>

            <!-- Step 3 : Description du Campagne -->
            <div v-if="currentStep === 3" class="w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">
                    Description du Campagne :
                </h2>
                <textarea
                    name="descriptionCampagne"
                    id="descriptionCampagne"
                    v-model="descriptionCampagne"
                    class="w-full border rounded-lg p-3 h-32 mb-2"
                    placeholder="Décrivez votre Campagne ici..."
                ></textarea>

                <!-- Previous & Next Buttons -->
                <div class="flex justify-between mt-4">
                    <button
                        @click="prevStep"
                        class="bg-gray-300 text-black py-2 px-4 rounded-lg"
                    >
                        Back
                    </button>
                    <button
                        @click="nextStep"
                        :disabled="!descriptionCampagne.trim()"
                        class="bg-blue-500 text-white py-2 px-4 rounded-lg disabled:bg-gray-300"
                    >
                        Next
                    </button>
                </div>
            </div>

            <!-- Step 4 : Frequence des Posts -->
            <div v-if="currentStep === 4" class="w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Fréquence des Posts :</h2>
                <div class="flex align-items-center relative">
                    <input
                        type="number"
                        v-model="frequenceParJour"
                        class="w-full border rounded-lg p-2 px-4"
                        placeholder="Nombre de Posts"
                        min="1"
                        max="10"
                    />

                    <button
                        class="bg-black text-white absolute right-0 py-2 px-3"
                        style="
                            border-top-right-radius: 8px;
                            border-bottom-right-radius: 8px;
                        "
                        disabled
                    >
                        par Jour
                    </button>
                </div>

                <!-- Previous & Next Buttons -->
                <div class="flex justify-between mt-4">
                    <button
                        @click="prevStep"
                        class="bg-gray-300 text-black py-2 px-4 rounded-lg"
                    >
                        Back
                    </button>
                    <button
                        @click="generatePostCards"
                        :disabled="
                            frequenceParJour < 1 || frequenceParJour > 10
                        "
                        class="bg-blue-500 text-white py-2 px-4 rounded-lg disabled:bg-gray-300"
                    >
                        Générer les Posts
                    </button>
                </div>
            </div>

            <!-- Step 5: Post Cards -->
            <div v-if="currentStep === 5" class="w-full max-w-4xl">
                <h2 class="text-xl font-bold mb-4">Configurez vos Posts</h2>

                <div class="grid gap-6">
                    <div
                        v-for="(post, index) in postCards"
                        :key="index"
                        class="border rounded-lg p-6"
                    >
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold">Post #{{ index + 1 }}</h3>
                            <div class="flex items-center gap-2">
                                <input
                                    type="datetime-local"
                                    v-model="post.scheduledDateTime"
                                    :min="campaignStartDateTime"
                                    :max="campaignEndDateTime"
                                    class="border rounded p-2"
                                />
                            </div>
                        </div>

                        <div class="flex items-center gap-2 mb-4">
                            <label class="text-sm">Type de Post:</label>
                            <select
                                v-model="post.type"
                                @change="resetPostContent(post)"
                                class="border rounded p-2"
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

                        <!-- Content based on post type -->
                        <div v-if="post.type === 'text'">
                            <textarea
                                v-model="post.content.text"
                                placeholder="Exprimez-vous !"
                                class="w-full border rounded-lg p-2 h-32 mb-4"
                            ></textarea>
                        </div>

                        <div
                            v-if="
                                post.type === 'image' || post.type === 'video'
                            "
                        >
                            <input
                                type="file"
                                @change="(e) => handleFileUpload(e, post)"
                                :accept="
                                    post.type === 'image'
                                        ? 'image/*'
                                        : 'video/*'
                                "
                                class="w-full border rounded-lg p-2 mb-2"
                            />
                            <p
                                v-if="post.content.file"
                                class="mt-2 text-sm text-gray-600"
                            >
                                Fichier sélectionné :
                                {{ post.content.file.name }}
                            </p>
                            <textarea
                                v-model="post.content.caption"
                                placeholder="Ajouter une légende... (Optionnel)"
                                class="w-full border rounded-lg p-2 h-32 mt-2"
                            ></textarea>
                        </div>

                        <div
                            v-if="post.type === 'article'"
                            class="flex flex-col gap-2"
                        >
                            <label for="article" class="text-sm text-gray-600"
                                >Article URL* :</label
                            >
                            <input
                                type="text"
                                class="w-full border rounded-lg p-2"
                                placeholder="e.g: www.example.com"
                                v-model="post.content.url"
                            />
                            <label for="title" class="text-sm text-gray-600"
                                >Article Title :</label
                            >
                            <input
                                type="text"
                                class="w-full border rounded-lg p-2"
                                placeholder="Official LinkedIn Blog"
                                v-model="post.content.title"
                            />
                            <label
                                for="description"
                                class="text-sm text-gray-600"
                                >Article Description :</label
                            >
                            <input
                                type="text"
                                class="w-full border rounded-lg p-2"
                                placeholder="Official LinkedIn Blog - Your source for insights and information about LinkedIn."
                                v-model="post.content.description"
                            />
                            <textarea
                                v-model="post.content.caption"
                                placeholder="Ajouter un commentaire... (Optionnel)"
                                class="w-full border rounded-lg p-2 h-32 mt-2"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Error Messages -->
                <Popup
                    v-if="submissionError"
                    path="/build/assets/popups/sad-face.svg"
                    @close="submissionError = null"
                >
                    {{ submissionError }}
                </Popup>

                <div class="flex justify-between mt-6">
                    <button
                        @click="prevStep"
                        class="bg-gray-300 text-black py-2 px-4 rounded-lg"
                    >
                        Back
                    </button>
                    <button
                        @click="submitAllPosts"
                        :disabled="!areAllPostsValid"
                        class="bg-blue-500 text-white py-2 px-4 rounded-lg disabled:bg-gray-300"
                    >
                        Publier tous les Posts
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
        const today = new Date();
        const tomorrow = new Date();
        tomorrow.setDate(today.getDate() + 1);

        return {
            currentStep: 1,
            selectedAccount: null,
            isSubmitting: false,
            submissionError: null,
            showSuccessPopup: false,
            successMessage: "",
            todayDate: today.toISOString().split("T")[0],
            startDate: today.toISOString().split("T")[0],
            endDate: tomorrow.toISOString().split("T")[0],
            frequenceParJour: 1,
            descriptionCampagne: "",
            campaignStartDateTime: "",
            campaignEndDateTime: "",
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
            postCards: [],
        };
    },
    computed: {
        areAllPostsValid() {
            return this.postCards.every((post) => {
                // Validate datetime
                if (!post.scheduledDateTime) return false;

                const postDateTime = new Date(post.scheduledDateTime);
                const startDateTime = new Date(this.campaignStartDateTime);
                const endDateTime = new Date(this.campaignEndDateTime);

                if (
                    postDateTime < startDateTime ||
                    postDateTime > endDateTime
                ) {
                    return false;
                }

                // Validate content based on type
                switch (post.type) {
                    case "text":
                        return post.content.text.trim() !== "";
                    case "image":
                    case "video":
                        return !!post.content.file;
                    case "article":
                        return !!post.content.url;
                    default:
                        return false;
                }
            });
        },
    },
    methods: {
        selectAccount(account) {
            this.selectedAccount = account;
        },

        nextStep() {
            if (this.currentStep < 4) {
                this.currentStep++;
            }
        },

        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },

        generatePostCards() {
            this.postCards = [];
            const start = new Date(this.startDate);
            const end = new Date(this.endDate);

            // Set campaign datetime boundaries
            this.campaignStartDateTime = `${this.startDate}T00:00`;
            this.campaignEndDateTime = `${this.endDate}T23:59`;

            // Calculate total days
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

            // Generate posts for each day
            for (let i = 0; i < diffDays; i++) {
                const currentDate = new Date(start);
                currentDate.setDate(start.getDate() + i);

                // Generate posts for each frequency per day
                for (let j = 0; j < this.frequenceParJour; j++) {
                    // Calculate time slot (distribute posts evenly throughout the day)
                    const timeSlot = Math.floor(
                        (24 / this.frequenceParJour) * j
                    );
                    const hours = Math.min(timeSlot, 23);
                    const minutes = Math.floor((timeSlot % 1) * 60) || 0;

                    const postDateTime = new Date(currentDate);
                    postDateTime.setHours(hours, minutes, 0, 0);

                    this.postCards.push({
                        scheduledDateTime: postDateTime
                            .toISOString()
                            .slice(0, 16),
                        type: "text",
                        content: {
                            text: "",
                            file: null,
                            caption: "",
                            url: "",
                            title: "",
                            description: "",
                        },
                    });
                }
            }

            this.currentStep = 5;
        },

        handleFileUpload(event, post) {
            post.content.file = event.target.files[0];
        },

        resetPostContent(post) {
            post.content = {
                text: "",
                file: null,
                caption: "",
                url: "",
                title: "",
                description: "",
            };
        },

        async submitAllPosts() {
            this.isSubmitting = true;
            this.submissionError = null;

            try {
                const sortedPosts = [...this.postCards].sort((a, b) => {
                    return (
                        new Date(a.scheduledDateTime) -
                        new Date(b.scheduledDateTime)
                    );
                });

                for (const post of sortedPosts) {
                    let formData;

                    switch (post.type) {
                        case "text":
                            formData = {
                                linkedin_id: this.selectedAccount.id,
                                type: "text",
                                content: {
                                    text: post.content.text.trim(),
                                },
                                scheduled_date: post.scheduledDateTime,
                            };
                            break;

                        case "image":
                        case "video":
                            const asset = await this.uploadMedia(post);
                            formData = {
                                linkedin_id: this.selectedAccount.id,
                                type: post.type,
                                content: {
                                    asset: asset,
                                    caption: post.content.caption.trim(),
                                },
                                scheduled_date: post.scheduledDateTime,
                            };
                            break;

                        case "article":
                            formData = {
                                linkedin_id: this.selectedAccount.id,
                                type: "article",
                                content: {
                                    url: post.content.url,
                                    title: post.content.title,
                                    description: post.content.description,
                                    caption: post.content.caption.trim(),
                                },
                                scheduled_date: post.scheduledDateTime,
                            };
                            break;

                        default:
                            throw new Error("Invalid post type");
                    }

                    await axios.post("/linkedin/schedule-post", formData, {
                        headers: {
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                    });
                }

                this.showSuccessPopup = true;
                this.successMessage =
                    "Tous vos posts ont été programmés avec succès!";
                this.resetForm();
            } catch (error) {
                console.error("Error submitting posts:", error);
                this.submissionError =
                    error.response?.data?.message ||
                    error.message ||
                    "Une erreur s'est produite lors de la publication des posts";
            } finally {
                this.isSubmitting = false;
            }
        },

        async uploadMedia(post) {
            try {
                const formData = new FormData();
                formData.append("media", post.content.file);
                formData.append("type", post.type);
                formData.append("caption", post.content.caption.trim());
                formData.append("token", this.selectedAccount.linkedin_token);
                formData.append(
                    "linkedin_id",
                    this.selectedAccount.linkedin_id
                );

                const response = await axios.post(
                    "/linkedin/registermedia",
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                    }
                );

                if (response.data.status === 200) {
                    return response.data.asset;
                } else {
                    throw new Error("Media upload failed");
                }
            } catch (error) {
                console.error("Error uploading media:", error);
                throw error;
            }
        },

        async submitTextPost(post) {
            const postData = {
                token: this.selectedAccount.linkedin_token,
                linkedin_id: this.selectedAccount.linkedin_id,
                caption: post.content.text.trim(),
                scheduled_date: post.scheduledDateTime,
            };

            await axios.post("/linkedin/publish", postData, {
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            });
        },

        async submitMediaPost(post) {
            const formData = new FormData();
            formData.append("token", this.selectedAccount.linkedin_token);
            formData.append("linkedin_id", this.selectedAccount.linkedin_id);
            formData.append("type", post.type);
            formData.append("media", post.content.file);
            formData.append("caption", post.content.caption.trim());
            formData.append("scheduled_date", post.scheduledDateTime);

            const registerResponse = await axios.post(
                "/linkedin/registermedia",
                formData,
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                }
            );

            if (registerResponse.data.status === 200) {
                const binaryUploadFormData = new FormData();
                binaryUploadFormData.append(
                    "token",
                    this.selectedAccount.linkedin_token
                );
                binaryUploadFormData.append(
                    "upload_url",
                    registerResponse.data.uploadUrl
                );
                binaryUploadFormData.append("media", post.content.file);

                await axios.post(
                    "/linkedin/upload-media-binary",
                    binaryUploadFormData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                    }
                );

                await this.shareMediaPost(
                    registerResponse.data.asset,
                    post.content.caption.trim(),
                    post.type === "image" ? "IMAGE" : "VIDEO",
                    post.scheduledDateTime
                );
            } else {
                throw new Error(
                    registerResponse.data.error || "Media registration failed"
                );
            }
        },

        async shareMediaPost(asset, caption, mediaType, scheduledDateTime) {
            const shareData = {
                token: this.selectedAccount.linkedin_token,
                linkedin_id: this.selectedAccount.linkedin_id,
                asset: asset,
                caption: caption,
                media_type: mediaType,
                scheduled_date: scheduledDateTime,
            };

            const response = await axios.post(
                "/linkedin/share-media",
                shareData,
                {
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                }
            );

            return response;
        },

        async submitArticlePost(post) {
            const articleData = {
                token: this.selectedAccount.linkedin_token,
                linkedin_id: this.selectedAccount.linkedin_id,
                article_url: post.content.url,
                article_title: post.content.title,
                article_description: post.content.description,
                caption: post.content.caption.trim(),
                scheduled_date: post.scheduledDateTime,
            };

            await axios.post("/linkedin/share-article", articleData, {
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            });
        },

        resetForm() {
            this.currentStep = 1;
            this.selectedAccount = null;
            this.postCards = [];
            this.startDate = this.todayDate;
            const tomorrow = new Date();
            tomorrow.setDate(new Date().getDate() + 1);
            this.endDate = tomorrow.toISOString().split("T")[0];
            this.frequenceParJour = 1;
            this.descriptionCampagne = "";
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
    padding: 2% 5%; /* Using percentages for responsive padding */
    margin-top: 10%; /* Added margin to push content below header */
    overflow-y: auto; /* Add scroll if content is too long */
}

/* Style for datetime input */
input[type="datetime-local"] {
    padding: 0.5em;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-family: inherit;
    width: 100%;
    box-sizing: border-box;
}

/* Responsive grid for post cards */
.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5em;
    width: 100%;
}

/* Post card styling */
.post-card {
    border: 1px solid #e2e8f0;
    border-radius: 0.5em;
    padding: 1.5em;
    background: white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-main {
        margin-top: 15%; /* More margin on smaller screens */
        padding: 5%; /* More padding on smaller screens */
    }

    .posts-grid {
        grid-template-columns: 1fr;
    }

    .flex.justify-between {
        flex-direction: column;
        gap: 0.5em;
    }

    button {
        width: 100%;
    }
}

/* Ensure the container doesn't overflow */
.wh-100 {
    width: 100%;
    overflow-x: hidden;
}

/* Button styling adjustments */
button {
    padding: 0.75em 1.5em;
    font-size: 1em;
    border-radius: 0.5em;
    transition: all 0.2s ease;
}

/* Textarea styling */
textarea {
    width: 100%;
    box-sizing: border-box;
    min-height: 8em;
    padding: 0.75em;
    border-radius: 0.5em;
    border: 1px solid #e2e8f0;
}

/* Input field styling */
input[type="text"],
input[type="date"],
input[type="number"],
select {
    width: 100%;
    padding: 0.75em;
    border-radius: 0.5em;
    border: 1px solid #e2e8f0;
    box-sizing: border-box;
}
</style>
