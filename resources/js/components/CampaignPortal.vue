<template>
    <div class="bg-black bg-opacity-50 inset-0 h-[100vh] w-full absolute"></div>
    <div class="flex items-center w-full p-4 justify-center gap-2 absolute top-[50%] left-[50%] translate-y-[-50%] translate-x-[-50%]">
        <div class="bg-white p-4 rounded-md">
            <div class="flex flex-col gap-3" v-if="currentStep === 1">
                <div class="flex items-center justify-between mb-2">
                    <h3 v-if="!readMode" class="text-black text-lg mb-0">Commencez votre Campagne personnalisé :</h3>
                    <h3 v-else class="text-black text-lg mb-0">Détails de votre Campagne :</h3>
                    <button @click="handleClose">
                        <img src="/build/assets/icons/close.svg" alt="Close Icon" height="20" width="20" />
                    </button>
                </div>

                <p v-if="errorMessage && !readMode" class="text-red-500 text-sm mb-2 px-2 py-3 rounded-lg bg-red-200">
                    <i class="fa-solid fa-circle-exclamation mx-2 text-xl" style="color: red;"></i>
                    {{ errorMessage }}
                </p>

                <!-- Read Mode: Campaign Details -->
                <div v-if="readMode">
                    <CampaignDetails 
                        :campaign="selectedCampaign" 
                        :linkedin-account="selectedAccount" 
                        :posts="linkedinPosts" 
                        :cibles="cibles" 
                    />
                </div>

                <!-- Create Mode: Campaign Form -->
                <div v-else>
                    <div class="flex items-center justify-between gap-2">
                        <!-- Date de Debut -->
                        <div class="w-full">
                            <label for="startDate" class="block text-md mb-2">
                                Date de Début <span class="text-red-500">*</span> :
                            </label>
                            <input
                                type="datetime-local"
                                id="startDate"
                                v-model="startDate"
                                :min="todayDate"
                                :class="{'border-red-500': startDateErrorMessage}"
                                class="w-full border bg-white rounded-lg p-2 mb-1"
                                @change="updateDates"
                            />
                        </div>
                        <!-- Date de Fin -->
                        <div class="w-full">
                            <label for="endDate" class="block text-md mb-2">
                                Date de Fin <span class="text-red-500">*</span> :
                            </label>
                            <input
                                type="datetime-local"
                                id="endDate"
                                v-model="endDate"
                                :min="startDate"
                                :class="{'border-red-500': endDateErrorMessage}"
                                class="w-full border bg-white rounded-lg p-2 mb-1"
                                @change="updateDates"
                            />
                        </div>
                    </div>

                    <!-- Audience Cible -->
                    <div>
                        <label for="cible" class="block text-md mb-2">
                            Choisissez votre Audience cible <span class="text-red-500">*</span> :
                        </label>
                        <select
                            id="cible"
                            v-model="selectedCible"
                            class="w-full border rounded-lg bg-gray-200 p-2 mb-1"
                            :class="{'border-red-500': cibleErrorMessage}"
                        >
                            <option value="" disabled>Choisissez votre audience</option>
                            <option v-for="cible in cibles" :key="cible.id" :value="cible.id">
                                {{ cible.name }}
                            </option>
                        </select>
                        <p v-if="cibleErrorMessage" class="text-red-500 text-sm mb-2">
                            {{ cibleErrorMessage }}
                        </p>
                    </div>

                    <!-- Frequence des Posts -->
                    <div>
                        <label for="frequence" class="block text-md mb-2">Fréquence des Posts <span class="text-red-500">*</span> :</label>
                        <div class="relative">
                            <input
                                type="number"
                                id="frequence"
                                v-model="frequenceParJour"
                                class="w-full border bg-white rounded-lg p-2 px-4"
                                placeholder="Nombre de Posts"
                                min="1"
                                max="10"
                            />
                            <button
                                class="bg-black text-white absolute right-0 top-0 bottom-0 py-2 px-3"
                                style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;"
                                disabled
                            >
                                par Jour
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center flex-wrap gap-2 mt-2">
                        <!-- Nom de Campagne -->
                        <div class="mb-2 flex-1">
                            <label for="nomCampagne" class="block text-md mb-2">Nom de Campagne <span class="text-red-500">*</span> :</label>
                            <input
                                type="text"
                                id="nomCampagne"
                                v-model="nomCampagne"
                                placeholder="e.g: Campagne de Noël"
                                class="min-w-[300px] w-full h-[50px] border rounded-xl p-3 bg-white mb-1"
                            />
                        </div>
                        <!-- Couleur du Campagne -->
                        <div class="mb-2">
                            <label for="couleurCampagne" class="block text-md mb-2">Choisissez une couleur spéciale <span class="text-red-500">*</span> :</label>
                            <input
                                type="color"
                                id="couleurCampagne"
                                v-model="couleurCampagne"
                                class="min-w-[300px] max-w-[500px] h-[50px] border rounded-xl p-2 bg-white mb-1"
                            />
                        </div>
                    </div>

                    <!-- Description du Campagne -->
                    <div class="mb-4">
                        <label for="descriptionCampagne" class="block text-md mb-2">Description <span class="text-red-500">*</span> :</label>
                        <textarea
                            id="descriptionCampagne"
                            v-model="descriptionCampagne"
                            class="w-full border rounded-lg p-3 h-32 bg-white"
                            placeholder="Décrivez votre Campagne ici..."
                            min="10"
                            max="500"
                        ></textarea>
                        <p v-if="descriptionErrorMessage" class="text-red-500 text-sm mb-2">
                            {{ descriptionErrorMessage }}
                        </p>
                    </div>

                    <button
                        @click="generatePosts"
                        :disabled="!isFormValid"
                        class="bg-blue-500 text-white py-2 rounded-lg disabled:bg-gray-300 w-full"
                    >
                        Génerer les Posts
                    </button>
                </div>
            </div>

            <!-- Display the Generated Posts -->
            <div class="flex flex-col gap-2 min-w-[400px] items-start" v-if="currentStep === 2">
                <h3 class="text-lg">Les Posts de votre Campagne :</h3>
                <p v-if="submissionError" class="text-red-500 text-sm mb-2 w-full px-2 py-3 rounded-lg bg-red-200">
                    <i class="fa-solid fa-circle-exclamation mx-2 text-xl" style="color: red;"></i>
                    {{ submissionError }}
                </p>
                <p v-if="successMessage" class="text-white text-sm mb-2 px-2 py-3 w-full rounded-lg bg-green-500">
                    <i class="fa-solid fa-circle-check text-xl mx-2 text-green-200"></i>
                    {{ successMessage }}
                </p>
                <div class="flex flex-col w-full items-center gap-2 max-h-[400px] overflow-y-scroll">
                    <div 
                        v-for="post in postCards" 
                        class="py-4 px-3 w-full rounded-lg flex items-center cursor-pointer text-black"
                        :class="{
                            'bg-green-200 hover:bg-green-400': post.type === 'text',
                            'bg-yellow-200 hover:bg-yellow-400': post.type === 'image',
                            'bg-red-200 hover:bg-red-400': post.type === 'video',
                            'bg-purple-300 hover:bg-purple-400': post.type === 'article',
                            'bg-pink-400 hover:bg-pink-500': post.type === 'multiimage'
                        }"
                        :key="post.tempId"
                        @click="editCampaignPost(post)"
                    >
                        {{ getPostTypeIcon(post.type) }} {{ formatTime(post.scheduledDateTime) }}
                    </div>
                </div>
                <div class="flex items-center justify-between w-full">
                    <button 
                        class="bg-gray-300 rounded-lg px-3 py-2 mt-4"
                        @click="prevStep"
                    >
                        Précédent
                    </button>
                    <button
                        v-if="isSubmitting"
                        class="bg-white rounded-lg px-3 py-2 mt-4 loader"
                    >
                    </button>
                    <button
                        v-else
                        class="bg-blue-500 text-white rounded-lg px-3 py-2 mt-4"
                        :disabled="!areAllPostsValid"
                        @click="submitAllPosts"
                    >
                        Commencez la campagne
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import CampaignDetails from './CampaignDetails.vue';

