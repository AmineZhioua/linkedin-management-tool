<template>
    <div class="bg-white p-3 rounded-lg min-w-[400px] max-w-[450px]">
        <div class="border rounded-md">
            <!-- Heading of the LinkedIn Post Preview Card -->
            <div class="flex items-center gap-2 p-2">
                <img 
                    v-if="account.linkedin_picture"
                    :src="account.linkedin_picture" 
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
                    <h4 v-if="account" class="fw-bold mb-0" style="font-size: 15px;">
                        {{ account.linkedin_firstname }} {{ account.linkedin_lastname }}
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
            <div v-if="post.type === 'text'" class="px-2 my-2 max-h-[300px] overflow-y-scroll">
                <p class="mb-0 text-black">{{ parsedContent.text }}</p>
            </div>

            <!-- Image Type Preview -->
            <div v-if="post.type === 'image'" class="w-full h-auto">
                <p class="my-2 px-2">{{ parsedContent.caption }}</p>
                <img 
                    :src="getMediaUrl(parsedContent.file_path)" 
                    class="object-fill w-full" 
                    alt="Image Preview"
                />
            </div>

            <!-- Video Type Preview -->
            <div v-if="post.type === 'video'" class="w-full h-auto">
                <p class="my-2 px-2">{{ parsedContent.caption }}</p>
                <video 
                    controls 
                    class="w-full"
                >
                    <source :src="getMediaUrl(parsedContent.file_path)" type="video/mp4">
                    Votre navigateur ne supporte pas la lecture de vidéos.
                </video>
            </div>

            <!-- Article Type Preview -->
            <div v-if="post.type === 'article'" class="w-full h-auto">
                <p class="my-2 px-2">{{ parsedContent.caption || 'Votre légende ici...' }}</p>
                <div class="border p-2 rounded-md">
                    <a :href="parsedContent.url || '#'" target="_blank" class="text-blue-600 hover:underline">
                        {{ parsedContent.title || 'Titre de l\'article' }}
                    </a>
                    <p class="text-gray-500">{{ parsedContent.description || 'Description de l\'article' }}</p>
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
</template>

<script>
export default {
    name: "PostView",
    
    props: {
        post: {
            type: Object,
            required: true,
        },
        account: {
            type: Object,
            required: true,
        },
    },

    computed: {
        parsedContent() {
            try {
                return JSON.parse(this.post.content);
            } catch (error) {
                console.error("Failed to parse post content:", error);
                return {};
            }
        },
    },

    methods: {
        getMediaUrl(filePath) {
            return filePath ? `/linkedin/${filePath}` : '';
        },
    },
};
</script>