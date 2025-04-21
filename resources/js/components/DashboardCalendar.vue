<template>
    <div class="dashboard-calendar py-10">
        <calendar-navigation v-model:currentMonth="currentMonth" v-model:currentYear="currentYear" />
        <loading-overlay :isLoading="isLoading" message="Traitement en cours..." />
        <!-- Day Headers -->
        <div class="grid grid-cols-7 text-center font-medium mb-2">
            <div class="py-2">Dim</div>
            <div class="py-2">Lun</div>
            <div class="py-2">Mar</div>
            <div class="py-2">Mer</div>
            <div class="py-2">Jeu</div>
            <div class="py-2">Ven</div>
            <div class="py-2">Sam</div>
        </div>
    
        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 gap-1">
            <!-- Blank days before the first day of the month -->
            <template v-for="blankDay in Array.from({ length: getFirstDayOfMonth(currentMonth, currentYear) }, (_, i) => i)" :key="'blank-' + blankDay">
                <div class="h-24 bg-gray-100 rounded"></div>
            </template>
    
            <!-- Days of the month -->
            <template v-for="day in getDaysInMonth(currentMonth, currentYear)" :key="day">
                <div 
                    class="h-24 border rounded overflow-hidden relative p-1"
                    :class="{
                    'bg-blue-500 cursor-pointer': displayCampaigns(day),
                    'cursor-not-allowed': !displayCampaigns(day),
                    }"
                    @click="getPostsForDate(day).length > 0 ? showPostsPopover(day) : null"
                >
                    <div class="text-right text-sm">{{ day }}</div>

                    <!-- A Message to display if that Day doesn't have any Posts -->
                    <div 
                        v-if="getPostsForDate(day).length === 0 && isDayInCampaign(day)"
                    >
                        <p class="text-black fw-semibold text-center">Pas de posts !</p>
                    </div>

                    <!-- Posts for that Day -->
                    <div class="overflow-y-auto" style="max-height: 100px;">
                        <div 
                            v-for="post in getPostsForDate(day)"
                            class="text-xs p-1 mb-1 rounded truncate"
                            :key="post.id || post.tempId"
                            :class="{
                            'bg-green-200': post.type === 'text',
                            'bg-yellow-200': post.type === 'image',
                            'bg-red-200': post.type === 'video',
                            'bg-purple-300': post.type === 'article'
                            }"
                            @click="showPostsPopover(day)"
                        >
                            {{ getPostTypeIcon(post.type) }} {{ formatTime(post.scheduled_time) }}
                        </div>
                    </div>
                </div>
            </template>
        </div>
  
        <!-- Popover for viewing posts -->
        <div v-if="showPopover" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="closePopover">
            <div class="bg-white shadow-lg flex flex-col rounded-lg p-4 z-10 max-w-md w-full min-h-[400px]" @click.stop>
                <div class="flex justify-between items-center">
                    <h3 class="font-semibold text-lg">Posts for {{ selectedDay }} {{ getMonthName(currentMonth) }} {{ currentYear }}</h3>
                    <button 
                        class="text-black text-3xl"
                        @click="closePopover"
                    >
                        ×
                    </button>
                </div>
                <div class="mt-4 flex-1">
                    <div v-if="getPostsForDate(selectedDay).length === 0" class="text-center text-gray-500 py-4">
                        Pas de posts prévus
                    </div>
                    <div v-else class="space-y-2">
                        <div 
                            v-for="post in getPostsForDate(selectedDay)"
                            :key="post.id || post.tempId"
                            class="p-3 rounded flex items-center cursor-pointer justify-between"
                            :class="{
                            'bg-green-200': post.type === 'text',
                            'bg-yellow-200': post.type === 'image',
                            'bg-red-200': post.type === 'video',
                            'bg-purple-200': post.type === 'article'
                            }"
                            @click="openPost(post)"
                        >
                            <div class="flex items-center w-full">
                                <div class="flex-1"> 
                                    <span class="mr-2">{{ getPostTypeIcon(post.type) }}</span>
                                    <span>{{ formatTime(post.scheduled_time) }}</span>
                                </div>
                                <p 
                                    class="mb-0 px-4 py-1 rounded-full fw-semibold"
                                    :class="{
                                        'text-green-500 bg-green-100': post.status === 'posted',
                                        'text-red-600': post.status === 'failed',
                                        'text-blue-600 bg-blue-300': post.status === 'queued',
                                    }"
                                >
                                    {{ post.status === "posted" ? "Publié" : post.status === "failed" ? "Échoué" : "En attente" }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- POST MODAL POPOVER -->
        <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold mb-0">Détails du Post</h3>
                    <button 
                        @click="closeModal" 
                        class="text-black text-3xl"
                    >
                        ×
                    </button>
                </div>

                <div v-if="selectedPost" class="mb-4">
                    <!-- Editable Fields when in edit mode -->
                    <div v-if="isEditing" class="flex flex-col gap-4">
                        <div>
                            <label class="text-sm">Date et heure:</label>
                            <input
                                type="datetime-local"
                                v-model="localScheduledDateTime"
                                class="border rounded p-2 w-full"
                            />
                        </div>

                        <div>
                            <label class="text-sm">Type de Post:</label>
                            <select
                                v-model="editedPost.type"
                                class="border rounded p-2 w-full"
                            >
                                <option value="text">Texte</option>
                                <option value="image">Image</option>
                                <option value="video">Vidéo</option>
                                <option value="article">Article</option>
                            </select>
                        </div>

                        <!-- Content based on post type -->
                        <div v-if="editedPost.type === 'text'">
                            <textarea
                                v-model="editedPost.content.text"
                                placeholder="Exprimez-vous !"
                                class="w-full border rounded-lg p-2 h-32"
                            ></textarea>
                        </div>

                        <div v-if="editedPost.type === 'image' || editedPost.type === 'video'">
                            <input
                                type="file"
                                :accept="editedPost.type === 'image' ? 'image/*' : 'video/*'"
                                class="w-full border rounded-lg p-2 mb-2"
                                @change="handleFileUpload"
                            />
                            <p v-if="editedPost.content.file_path" class="mt-2 mb-0 text-sm text-gray-600">
                                Fichier actuel : {{ editedPost.content.file_path.split('/').pop() }}
                            </p>
                            <textarea
                                v-model="editedPost.content.caption"
                                placeholder="Souhaitez-vous ajouter quelques mots ? (Optionnel)"
                                class="w-full border rounded-lg p-2 h-32 mt-2"
                            ></textarea>
                        </div>

                        <div v-if="editedPost.type === 'article'" class="flex flex-col gap-2">
                            <label class="text-sm text-gray-600">URL de l'Article :</label>
                            <input
                                type="text"
                                class="w-full border rounded-lg p-2"
                                v-model="editedPost.content.url"
                            />
                            <label class="text-sm text-gray-600">Titre de l'Article :</label>
                            <input
                                type="text"
                                class="w-full border rounded-lg p-2"
                                v-model="editedPost.content.title"
                            />
                            <label class="text-sm text-gray-600">Description de l'Article :</label>
                            <input
                                type="text"
                                class="w-full border rounded-lg p-2"
                                v-model="editedPost.content.description"
                            />
                            <textarea
                                v-model="editedPost.content.caption"
                                placeholder="Souhaitez-vous ajouter quelques mots ? (Optionnel)"
                                class="w-full border rounded-lg p-2 h-32 mt-2"
                            ></textarea>
                        </div>
                    </div>

                    <!-- View Mode -->
                    <div v-else>
                        <p>
                            <strong>Date et heure :</strong> {{ formatDateTime(selectedPost.scheduledDateTime) }}
                        </p>
                        <p>
                            <strong>Type de Post :</strong> {{ selectedPost.type }}
                        </p>

                        <!-- Content based on post type -->
                        <div v-if="selectedPost.type === 'text'" class="mt-2">
                            <p>
                                <strong>Contenu :</strong> {{ selectedPost.content.text || 'Aucun contenu disponible' }}
                            </p>
                        </div>

                        <div v-if="selectedPost.type === 'image'" class="mt-2">
                            <img :src="getMediaUrl(selectedPost.content.file_path)" alt="Image" class="max-w-full h-auto mb-2">
                            <p v-if="selectedPost.content.caption">
                                <strong>Légende :</strong> {{ selectedPost.content.caption }}
                            </p>
                        </div>

                        <div v-if="selectedPost.type === 'video'" class="mt-2">
                            <video :src="getMediaUrl(selectedPost.content.file_path)" controls class="max-w-full h-auto mb-2"></video>
                            <p v-if="selectedPost.content.caption">
                                <strong>Légende :</strong> {{ selectedPost.content.caption }}
                            </p>
                        </div>

                        <div v-if="selectedPost.type === 'article'" class="mt-2 flex flex-col gap-2">
                            <p>
                                <strong>URL de l'Article :</strong> 
                                <a :href="selectedPost.content.url" target="_blank" class="text-blue-600 hover:underline">
                                    {{ selectedPost.content.url }}
                                </a>
                            </p>
                            <p>
                                <strong>Titre de l'Article :</strong> {{ selectedPost.content.title || 'Sans titre' }}
                            </p>
                            <p v-if="selectedPost.content.description">
                                <strong>Description de l'Article :</strong> {{ selectedPost.content.description }}
                            </p>
                            <p v-if="selectedPost.content.caption">
                                <strong>Légende :</strong> {{ selectedPost.content.caption }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button 
                        @click="closeModal" 
                        class="bg-gray-300 text-black py-2 px-4 rounded-lg"
                    >
                        Fermer
                    </button>
                    <div class="flex items-center gap-2">
                        <button 
                            @click="deletePost(selectedPost.id)"
                            :disabled="selectedPost.status === 'posted'" 
                            :class="{'opacity-50 cursor-not-allowed': selectedPost.status === 'posted'}"
                            class="bg-red-500 text-white py-2 px-4 rounded-lg"
                        >
                            Supprimer
                        </button>

                        <button 
                            v-if="!isEditing"
                            @click="startEditing"
                            :disabled="selectedPost.status === 'posted'" 
                            class="bg-blue-500 text-white py-2 px-4 rounded-lg"
                            :class="{'opacity-50 cursor-not-allowed': selectedPost.status === 'posted'}"
                        >
                            Modifier
                        </button>
                        <button 
                            v-else
                            @click="updatePost"
                            class="bg-green-500 text-white py-2 px-4 rounded-lg"
                        >
                            Enregistrer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
  
<script>
import axios from 'axios';

export default {
    name: 'DashboardCalendar',

    props: {
        campaigns: {
            type: Array,
            required: true
        },
        posts: {
            type: Array,
            required: true
        },
        initialMonth: {
            type: Number,
            required: true
        },
        initialYear: {
            type: Number,
            required: true
        }
    },
    
    data() {
        console.log("Posts:", this.posts);
        console.log("Campaigns:", this.campaigns);
        return {
            selectedDay: null,
            showPopover: false,
            currentMonth: this.initialMonth,
            currentYear: this.initialYear,
            selectedPost: null,
            editedPost: null,
            isOpen: false,
            isEditing: false,
            isLoading: false,
            localScheduledDateTime: '',
        };
    },
  
    methods: {
        displayCampaigns(day) {
            return this.campaigns.some(campaign => {
                const startDate = new Date(campaign.start_date);
                startDate.setHours(0, 0, 0, 0);
                const endDate = new Date(campaign.end_date);
                endDate.setHours(0, 0, 0, 0);
                const checkDate = new Date(this.currentYear, this.currentMonth, day);
                checkDate.setHours(0, 0, 0, 0);
                return checkDate >= startDate && checkDate <= endDate;
            });
        },

        isDayInCampaign(day) {
            const currentDate = new Date(this.currentYear, this.currentMonth, day);
            return this.campaigns.some(campaign => {
                const start = new Date(campaign.start_date);
                const end = new Date(campaign.end_date);
                return currentDate >= start && currentDate <= end;
            });
        },

        getDaysInMonth(month, year) {
            return new Date(year, month + 1, 0).getDate();
        },
        
        getFirstDayOfMonth(month, year) {
            return new Date(year, month, 1).getDay();
        },
        
        getPostsForDate(date) {
            return this.posts.filter(post => {
                const postDate = new Date(post.scheduled_time);
                return postDate.getDate() === date && 
                    postDate.getMonth() === this.currentMonth && 
                    postDate.getFullYear() === this.currentYear;
            });
        },
                
        formatTime(dateTime) {
            return new Date(dateTime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },
        
        formatDateTime(dateTime) {
            const date = new Date(dateTime);
            return date.toLocaleString('fr-FR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
                
        getPostTypeIcon(type) {
            switch(type) {
                case 'text': return '📝';
                case 'image': return '🖼️';
                case 'video': return '🎬';
                case 'article': return '📰';
                default: return '📌';
            }
        },
            
        getMediaUrl(filePath) {
            return `/linkedin/${filePath}`;
        },
            
        showPostsPopover(day) {
            this.selectedDay = day;
            this.showPopover = true;
        },
            
        closePopover() {
            this.showPopover = false;
        },
            
        getMonthName(monthIndex) {
            const months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            return months[monthIndex];
        },

        utcToLocalForInput(utcString) {
            const date = new Date(utcString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        },

        // localToUtc(localDateString) {
        //     const localDate = new Date(localDateString);
        //     return localDate.toISOString();
        // },

        openPost(post) {
            let postContent = post.content;
            if (typeof post.content === 'string') {
                try {
                    postContent = JSON.parse(post.content);
                } catch (e) {
                    console.error("Failed to parse post content:", e);
                    postContent = { text: post.content };
                }
            }
            this.selectedPost = {
                ...post,
                content: postContent,
                scheduledDateTime: post.scheduled_time // Keep as UTC
            };
            console.log("url:", this.getMediaUrl(postContent.file_path));
            this.editedPost = { ...this.selectedPost };
            this.localScheduledDateTime = this.utcToLocalForInput(post.scheduled_time); // Convert to local
            this.isOpen = true;
            this.isEditing = false;
        },
            
        closeModal() {
            this.isOpen = false;
            this.selectedPost = null;
            this.editedPost = null;
            this.isEditing = false;
        },

        startEditing() {
            this.isEditing = true;
        },

        handleFileUpload(event) {
            const file = event.target.files && event.target.files[0];
            if (file) {
                this.editedPost.content.file = file;
                this.editedPost.content.fileName = file.name;
            }
        },

        async updatePost() {
            this.isLoading = true;
            console.log("Updating post:", this.editedPost);
            console.log(this.editedPost.scheduledDateTime);
            console.log(this.localScheduledDateTime);
            try {
                this.editedPost.scheduledDateTime = this.localScheduledDateTime;

                const formData = new FormData();
                formData.append('post_id', this.editedPost.id);
                formData.append('linkedin_user_id', this.editedPost.linkedin_user_id);
                formData.append('type', this.editedPost.type);
                formData.append('scheduled_time', this.editedPost.scheduledDateTime);
                formData.append('content', JSON.stringify(this.editedPost.content));
                formData.append('_method', 'PUT');

                if (this.editedPost.content.file) {
                    formData.append('file', this.editedPost.content.file);
                }

                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }
                const response = await axios.post('/linkedin/update-post', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                });

                if (response.status === 200) {
                    this.isEditing = false;
                    this.selectedPost = { ...this.editedPost };
                    window.location.reload();
                } else {
                    console.error("Failed to update post:", response);
                    throw new Error("Failed to update post");
                }
            } catch (error) {
                console.error("Error updating post:", error);
                if (error.response && error.response.status === 422) {
                    console.log("Validation errors:", error.response.data);
                }
            } finally {
                this.isLoading = false;
            }
        },
        
        async deletePost(postId) {
            this.isLoading = true;
            try {
                const response = await axios.delete("/linkedin/delete-post", {
                    data: {
                        post_id: postId,
                        linkedin_user_id: this.selectedPost.linkedin_user_id,
                    },
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    },
                });

                if (response.status === 200) {
                    this.closeModal();
                    window.location.reload();
                } else {
                    console.error("Failed to delete post:", response);
                    throw new Error("Failed to delete post");
                }
            } catch (error) {
                console.error("Error deleting post:", error);
                this.isLoading = false;
            } finally {
                this.isLoading = false;
            }
        },
    },
}
</script>
  
<style scoped>
.dashboard-calendar {
    max-width: 100%;
}
</style>