export default {
    name: 'CampaignPortal',

    components: { CampaignDetails },
    props: {
        selectedAccount: { 
            type: Object, 
            required: true 
        },

        readMode: { 
            type: Boolean, 
            required: true 
        },

        selectedCampaign: { 
            type: Object, 
            required: false 
        },

        linkedinPosts: {
            type: Array,
            required: true
        }
    },

    emits: ['CampaignPostEditing', 'posts-generated', 'dates-updated', 'update:form-data', 'validate', 'close-campaign-portal'],

    data() {
        const today = new Date();
        const tomorrow = new Date();
        tomorrow.setDate(today.getDate() + 1);
        return {
            currentStep: 1,
            isSubmitting: false,
            submissionError: null,
            successMessage: null,
            todayDate: this.formatDateTime(today),
            startDate: this.formatDateTime(today),
            endDate: this.formatDateTime(tomorrow),
            selectedCible: '',
            frequenceParJour: 1,
            descriptionCampagne: '',
            cibleErrorMessage: '',
            descriptionErrorMessage: '',
            couleurCampagne: '#fff000',
            nomCampagne: '',
            errorMessage: '',
            postCards: [],
            cibles: [
                { id: '1', name: 'Audience 1' },
                { id: '2', name: 'Audience 2' },
                { id: '3', name: 'Audience 3' }
            ],
        };
    },


    computed: {
        minEndDate() {
            if (!this.startDate) return this.todayDate;
            const start = new Date(this.startDate);
            // start.setHours(start.getHours() + 2);
            start.setHours(start.getHours());
            return this.formatDateTime(start);
        },
        isStartDateValid() {
            if (!this.startDate) return false;
            const selectedStart = new Date(this.startDate);
            const now = new Date();
            const minStartTime = new Date(now);
            minStartTime.setHours(minStartTime.getHours());
            return selectedStart >= minStartTime;
        },
        isEndDateValid() {
            if (!this.endDate || !this.startDate) return false;
            const selectedStart = new Date(this.startDate);
            const selectedEnd = new Date(this.endDate);
            const minEndTime = new Date(selectedStart);
            // minEndTime.setHours(minEndTime.getHours() + 2);
            minEndTime.setHours(minEndTime.getHours());

            return selectedEnd >= minEndTime;
        },
        startDateErrorMessage() {
            if (!this.startDate) return "Date de début requise";
            if (!this.isStartDateValid) {
                const now = new Date();
                const minStartTime = new Date(now);
                minStartTime.setHours(minStartTime.getHours() + 1);
                this.errorMessage = `La date de début doit être au moins 1 heure après maintenant (${this.formatReadableDateTime(minStartTime)})`;
                return `La date de début doit être au moins 1 heure après maintenant (${this.formatReadableDateTime(minStartTime)})`;
            }
            return "";
        },
        endDateErrorMessage() {
            if (!this.endDate) return "Date de fin requise";
            if (!this.isEndDateValid) {
                const selectedStart = new Date(this.startDate);
                const minEndTime = new Date(selectedStart);
                minEndTime.setHours(minEndTime.getHours() + 2);
                this.errorMessage = `La date de fin doit être au moins 2 heures après la date de début (${this.formatReadableDateTime(minEndTime)})`;
                return `La date de fin doit être au moins 2 heures après la date de début (${this.formatReadableDateTime(minEndTime)})`;
            }
            return "";
        },
        isDescriptionValid() {
            if (this.descriptionCampagne.trim() === "") {
                this.descriptionErrorMessage = "La description ne peut pas être vide";
                return false;
            } else if (this.descriptionCampagne.length < 10) {
                this.descriptionErrorMessage = "La description doit contenir au moins 10 caractères";
                return false;
            } else if (this.descriptionCampagne.length > 500) {
                this.descriptionErrorMessage = "La description ne peut pas dépasser 500 caractères";
                return false;
            } else {
                this.descriptionErrorMessage = "";
                return true;
            }
        },
        isFormValid() {
            return (
                this.isStartDateValid &&
                this.isEndDateValid &&
                this.selectedCible &&
                this.couleurCampagne &&
                this.nomCampagne &&
                this.isDescriptionValid &&
                this.frequenceParJour >= 1 &&
                this.frequenceParJour <= 10
            );
        },
        formData() {
            return {
                startDate: this.startDate,
                endDate: this.endDate,
                selectedCible: this.selectedCible,
                frequenceParJour: this.frequenceParJour,
                descriptionCampagne: this.descriptionCampagne,
                couleurCampagne: this.couleurCampagne,
                nomCampagne: this.nomCampagne,
            };
        },
        areAllPostsValid() {
            return this.postCards.every((post) => {
                if (!post.scheduledDateTime) return false;
                const postDateTime = new Date(post.scheduledDateTime);
                const startDateTime = new Date(this.startDate);
                const endDateTime = new Date(this.endDate);
                if (postDateTime < startDateTime || postDateTime > endDateTime) return false;
                switch (post.type) {
                    case "text": return post.content.text.trim() !== "";
                    case "image":
                    case "video": return !!post.content.file;
                    case "article": return !!post.content.url;
                    case "multiimage": return true;
                    default: return false;
                }
            });
        },
    },


    watch: {
        startDate(newVal) {
            if (newVal) {
                const suggestedEnd = new Date(newVal);
                suggestedEnd.setHours(suggestedEnd.getHours() + 2);
                if (!this.endDate || new Date(this.endDate) < suggestedEnd) {
                    this.endDate = this.formatDateTime(suggestedEnd);
                }
            }
        },
        formData: {
            deep: true,
            handler(newVal) {
                this.$emit('update:form-data', newVal);
                this.$emit('validate', this.isFormValid);
            },
        },
    },
    methods: {
        prevStep() {
            if (this.currentStep > 1) this.currentStep--;
        },

        getPostTypeIcon(type) {
            switch (type) {
                case 'text': return '📝';
                case 'image': return '🖼️';
                case 'video': return '🎬';
                case 'article': return '📰';
                case 'multiimage': return '📷'
                default: return '📌';
            }
        },

        formatDateTime(date) {
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${date.toISOString().split('T')[0]}T${hours}:${minutes}`;
        },

        formatReadableDateTime(date) {
            const options = { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' };
            return date.toLocaleDateString('fr-FR', options).replace(',', ' à');
        },

        generatePosts() {
            if (this.isFormValid) {
                const start = new Date(this.startDate);
                const end = new Date(this.endDate);
                
                let campaignStartDateTime = `${this.startDate}`;
                let campaignEndDateTime = `${this.endDate}`;

                if (end < start) {
                    console.error("End date is before start date");
                    return;
                }
                const startDay = new Date(start);
                startDay.setHours(0, 0, 0, 0);

                const endDay = new Date(end);
                endDay.setHours(0, 0, 0, 0);

                const diffTime = Math.abs(endDay - startDay);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                const daysToProcess = diffDays + 1;

                this.postCards = [];

                for (let i = 0; i < daysToProcess; i++) {
                    const currentDate = new Date(start);

                    currentDate.setDate(start.getDate() + i);
                    currentDate.setHours(0, 0, 0, 0);

                    if (currentDate > endDay) continue;
                    let dayStart = (i === 0) ? new Date(start) : new Date(currentDate);
                    let dayEnd = (currentDate.getTime() === endDay.getTime()) ? new Date(end) : new Date(currentDate).setHours(23, 59, 59, 999);
                    dayEnd = new Date(dayEnd);
                    if (dayEnd <= dayStart) continue;
                    const durationMs = dayEnd - dayStart;
                    const intervalMs = durationMs / Math.max(this.frequenceParJour, 1);
                    for (let j = 0; j < this.frequenceParJour; j++) {
                        let postDateTime = (i === 0 && j === 0) ? new Date(start) : new Date(dayStart.getTime() + j * intervalMs);
                        if (postDateTime > end) continue;
                        this.postCards.push({
                            tempId: `post-${i}-${j}-${Date.now()}`,
                            scheduledDateTime: this.toLocalISOString(postDateTime),
                            type: "text",
                            content: { text: "", file: null, caption: "", url: "", title: "", description: "" },
                            accountId: this.selectedAccount.id,
                        });
                    }
                }
                this.$emit('posts-generated', this.postCards);
                this.currentStep = 2;
            } else {
                this.errorMessage = "Veuillez corriger les erreurs dans le formulaire.";
            }
        },

        editCampaignPost(post) {
            this.$emit('CampaignPostEditing', { ...post });
        },

        updatePost(updatedPost) {
            const index = this.postCards.findIndex(p => p.tempId === updatedPost.tempId);
            if (index !== -1) {
                this.postCards.splice(index, 1, { ...updatedPost });
            } else {
                console.error("Post not found in postCards");
            }
        },

        toLocalISOString(date) {
            const pad = (num) => String(num).padStart(2, "0");
            const year = date.getFullYear();
            const month = pad(date.getMonth() + 1);
            const day = pad(date.getDate());
            const hours = pad(date.getHours());
            const minutes = pad(date.getMinutes());
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        },

        formatTime(dateTime) {
            return new Date(dateTime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },

        updateDates() {
            const now = new Date();
            this.todayDate = this.formatDateTime(now);
            if (this.startDate && (!this.endDate || new Date(this.endDate) < new Date(this.startDate))) {
                const suggestedStart = new Date(this.startDate);
                const suggestedEnd = new Date(suggestedStart);
                suggestedEnd.setHours(suggestedEnd.getHours() + 2);
                this.endDate = this.formatDateTime(suggestedEnd);
            }
            this.$emit('dates-updated', { startDate: this.startDate, endDate: this.endDate });
        },

        async submitAllPosts() {
            this.isSubmitting = true;
            this.submissionError = null;
            try {
                if (!this.isTokenValid()) {
                    this.submissionError = "Votre jeton d'accès LinkedIn a expiré. Veuillez <a href='/linkedin/reconnect'>reconnecter votre compte</a>.";
                    this.isSubmitting = false;
                    return;
                }
                console.log(this.selectedAccount.id)
                const sortedPosts = [...this.postCards].sort((a, b) => new Date(a.scheduledDateTime) - new Date(b.scheduledDateTime));
                const campaignFormData = new FormData();
                campaignFormData.append("linkedin_id", this.selectedAccount.id);
                campaignFormData.append("name", this.nomCampagne);
                campaignFormData.append("description", this.descriptionCampagne || '');
                campaignFormData.append("target_audience", this.selectedCible);
                campaignFormData.append("frequency_per_day", this.frequenceParJour);
                campaignFormData.append("couleur", this.couleurCampagne);
                campaignFormData.append("start_date", this.startDate);
                campaignFormData.append("end_date", this.endDate);
                const campaignResponse = await axios.post("/linkedin/create-campaign", campaignFormData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    },
                });
                if (campaignResponse.data.status !== 201) throw new Error("Échec de la création de la campagne");
                
                const campaignId = campaignResponse.data.id;
                for (const post of sortedPosts) {
                    let formData = new FormData();
                    formData.append("linkedin_id", this.selectedAccount.id);
                    formData.append("type", post.type);
                    formData.append("scheduled_date", post.scheduledDateTime);
                    formData.append("campaign_id", campaignId);
                    switch (post.type) {
                        case "text": formData.append("content[text]", post.content.text.trim()); break;
                        case "image":
                        case "video":
                            formData.append("content[file]", post.content.file);
                            formData.append("content[caption]", post.content.caption.trim());
                            formData.append("content[original_filename]", post.content.file.name);
                            break;
                        
                        case 'multiimage':
                            post.content.files.forEach((file, index) => {
                                formData.append(`content[files][${index}]`, file);
                                formData.append(`content[original_filenames][${index}]`, file.name);
                            });
                            formData.append("content[caption]", post.content.caption.trim());
                            break;

                        case "article":
                            formData.append("content[url]", post.content.url);
                            formData.append("content[title]", post.content.title);
                            formData.append("content[description]", post.content.description);
                            formData.append("content[caption]", post.content.caption.trim());
                            break;
                        default: throw new Error("Type de publication invalide");
                    }
                    await axios.post("/linkedin/schedule-post", formData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        },
                    });
                }
                this.successMessage = "Tous vos posts ont été programmés avec succès!";
                setTimeout(() => { window.location = "/dashboard/linkedin"; }, 2000);
            } catch (error) {
                console.error("Error submitting posts:", error);
                if (error.response?.status === 401 || 
                    (error.response?.data?.error && error.response?.data?.error.toLowerCase().includes("token")) ||
                    (error.message && error.message.toLowerCase().includes("token"))) {
                    this.submissionError = "Votre jeton d'accès LinkedIn a expiré. Veuillez <a href='/linkedin/reconnect'>reconnecter votre compte</a>.";
                } else if (error.response?.data?.error) {
                    this.submissionError = error.response.data.error;
                } else if (error.response?.data?.message) {
                    this.submissionError = error.response.data.message;
                } else if (error.message) {
                    this.submissionError = error.message;
                } else {
                    this.submissionError = "Une erreur s'est produite lors de la publication des posts";
                }
            } finally {
                this.isSubmitting = false;
            }
        },
        
        isTokenValid() {
            if (!this.selectedAccount) return false;
            const tokenExpirationDate = new Date(this.selectedAccount.expire_at);
            const now = new Date();
            return tokenExpirationDate > now;
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            const daysOfWeek = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
            const dayOfWeek = daysOfWeek[date.getDay()];
            const dayOfMonth = date.getDate();
            const year = date.getFullYear();
            let hours = date.getHours();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${dayOfWeek} ${dayOfMonth}, ${year} à ${hours}:${minutes}${ampm}`;
        },

        
        handleClose() {
            this.$emit('close-campaign-portal');
        },
    },
};
</script>

<style scoped>
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: white;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, rgb(255 16 185) 0%, rgb(255 125 82) 100%);
}

.loader {
    border: 5px solid rgba(128, 128, 128, 0.411);
    border-radius: 50%;
    border-top: 5px solid gray;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>