<template>
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg">
            <div class="flex justify-between align-items-center mb-4">
                <h3 class="text-lg font-semibold mb-0">Modifier le Post</h3>
                <button 
                    @click="closeModal" 
                    class="text-gray-500 text-3xl"
                >
                    &times;
                </button>
            </div>

            <!-- Error Messages -->
            <div v-if="errorMessage" class="px-4 py-3 flex align-items-center bg-red-100 border-red-400 rounded mb-4">
                <i class="fa-solid fa-circle-exclamation mr-3 text-xl" style="color: red;"></i>
                <p class="text-red-500 text-sm mb-0">{{ errorMessage }}</p>
            </div>

            <div v-if="editedPost" class="mb-4">
                <div class="flex items-center gap-2 mb-4">
                    <label class="text-sm">Date et heure:</label>
                    <input
                        type="datetime-local"
                        v-model="editedPost.scheduledDateTime"
                        :min="campaignStartDateTime"
                        :max="campaignEndDateTime"
                        class="border rounded p-2"
                    />
                </div>

                <div class="flex items-center gap-2 mb-4">
                    <label class="text-sm">Type de Post:</label>
                    <select
                        v-model="editedPost.type"
                        @change="resetPostContent"
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
                <!-- Only Text Posts -->
                <div v-if="editedPost.type === 'text'">
                    <textarea
                        v-model="editedPost.content.text"
                        placeholder="Exprimez-vous !"
                        class="w-full border rounded-lg p-2 h-32 mb-4"
                    ></textarea>
                </div>

                <!-- Image or Video Post Input -->
                <div v-if="editedPost.type === 'image' || editedPost.type === 'video'">
                    <input
                        type="file"
                        @change="handleFileUpload"
                        :accept="editedPost.type === 'image' ? 'image/*' : 'video/*'"
                        class="w-full border rounded-lg p-2 mb-2"
                        ref="fileInput"
                    />
                    <p v-if="editedPost.content.file" class="mt-2 mb-0 text-sm text-gray-600">
                        Fichier sélectionné : {{ editedPost.content.fileName || (editedPost.content.file && editedPost.content.file.name) }}
                    </p>
                    <textarea
                        v-model="editedPost.content.caption"
                        placeholder="Ajouter une légende... (Optionnel)"
                        class="w-full border rounded-lg p-2 h-32 mt-2"
                    ></textarea>
                </div>

                <!-- Article Post Input -->
                <div v-if="editedPost.type === 'article'" class="flex flex-col gap-2">
                    <label class="text-sm text-gray-600">Article URL* :</label>
                    <input
                        type="text"
                        class="w-full border rounded-lg p-2"
                        placeholder="e.g: www.example.com"
                        v-model="editedPost.content.url"
                    />
                    <label class="text-sm text-gray-600">Article Title :</label>
                    <input
                        type="text"
                        class="w-full border rounded-lg p-2"
                        placeholder="Official LinkedIn Blog"
                        v-model="editedPost.content.title"
                    />
                    <label class="text-sm text-gray-600">Article Description :</label>
                    <input
                        type="text"
                        class="w-full border rounded-lg p-2"
                        placeholder="Official LinkedIn Blog - Your source for insights and information about LinkedIn."
                        v-model="editedPost.content.description"
                    />
                    <textarea
                        v-model="editedPost.content.caption"
                        placeholder="Ajouter un commentaire... (Optionnel)"
                        class="w-full border rounded-lg p-2 h-32 mt-2"
                    ></textarea>
                </div>
            </div>

            <div class="flex justify-between gap-2">
                <button 
                    @click="closeModal" 
                    class="bg-gray-300 text-black py-2 px-4 rounded-lg"
                >
                    Annuler
                </button>
                <div class="flex gap-2">
                    <button class="bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-400" @click="deletePost">
                        Supprimer
                    </button>

                    <button @click="saveChanges" class="bg-blue-500 text-white py-2 px-4 rounded-lg">
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
export default {
    name: 'PostModal',
    props: {
        post: Object,
        postTypes: Array,
        isOpen: Boolean,
        onClose: Function,
        onSave: Function,
        onDelete: Function,
        errorMessage: String,
        campaignStartDateTime: String,
        campaignEndDateTime: String,
    },

    data() {
        return {
            editedPost: null,
        };
    },

    watch: {
        post(newPost) {
            if (newPost) {
                this.editedPost = this.deepCopyPost(newPost);
            } else {
                this.editedPost = null;
            }
        },
        
        isOpen(newVal) {
            if (newVal && this.post) {
                // When modal opens, create a fresh copy of the post
                this.editedPost = this.deepCopyPost(this.post);
                
                // Reset file input when opening modal
                this.$nextTick(() => {
                    if (this.$refs.fileInput) {
                        this.$refs.fileInput.value = '';
                    }
                });
            }
        }
    },
    
    mounted() {
        if (this.post) {
            this.editedPost = this.deepCopyPost(this.post);
        }
    },

    methods: {
        deepCopyPost(post) {
            if (!post) return null;
            
            const copy = {
                id: post.id,
                tempId: post.tempId,
                scheduledDateTime: post.scheduledDateTime,
                type: post.type,
                content: {
                    text: post.content?.text || "",
                    caption: post.content?.caption || "",
                    url: post.content?.url || "",
                    title: post.content?.title || "",
                    description: post.content?.description || "",
                }
            };
            
            // Handle file separately since it can't be deep copied with JSON methods
            if (post.content?.file) {
                copy.content.file = post.content.file;
                copy.content.fileName = post.content.fileName || post.content.file.name;
            } else {
                copy.content.file = null;
                copy.content.fileName = null;
            }
            
            return copy;
        },

        handleFileUpload(event) {
            const file = event.target.files && event.target.files[0];
            if (file) {
                this.editedPost.content.file = file;
                this.editedPost.content.fileName = file.name;
            } else {
                this.editedPost.content.file = null;
            }
        },

        resetPostContent() {
            const currentType = this.editedPost.type;
            
            this.editedPost.content = {
                text: "",
                file: null,
                fileName: null,
                caption: "",
                url: "",
                title: "",
                description: "",
            };
            
            this.$nextTick(() => {
                if (this.$refs.fileInput) {
                    this.$refs.fileInput.value = '';
                }
            });
        },

        deletePost() {
            if (this.editedPost.id || this.editedPost.tempId) {
                this.onDelete(this.editedPost.id || this.editedPost.tempId);
                this.closeModal();
            } else {
                console.error("No ID or tempId for post to delete");
            }
        },
        
        saveChanges() {
            // Create a clean copy of the post to save
            const postToSave = this.deepCopyPost(this.editedPost);
            
            // Call the parent's save function with the clean copy
            this.onSave(postToSave);
        },
        
        closeModal() {
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
            
            this.onClose();
        }
    },
}
</script>

<style scoped>
input[type="datetime-local"] {
    padding: 0.5em;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-family: inherit;
    width: 100%;
    box-sizing: border-box;
}

select {
    width: 100%;
    padding: 0.75em;
    border-radius: 0.5em;
    border: 1px solid #e2e8f0;
    box-sizing: border-box;
}
</style>