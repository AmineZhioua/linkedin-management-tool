<template>
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg">
            <div class="flex justify-between align-items-center mb-4">
                <h3 class="text-lg font-semibold mb-0">Modifier le Post</h3>
                <button 
                    @click="onClose" 
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
                        @change="(e) => handleFileUpload(e)"
                        :accept="editedPost.type === 'image' ? 'image/*' : 'video/*'"
                        class="w-full border rounded-lg p-2 mb-2"
                    />
                    <p v-if="editedPost.content.file" class="mt-2 mb-0 text-sm text-gray-600">
                        Fichier sélectionné : {{ editedPost.content.fileName || editedPost.content.file.name }}
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

            <div class="flex justify-end gap-2">
                <button 
                    @click="onClose" 
                    class="bg-gray-300 text-black py-2 px-4 rounded-lg"
                >
                    Annuler
                </button>
                <button @click="saveChanges" class="bg-blue-500 text-white py-2 px-4 rounded-lg">
                    Enregistrer
                </button>
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
        errorMessage: String,
        campaignStartDateTime: String,
        campaignEndDateTime: String
    },

    data() {
        return {
            editedPost: null
        };
    },

    watch: {
        post: {
            immediate: true,
            handler(newPost) {
                if (newPost) {
                    this.editedPost = this.deepCopyPost(newPost);
                }
            }
        }
    },

    methods: {
        deepCopyPost(post) {
            const copy = { ...post };
            
            copy.content = { ...post.content };
            
            if (post.content.file) {
                copy.content.file = post.content.file;
                copy.content.fileName = post.content.file.name;
            }
            
            return copy;
        },

        handleFileUpload(event) {
            if (event.target.files && event.target.files[0]) {
                const file = event.target.files[0];
                this.editedPost.content.file = file;
                this.editedPost.content.fileName = file.name;
            }
        },

        resetPostContent() {
            this.editedPost.content = {
                text: "",
                file: null,
                fileName: null,
                caption: "",
                url: "",
                title: "",
                description: "",
            };
        },

        saveChanges() {
            this.onSave(this.editedPost);
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