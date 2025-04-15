<template>
    <div class="bg-white vh-100 relative shadow-md" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
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
        <loading-overlay :isLoading="isSubmitting" message="Traitement en cours..." />

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
                            'bg-blue-500 text-white': selectedAccount && selectedAccount.id === linkedinUser.id,
                            'bg-white': !( selectedAccount && selectedAccount.id === linkedinUser.id)}"
                    >
                        <div class="flex flex-col align-items-center px-2 gap-2">
                            <img
                                :src="linkedinUser.linkedin_picture ? linkedinUser.linkedin_picture : '/build/assets/images/default-profile.png'"
                                alt="Profile Picture"
                                class="rounded-full"
                                width="100"
                            />
                            <p class="mb-0">{{ linkedinUser.linkedin_firstname }} {{ linkedinUser.linkedin_lastname }} </p>
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

            <!-- Integrated Campaign Form Component -->
            <div v-if="currentStep === 2" class="w-full flex flex-col align-items-center">
                <campaign-form
                    :initial-start-date="startDate"
                    :initial-end-date="endDate"
                    :initial-cible="selectedCible"
                    :initial-frequence="frequenceParJour"
                    :initial-description="descriptionCampagne"
                    :cibles="cibles"
                    @update:form-data="updateFormData"
                    @validate="isFormValid = $event"
                    @dates-updated="updateCampaignDates"
                />

                <button
                    @click="generatePostCards"
                    :disabled="!isFormValid"
                    class="bg-blue-500 text-white py-2 rounded-lg mt-4 disabled:bg-gray-300"
                >
                    Génerer les Posts
                </button>
            </div>

            

            <!-- Step 5: Calendar View of Posts -->
            <div v-if="currentStep === 3" class="w-full max-w-6xl">
                <h2 class="text-xl text-center font-bold mb-4">Calendrier des Posts</h2>
                
                <!-- Calendar Navigation Component -->
                <calendar-navigation v-model:currentMonth="currentMonth" v-model:currentYear="currentYear" />
                
                <!-- Legend -->
                <div class="flex gap-4 mb-4 text-sm">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-100 rounded-full mr-1"></div>
                        <span>Texte</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-100 rounded-full mr-1"></div>
                        <span>Image</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-100 rounded-full mr-1"></div>
                        <span>Vidéo</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-purple-100 rounded-full mr-1"></div>
                        <span>Article</span>
                    </div>
                </div>
                
                <!-- Calendar -->
                <div class="border rounded-lg p-4 bg-white">
                    <month-calendar
                        :posts="postCards"
                        :month="currentMonth"
                        :year="currentYear"
                        :onEditPost="editPost"
                    />
                </div>
                
                <!-- Post Edit Modal -->
                <post-modal
                    :post="selectedPost"
                    :postTypes="postTypes"
                    :isOpen="isPostModalOpen"
                    :onClose="closePostModal"
                    :onSave="savePostChanges"
                    :onDelete="deletePost"
                    :errorMessage="postModalError"
                    :campaignStartDateTime="campaignStartDateTime"
                    :campaignEndDateTime="campaignEndDateTime"
                />
                
                <!-- Error Messages -->
                <Popup
                    v-if="submissionError"
                    path="/build/assets/popups/sad-face.svg"
                    @close="submissionError = null"
                >
                    {{ submissionError }}
                </Popup>
                
                <!-- Action Buttons -->
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
            isFormValid: false,
            isSubmitting: false,
            submissionError: null,
            showSuccessPopup: false,
            successMessage: "",
            todayDate: this.formatDateTime(today),
            startDate: this.formatDateTime(today),
            endDate: this.formatDateTime(tomorrow),
            selectedCible: "",
            frequenceParJour: 1,
            descriptionCampagne: "",
            campaignStartDateTime: "",
            campaignEndDateTime: "",
            postTypes: [
                { value: "text", label: "Text", icon: "fas fa-align-left" },
                { value: "image", label: "Image", icon: "fas fa-image" },
                { value: "video", label: "Video", icon: "fas fa-video" },
                { value: "article", label: "Article", icon: "fas fa-file-alt" },
            ],
            cibles: [
                { id: '1', name: 'Audience 1' },
                { id: '2', name: 'Audience 2' },
                { id: '3', name: 'Audience 3' }
            ],
            postCards: [],
            // Calendar properties
            currentMonth: new Date().getMonth(),
            currentYear: new Date().getFullYear(),
            selectedPost: null,
            isPostModalOpen: false,
            postModalError: "",
            showDeleteSuccessPopup: false,
            addingNewPost: false,
        };
    },
    computed: {
        areAllPostsValid() {
            return this.postCards.every((post) => {
                if (!post.scheduledDateTime) {
                    return false;
                }

                const postDateTime = new Date(post.scheduledDateTime);
                const startDateTime = new Date(this.campaignStartDateTime);
                const endDateTime = new Date(this.campaignEndDateTime);

                if (postDateTime < startDateTime || postDateTime > endDateTime) {
                    return false;
                }

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

        updateFormData(formData) {
            this.startDate = formData.startDate;
            this.endDate = formData.endDate;
            this.selectedCible = formData.selectedCible;
            this.frequenceParJour = formData.frequenceParJour;
            this.descriptionCampagne = formData.descriptionCampagne;
        },

        updateCampaignDates({ startDate, endDate }) {
            this.startDate = startDate;
            this.endDate = endDate;
        },

        nextStep() {
            if(this.currentStep === 2) {
                if(!this.isStep2Valid) {
                    return;
                }
            }
            if (this.currentStep < 3) {
                this.currentStep++;
            }
        },

        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },

        toLocalISOString(date) { // THIS FUNCTION WAS CREATED TO FIX THE 'toISOString()' ERROR
            const pad = (num) => String(num).padStart(2, "0");
            const year = date.getFullYear();
            const month = pad(date.getMonth() + 1);
            const day = pad(date.getDate());
            const hours = pad(date.getHours());
            const minutes = pad(date.getMinutes());
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        },

        generatePostCards() {
            const start = new Date(this.startDate);
            const end = new Date(this.endDate);

            this.campaignStartDateTime = `${this.startDate}`;
            this.campaignEndDateTime = `${this.endDate}`;

            if (end < start) {
                console.error("End date is before start date");
                return;
            }

            // Calculate number of days
            const startDay = new Date(start);
            startDay.setHours(0, 0, 0, 0);
            const endDay = new Date(end);
            endDay.setHours(0, 0, 0, 0);
            const diffTime = Math.abs(endDay - startDay);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const daysToProcess = diffDays + 1;

            this.postCards = [];

            // Generate posts for each day
            for (let i = 0; i < daysToProcess; i++) {
                const currentDate = new Date(start);
                currentDate.setDate(start.getDate() + i);
                currentDate.setHours(0, 0, 0, 0);

                // Skip if the current date is after the end date
                if (currentDate > endDay) {
                    continue;
                }

                // Determine the time window for this day
                let dayStart, dayEnd;
                if (i === 0) {
                    dayStart = new Date(start);
                } else {
                    dayStart = new Date(currentDate);
                }

                if (currentDate.getTime() === endDay.getTime()) {
                    dayEnd = new Date(end);
                } else {
                    dayEnd = new Date(currentDate);
                    dayEnd.setHours(23, 59, 59, 999);
                }

                if (dayEnd <= dayStart) {
                    continue;
                }

                const durationMs = dayEnd - dayStart;
                const intervalMs = durationMs / Math.max(this.frequenceParJour, 1);

                for (let j = 0; j < this.frequenceParJour; j++) {
                    let postDateTime;
                    if (i === 0 && j === 0) {
                        postDateTime = new Date(start);
                    } else {
                        postDateTime = new Date(dayStart.getTime() + j * intervalMs);
                    }

                    if (postDateTime > end) {
                        continue;
                    }

                    this.postCards.push({
                        tempId: `post-${i}-${j}-${Date.now()}`,
                        scheduledDateTime: this.toLocalISOString(postDateTime),
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

            this.currentMonth = start.getMonth();
            this.currentYear = start.getFullYear();
            
            this.currentStep = 3;
        },


        formatDateTime(date) {
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${date.toISOString().split('T')[0]}T${hours}:${minutes}`;
        },

        editPost(post) {
            this.selectedPost = { ...post };
            // Preserve the content object and file reference
            if (post.content) {
                this.selectedPost.content = { ...post.content };
                if (post.content.file) {
                    this.selectedPost.content.file = post.content.file;
                }
            }
            this.isPostModalOpen = true;
            this.addingNewPost = false;
        },
        
        closePostModal() {
            this.isPostModalOpen = false;
            this.selectedPost = null;
        },

        // Refine the date output for the campaign start & end dates
        campaignDateTimeOutput(date) {
            const dateTime = new Date(date);
            
            const year = dateTime.getFullYear();
            const month = String(dateTime.getMonth() + 1).padStart(2, '0');
            const day = String(dateTime.getDate()).padStart(2, '0');
            const hours = String(dateTime.getHours()).padStart(2, '0');
            const minutes = String(dateTime.getMinutes()).padStart(2, '0');
            
            return `${month}/${day}/${year} ${hours}:${minutes}`;
        },

        savePostChanges(updatedPost) {
            const now = new Date();
            const scheduled = new Date(updatedPost.scheduledDateTime);
            const start = new Date(this.campaignStartDateTime);
            const end = new Date(this.campaignEndDateTime);

            if (scheduled < now) {
                this.postModalError = "La date de publication ne peut pas être dans le passé!";
                return;
            }

            if (scheduled < start || scheduled > end) {
                this.postModalError = `La date de publication doit être comprise entre ${this.campaignDateTimeOutput(this.campaignStartDateTime)} et ${this.campaignDateTimeOutput(this.campaignEndDateTime)} !`;
                return;
            }

            if (updatedPost.type === "text" && updatedPost.content.text.trim() === "") {
                this.postModalError = "Le contenu du post ne peut pas être vide !";
                return;
            }

            if ((updatedPost.type === "image" || updatedPost.type === "video") && !updatedPost.content.file) {
                this.postModalError = "Veuillez sélectionner un fichier pour le publier !";
                return;
            }

            if (updatedPost.type === "article") {
                const { url, title } = updatedPost.content;
                if (!url.trim() || !title.trim()) {
                    this.postModalError = "Veuillez remplir au moins l'URL et le titre de l'article.";
                    return;
                }
            }

            // Create a shallow copy while preserving the file reference
            const postToSave = { ...updatedPost };
            if (updatedPost.content) {
                postToSave.content = { ...updatedPost.content };
                if (updatedPost.content.file) {
                    postToSave.content.file = updatedPost.content.file;
                }
            }

            if (this.addingNewPost) {
                if (!postToSave.tempId) {
                    postToSave.tempId = `post-new-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
                }
                this.postCards.push(postToSave);
                this.addingNewPost = false;
            } else {
                const index = this.postCards.findIndex(p => {
                    if (postToSave.id && p.id === postToSave.id) {
                        return true;
                    }
                    if (postToSave.tempId && p.tempId === postToSave.tempId) {
                        return true;
                    }
                    return false;
                });

                if (index !== -1) {
                    this.postCards.splice(index, 1, postToSave);
                } else {
                    console.error("Could not find post to update:", postToSave);
                }
            }

            this.postModalError = "";
            this.closePostModal();
        },

        deletePost(id) {
            this.postCards = this.postCards.filter(post => post.id !== id && post.tempId !== id);
        },

        async submitAllPosts() {
            this.isSubmitting = true;
            this.submissionError = null;

            try {
                const sortedPosts = [...this.postCards].sort((a, b) => {
                    return new Date(a.scheduledDateTime) - new Date(b.scheduledDateTime);
                });

                for (const post of sortedPosts) {
                    let formData = new FormData();
                    formData.append("linkedin_id", this.selectedAccount.id);
                    formData.append("type", post.type);
                    formData.append("scheduled_date", post.scheduledDateTime);

                    switch (post.type) {
                        case "text":
                            formData.append("content[text]", post.content.text.trim());
                            break;

                        case "image":
                        case "video":
                            // Store the file and metadata instead of uploading
                            formData.append("content[file]", post.content.file);
                            formData.append("content[caption]", post.content.caption.trim());
                            formData.append("content[original_filename]", post.content.file.name);
                            break;

                        case "article":
                            formData.append("content[url]", post.content.url);
                            formData.append("content[title]", post.content.title);
                            formData.append("content[description]", post.content.description);
                            formData.append("content[caption]", post.content.caption.trim());
                            break;

                        default:
                            throw new Error("Invalid post type");
                    }

                    await axios.post("/linkedin/schedule-post", formData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        },
                    });
                }

                this.showSuccessPopup = true;
                this.successMessage = "Tous vos posts ont été programmés avec succès!";
                this.resetForm();
            } catch (error) {
                console.error("Error submitting posts:", error);
                this.submissionError = error.response?.data?.message || error.message || "Une erreur s'est produite lors de la publication des posts";
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
                formData.append("token", this.selectedAccount.linkedin_token); // Add token
                formData.append("linkedin_id", this.selectedAccount.linkedin_id); // Add linkedin_id

                const response = await axios.post("/linkedin/registermedia", formData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        },
                    }
                );

                if (response.data.status === 200) {
                    const binaryFormData = new FormData();
                    binaryFormData.append("token", this.selectedAccount.linkedin_token);
                    binaryFormData.append("upload_url", response.data.uploadUrl); // Expires in 15 minutes
                    binaryFormData.append("media", post.content.file);

                    const binaryResponse = await axios.post("/linkedin/upload-media-binary", binaryFormData, {
                            headers: {
                                "Content-Type": "multipart/form-data",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                            },
                        }
                    );

                    if (binaryResponse.data.status === 200) {
                        return response.data.asset;
                    } else {
                        throw new Error("Binary upload failed");
                    }
                } else {
                    throw new Error("Media registration failed");
                }
            } catch (error) {
                console.error("Error uploading media:", error);
                throw error;
            }
        },


        closeSuccessPopup() {
            this.showSuccessPopup = false;
            this.successMessage = "";
        },

        resetForm() {
            this.currentStep = 1;
            this.selectedAccount = null;
            this.startDate = this.todayDate;
            const tomorrow = new Date();
            tomorrow.setDate(new Date().getDate() + 1);
            this.endDate = tomorrow.toISOString().split("T")[0];
            this.frequenceParJour = 1;
            this.descriptionCampagne = "";
            this.postCards = [];
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
    padding: 2% 5%;
    margin-top: 50px;
}

.month::first-letter {
    text-transform: uppercase;
}

@media (max-width: 768px) {
    .card-main {
        margin-top: 15%;
        padding: 5%;
    }

    .flex.justify-between {
        flex-direction: column;
        gap: 0.5em;
    }

    button {
        width: 100%;
    }
}

button {
    padding: 0.75em 1.5em;
    font-size: 1em;
    transition: all 0.2s ease;
}

textarea {
    width: 100%;
    box-sizing: border-box;
    min-height: 8em;
    padding: 0.75em;
    border-radius: 0.5em;
    border: 1px solid #e2e8f0;
}

input[type="text"],
input[type="date"],
input[type="number"] {
    width: 100%;
    padding: 0.75em;
    border-radius: 0.5em;
    border: 1px solid #e2e8f0;
    box-sizing: border-box;
}
</style>