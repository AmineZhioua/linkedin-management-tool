<template>
    <div class="bg-black bg-opacity-50 inset-0 h-[100vh] w-full absolute z-10"></div>
    <div class="flex items-center w-full p-4 justify-center gap-2 absolute top-[50%] left-[50%] translate-y-[-50%] translate-x-[-50%] z-50">
        <div class="bg-white p-4 rounded-md">
            <div class="flex flex-col gap-4" v-if="currentStep === 1">
                <div class="flex items-center justify-between mb-2">
                    <h3 v-if="!readMode" class="text-black text-lg mb-0">
                        {{ editMode ? 'Modifier votre Campagne' : 'Commencez votre Campagne personnalisé' }} :
                    </h3>
                    <h3 v-else class="text-black text-lg mb-0">Détails de votre Campagne :</h3>
                    <button @click="handleClose">
                        <img src="/build/assets/icons/close.svg" alt="Close Icon" height="20" width="20" />
                    </button>
                </div>

                <!-- Read Mode: Campaign Details -->
                <div v-if="readMode">
                    <CampaignDetails 
                        :campaign="selectedCampaign" 
                        :linkedin-account="selectedAccount" 
                        :posts="linkedinPosts" 
                        :cibles="cibles" 
                    />
                </div>

                <!-- Create/Edit Mode: Campaign Form -->
                <div v-else class="flex flex-col gap-3 relative">
                    <!-- Loading Screen While Generating Content -->
                    <div v-if="isGenerating" class="absolute inset-0 w-full h-full bg-white z-50 flex items-center justify-center">
                        <div class="ai-loader">
                            <label>Chargement du contenu...</label>
                            <div class="loading"></div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between gap-2">
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
                            <p v-if="startDateErrorMessage" class="text-red-500 text-sm mb-2">{{ startDateErrorMessage }}</p>
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
                            <p v-if="endDateErrorMessage" class="text-red-500 text-sm mb-2">{{ endDateErrorMessage }}</p>
                        </div>
                    </div>

                    <!-- Audience Cible -->
                    <div>
                        <label for="cible" class="block text-md mb-2">Choisissez votre Audience cible <span class="text-red-500">*</span> :</label>
                        <select
                            id="cible"
                            v-model="selectedCible"
                            :class="{'border-red-500': cibleErrorMessage}"
                            class="w-full border rounded-lg bg-gray-200 p-2 mb-1"
                        >
                            <option value="" disabled>Choisissez votre audience</option>
                            <option v-for="cible in cibles" :key="cible.id" :value="cible.id">{{ cible.name }}</option>
                        </select>
                        <input
                            v-if="selectedCible === 'Autre'"
                            type="text"
                            placeholder="Spécifier votre audience cible..."
                            class="p-2 rounded-lg border bg-white w-full my-2"
                            v-model="customCible"
                        />
                        <p v-if="cibleErrorMessage" class="text-red-500 text-sm mb-2">{{ cibleErrorMessage }}</p>
                    </div>

                    <!-- Frequence des Posts -->
                    <div v-if="editMode">
                        <label class="block fw-semibold text-md mb-2">Fréquence des Posts :</label>
                        <div class="relative">
                            <div class="w-full border bg-white rounded-lg p-2 px-4">{{ frequenceParJour }}</div>
                            <button 
                                class="bg-black text-white absolute right-0 top-0 bottom-0 py-2 px-3" 
                                style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;" disabled>par Jour</button>
                        </div>
                    </div>
                    <div v-else>
                        <label for="frequence" class="block text-md mb-2">Fréquence des Posts <span class="text-red-500">*</span> :</label>
                        <div class="relative">
                            <input
                                type="number"
                                id="frequence"
                                v-model="frequenceParJour"
                                class="w-full border bg-white rounded-lg p-2 px-4"
                                placeholder="Nombre des posts pour publier par jour"
                                min="1"
                                max="10"
                            />
                            <button 
                                class="bg-black text-white absolute right-0 top-0 bottom-0 py-2 px-3" 
                                style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;" disabled
                            >
                                par Jour
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mt-2">
                        <!-- Nom de Campagne -->
                        <div class="mb-2 flex-1">
                            <label for="nomCampagne" class="block text-md mb-2">Nom de Campagne <span class="text-red-500">*</span> :</label>
                            <input
                                type="text"
                                id="nomCampagne"
                                v-model="nomCampagne"
                                :class="{'border-red-500': nomCampagneErrorMessage}"
                                placeholder="e.g: Campagne de Noël"
                                class="min-w-[300px] w-full h-[50px] border rounded-xl p-3 bg-white mb-1"
                            />
                            <p v-if="nomCampagneErrorMessage" class="text-red-500 text-sm mb-2">{{ nomCampagneErrorMessage }}</p>
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
                    <div v-if="editMode">
                        <!-- Description cannot be edited -->
                        <label class="block fw-semibold text-md mb-2">Description du Campagne :</label>
                        <ScrollPanel style="max-width: 700px; height: 200px; border: 1px solid #eee;" class="p-2 rounded-lg">
                            <p>{{ descriptionCampagne }}</p>
                        </ScrollPanel>
                    </div>
                    <div v-else class="mb-4">
                        <label for="descriptionCampagne" class="block text-md mb-2">Description <span class="text-red-500">*</span> :</label>
                        <textarea
                            id="descriptionCampagne"
                            v-model="descriptionCampagne"
                            :class="{'border-red-500': descriptionErrorMessage}"
                            class="w-full border rounded-lg p-3 h-32 bg-white"
                            placeholder="Décrivez votre Campagne ici..."
                        ></textarea>
                        <p v-if="descriptionErrorMessage" class="text-red-500 text-sm mb-2">{{ descriptionErrorMessage }}</p>
                    </div>

                    <!-- Edit mode Button -->
                    <button
                        v-if="editMode"
                        @click="reschedulePosts"
                        :disabled="!isFormValid"
                        class="bg-blue-500 text-white py-2 rounded-lg disabled:bg-gray-300 w-full"
                    >
                        Reprogrammmer les Posts
                    </button>
                    <!-- Generate Posts Button -->
                    <button
                        v-else
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
                <div class="flex flex-col w-full items-center gap-2 max-h-[400px] overflow-y-scroll">
                    <div 
                        v-for="(post, index) in postCards" 
                        class="py-4 px-3 w-full h-full rounded-lg flex items-center cursor-pointer"
                        style="background-color: #181818;"
                        :key="post.tempId"
                        
                    >
                        <div class="w-full h-full" @click="editCampaignPost(post)">
                            <div class="fw-semibold mb-2 px-3 py-1 bg-white rounded-full w-fit text-black">
                                {{ `Post ${index} :` }}
                            </div>
                            <div class="text-md mb-0 text-white">
                                <span class="text-xl">
                                    {{ getPostTypeIcon(post.type) }}
                                </span> {{ formatDateWithMonth(post.scheduledDateTime) }}
                            </div>
                        </div>
                        <button 
                            class="p-2 bg-red-500 rounded-full" 
                            @click="removePostFromGeneratedPosts(index)"
                        >
                            <img src="/build/assets/icons/close-white.svg" alt="Close icon" height="15" width="15" />
                        </button>
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
                        v-if="!isCampaignSuccess"
                        class="bg-blue-500 text-white rounded-lg px-3 py-2 mt-4"
                        :disabled="!areAllPostsValid"
                        @click="submitAllPosts"
                    >
                        {{ editMode ? 'Mettre à jour la campagne' : 'Commencez la campagne' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import CampaignDetails from './CampaignDetails.vue';
import { toLocalISOString, formatDate, formatDateTime, formatReadableDateTime, formatTime } from '../services/dateService';
import { formatDateWithMonth } from '../services/datatables';
import { useToast } from "vue-toastification";

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
        editMode: {
            type: Boolean,
            required: true,
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

    setup() {
        const toast = useToast();
        return { toast };
    },

    emits: ['campaign-post-editing', 'posts-generated', 'dates-updated', 'update:form-data', 'validate', 'close-campaign-portal'],

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
            customCible: '',
            frequenceParJour: 1,
            descriptionCampagne: '',
            cibleErrorMessage: '',
            couleurCampagne: '#fff000',
            nomCampagne: '',
            errorMessage: '',
            postCards: [],
            cibles: [
                { id: 'Chercheurs d\'emploi', name: 'Chercheurs d\'emploi' },
                { id: 'Responsables RH', name: 'Responsables RH' },
                { id: 'Chefs d\'entreprise / Fondateurs', name: 'Chefs d\'entreprise / Fondateurs' },
                { id: 'Professionnels du marketing', name: 'Professionnels du marketing' },
                { id: 'Startups', name: 'Startups' },
                { id: 'Consultants', name: 'Consultants' },
                { id: 'Créateurs de contenu', name: 'Créateurs de contenu' },
                { id: 'Associations', name: 'Associations' },
                { id: 'Autre', name: 'Autre' },
            ],
            isGenerating: false,
            isCampaignSuccess: false,
        };
    },

    computed: {
        minEndDate() {
            if (!this.startDate) return this.todayDate;
            const start = new Date(this.startDate);
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
        nomCampagneErrorMessage() {
            return this.nomCampagne ? "" : "Veuillez entrer un nom pour la campagne";
        },
        descriptionErrorMessage() {
            if (this.descriptionCampagne.trim() === "") return "La description ne peut pas être vide";
            if (this.descriptionCampagne.length < 10) return "La description doit contenir au moins 10 caractères";
            if (this.descriptionCampagne.length > 3000) return "La description ne peut pas dépasser 500 caractères";
            return "";
        },
        isDescriptionValid() {
            return !this.descriptionErrorMessage;
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
                    case "text": return post.content.caption.trim() !== "";
                    case "image":
                    case "video": return !!post.content.file || !!post.content.file_path;
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

    mounted() {
        if (this.editMode && this.selectedCampaign) {
            this.loadCampaignData();
        }
    },

    methods: {
        toLocalISOString,
        formatDate,
        formatDateTime,
        formatReadableDateTime,
        formatTime,
        formatDateWithMonth,

        loadCampaignData() {
            if (this.editMode && this.selectedCampaign) {
                this.startDate = this.formatDateTime(new Date(this.selectedCampaign.start_date));
                this.endDate = this.formatDateTime(new Date(this.selectedCampaign.end_date));
                this.selectedCible = this.selectedCampaign.target_audience;
                if (this.selectedCible === 'Autre') {
                    this.customCible = this.selectedCampaign.target_audience || '';
                }
                this.frequenceParJour = this.selectedCampaign.frequency_per_day;
                this.descriptionCampagne = this.selectedCampaign.description;
                this.couleurCampagne = this.selectedCampaign.color;
                this.nomCampagne = this.selectedCampaign.name;

                // Load existing posts with deep copy of content
                this.postCards = this.linkedinPosts.filter(post => post.campaign_id === this.selectedCampaign.id)
                    .map(post => {
                        const content = typeof post.content === 'string' ? JSON.parse(post.content) : post.content;
                        return {
                            id: post.id,
                            job_id: post.job_id,
                            isExisting: true,
                            scheduledDateTime: this.formatDateTime(new Date(post.scheduled_time)),
                            type: post.type,
                            tempId: post.tempId || `temp-${Date.now()}-${post.id}`,
                            content: {
                                text: content.text || '',
                                caption: content.caption || '',
                                url: content.url || '',
                                title: content.title || '',
                                description: content.description || '',
                                file_path: content.file_path || '', // For single image/video
                                file_paths: Array.isArray(content.file_paths) ? [...content.file_paths] : [], // Changed from files to file_paths
                                original_filenames: Array.isArray(content.original_filenames) ? [...content.original_filenames] : [],
                                file: null, // For new file uploads
                                fileName: content.original_filename || null, // For single image/video
                            },
                            accountId: this.selectedAccount.id,
                        };
                    });
                console.log(this.postCards);
            }
        },

        prevStep() {
            if (this.currentStep > 1) this.currentStep--;
        },

        getPostTypeIcon(type) {
            switch (type) {
                case 'text': return '📝';
                case 'image': return '🖼️';
                case 'video': return '🎬';
                case 'article': return '📰';
                case 'multiimage': return '📷';
                default: return '📌';
            }
        },

        async generatePosts() {
            if (this.isFormValid) {
                const start = new Date(this.startDate);
                const end = new Date(this.endDate);
                if (end < start) {
                    this.showErrorToast("Vérifier les dates tout d'abord");
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
                        const post = {
                            tempId: `post-${i}-${j}-${Date.now()}`,
                            isExisting: false,
                            scheduledDateTime: this.toLocalISOString(postDateTime),
                            type: "text",
                            content: { text: "", file: null, caption: "", url: "", title: "", description: "" },
                            accountId: this.selectedAccount.id,
                        };
                        this.postCards.push(post);
                    }
                }

                // Generate captions using OpenAI API
                const captionPromises = this.postCards.map(async (post) => {
                    const targetAudience = this.selectedCible === 'Autre' 
                        ? this.customCible 
                        : this.cibles.find(c => c.id === this.selectedCible).name;
                    const prompt = `Generate a creative and engaging LinkedIn post caption targeting ${targetAudience} based on this campaign description: ${this.descriptionCampagne}`;
                    const openaiKey = import.meta.env.VITE_OPENAI_API_KEY;
                    this.isGenerating = true;

                    try {
                        const response = await axios.post('https://api.openai.com/v1/chat/completions', {
                            model: 'gpt-3.5-turbo',
                            messages: [
                                { role: 'system', content: 'You are a skilled copywriter specializing in LinkedIn content.' },
                                { role: 'user', content: prompt }
                            ],
                            max_tokens: 150,
                        }, {
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${openaiKey}`
                            }
                        });

                        if (response.status === 200) {
                            post.content.caption = response.data.choices[0].message.content.trim();
                        } else {
                            post.content.caption = "Failed to generate caption";
                        }
                    } catch (error) {
                        console.error('Error generating caption:', error);
                        post.content.text = "Erreur lors de la génération de contenu !";
                    } finally {
                        this.isGenerating = false;
                    }
                });

                await Promise.all(captionPromises);
                this.$emit('posts-generated', this.postCards);
                this.currentStep = 2;
            } else {
                const errors = [];
                if (!this.isStartDateValid) errors.push(this.startDateErrorMessage);
                if (!this.isEndDateValid) errors.push(this.endDateErrorMessage);
                if (!this.selectedCible) errors.push("Veuillez sélectionner une audience cible");
                if (!this.nomCampagne) errors.push("Veuillez entrer un nom pour la campagne");
                if (!this.isDescriptionValid) errors.push(this.descriptionErrorMessage);
                if (this.frequenceParJour < 1 || this.frequenceParJour > 10) errors.push("La fréquence doit être entre 1 et 10");

                this.$toast.error(errors.join('\n'), {
                    timeout: 5000,
                    closeOnClick: true,
                });
            }
        },

        reschedulePosts() {
            if (!this.isFormValid) {
                this.showErrorToast("Veuillez vérifier les informations du formulaire.");
                return;
            }

            const start = new Date(this.startDate);
            const end = new Date(this.endDate);
            if (end <= start) {
                this.showErrorToast("La date de fin doit être après la date de début.");
                return;
            }

            const totalPosts = this.postCards.length;
            if (totalPosts === 0) {
                this.showErrorToast("Aucun post à reprogrammer.");
                return;
            }

            if (totalPosts === 1) {
                this.postCards[0].scheduledDateTime = this.toLocalISOString(start);
            } else {
                const durationMs = end - start;
                const intervalMs = durationMs / (totalPosts - 1);
                for (let k = 0; k < totalPosts; k++) {
                    const postTime = new Date(start.getTime() + k * intervalMs);
                    this.postCards[k].scheduledDateTime = this.toLocalISOString(postTime);
                }
            }

            this.$emit('posts-generated', this.postCards);
            this.currentStep = 2;
        },

        removePostFromGeneratedPosts(postIndex) {
            this.postCards.splice(postIndex, 1);
        },

        editCampaignPost(post) {
            this.$emit('campaign-post-editing', { ...post });
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

                let campaignId;
                if (this.editMode) {
                    campaignFormData.append("campaign_id", this.selectedCampaign.id);
                    campaignFormData.append('_method', 'PUT');

                    const updateResponse = await axios.post(`/linkedin/update-campaign`, campaignFormData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        },
                    });
                    if (updateResponse.data.status !== 200) {
                        this.showErrorToast("Erreur lors de la mise à jour du campagne !");
                        throw new Error("Échec de la mise à jour de la campagne");
                    }
                    campaignId = this.selectedCampaign.id;
                } else {
                    const response = await axios.post("/linkedin/create-campaign", campaignFormData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        },
                    });
                    if (response.data.status !== 201) {
                        this.showErrorToast("Erreur lors de la création du campagne !");
                        throw new Error("Échec de la création de la campagne");
                    }
                    campaignId = response.data.id;
                }

                for (const post of sortedPosts) {
                    let formData = new FormData();
                    formData.append("linkedin_id", this.selectedAccount.id);
                    formData.append("type", post.type);
                    formData.append("scheduled_date", post.scheduledDateTime);
                    formData.append("campaign_id", campaignId);

                    if (this.editMode) {
                        formData.append("job_id", post.job_id);
                        formData.append("post_id", post.id);
                        formData.append("_method", "PUT");
                        console.log(post)

                        let contentObj = {};
                        switch (post.type) {
                            case "text":
                                contentObj = {
                                    caption: post.content.caption.trim()
                                };
                                break;
                            case "image":
                            case "video":
                                contentObj = {
                                    caption: post.content.caption.trim(),
                                    file_path: post.content.file_path || null,
                                    original_filename: post.content.file?.name || post.content.original_filename || ''
                                };
                                if (post.content.file) {
                                    formData.append("file", post.content.file);
                                }
                                break;
                            case "multiimage":
                                console.log(post.content)
                                contentObj = {
                                    caption: post.content.caption.trim(),
                                    file_paths: [...(post.content.file_paths || [])], // Keep existing file paths
                                    original_filenames: [...(post.content.original_filenames || [])] // Keep existing filenames
                                };
                                
                                // Handle new files being added
                                if (post.content.files && post.content.files.length > 0) {
                                    post.content.files.forEach((file, index) => {
                                        formData.append(`files[${index}]`, file);
                                    });
                                    
                                    const newFilenames = post.content.files.map(file => file.name);
                                    contentObj.original_filenames = [...contentObj.original_filenames, ...newFilenames];
                                }
                                
                                console.log(contentObj)
                                break;
                            case "article":
                                contentObj = {
                                    url: post.content.url || '',
                                    title: post.content.title || '',
                                    description: post.content.description || '',
                                    caption: post.content.caption.trim()
                                };
                                break;
                            default:
                                throw new Error("Type de publication invalide");
                        }
                        formData.append("content", JSON.stringify(contentObj));
                    } else {
                        switch (post.type) {
                            case "text":
                                formData.append("content[caption]", post.content.caption.trim());
                                break;
                            case "image":
                            case "video":
                                if (post.content.file) {
                                    formData.append("content[file]", post.content.file);
                                }
                                formData.append("content[caption]", post.content.caption.trim());
                                formData.append("content[original_filename]", post.content.file?.name || '');
                                break;
                            case "multiimage":
                                post.content.files?.forEach((file, index) => {
                                    formData.append(`content[files][${index}]`, file);
                                    formData.append(`content[original_filenames][${index}]`, file.name);
                                });
                                formData.append("content[caption]", post.content.caption.trim());
                                break;
                            case "article":
                                formData.append("content[url]", post.content.url || '');
                                formData.append("content[title]", post.content.title || '');
                                formData.append("content[description]", post.content.description || '');
                                formData.append("content[caption]", post.content.caption.trim());
                                break;
                            default:
                                throw new Error("Type de publication invalide");
                        }
                    }

                    if (this.editMode) {
                        await axios.post(`/linkedin/update-post`, formData, {
                            headers: {
                                "Content-Type": "multipart/form-data",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                            },
                        });
                    } else {
                        await axios.post("/linkedin/schedule-post", formData, {
                            headers: {
                                "Content-Type": "multipart/form-data",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                            },
                        });
                    }
                }
                this.toast.success('Votre campagne a été sauvegardée avec succès !');
                this.isCampaignSuccess = true;
                setTimeout(() => { 
                    window.location.reload();
                    this.isCampaignSuccess = false;
                }, 2000);
            } catch (error) {
                console.error("Error submitting posts:", error);
                if (error.response?.status === 401 || 
                    (error.response?.data?.error && error.response?.data?.error.toLowerCase().includes("token")) ||
                    (error.message && error.message.toLowerCase().includes("token"))) {
                    this.submissionError = "Votre jeton d'accès LinkedIn a expiré. Veuillez <a href='/linkedin/reconnect'>reconnecter votre compte</a>.";
                } else if (error.response?.data?.errors) {
                    this.submissionError = Object.values(error.response.data.errors).flat().join(' ');
                } else if (error.response?.data?.message) {
                    this.submissionError = error.response.data.message;
                } else if (error.message) {
                    this.submissionError = error.message;
                } else {
                    this.submissionError = "Une erreur s'est produite lors de la publication des posts";
                }
                this.showErrorToast(this.submissionError);
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

        handleClose() {
            this.$emit('close-campaign-portal');
        },

        updatePost(updatedPost) {
            const index = this.postCards.findIndex(p => p.tempId === updatedPost.tempId);
            if (index !== -1) {
                this.postCards.splice(index, 1, { ...updatedPost });
            } else {
                console.error("Post not found in postCards");
            }
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

.ai-loader {
  width: 350px;
  height: 180px;
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-evenly;
  padding: 30px;
}
.loading {
  width: 100%;
  height: 10px;
  background: lightgrey;
  border-radius: 10px;
  position: relative;
}
.loading::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 50%;
  height: 10px;
  background: #002;
  border-radius: 10px;
  z-index: 1;
  animation: loading 0.6s alternate infinite;
}
.ai-loader label {
  color: #002;
  font-size: 18px;
  animation: bit 0.6s alternate infinite;
}

@keyframes bit {
  from {
    opacity: 0.3;
  }
  to {
    opacity: 1;
  }
}

@keyframes loading {
  0% {
    left: 25%;
  }
  100% {
    left: 50%;
  }
  0% {
    left: 0%;
  }
}

::-webkit-scrollbar {
    width: 6px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
.no-scroll {
    overflow: hidden;
}
::-webkit-scrollbar-thumb {
    background: transparent;
    border-radius: 1px;
}
</style>