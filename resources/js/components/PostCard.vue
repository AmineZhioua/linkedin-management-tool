<template>
    <div class="w-full h-full bg-gray-100 shadow-lg p-3 rounded-lg post-card">
        <h3>{{ post.type }} Post</h3>

        <div v-if="parsedContent">
            <p v-if="post.type === 'text'">
                {{ parsedContent.text }}
            </p>
        </div>

        <div v-if="parsedContent">
            <div v-if="post.type === 'image'">
                <img 
                    :src="getMediaUrl(parsedContent.file_path)" 
                    alt="Post Image" 
                    class="w-full h-auto rounded-lg"
                    v-if="parsedContent.file_path"
                >
            </div>
            <div v-if="post.type === 'video'">
                <video 
                    controls 
                    class="w-full h-auto rounded-lg"
                    v-if="parsedContent.file_path"
                >
                    <source :src="getMediaUrl(parsedContent.file_path)" type="video/mp4">
                </video>
            </div>
            <p>{{ post.status }}</p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'PostCard',
    props: {
        post: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            parsedContent: null
        };
    },
    mounted() {
        if (this.post.content) {
            try {
                this.parsedContent = JSON.parse(this.post.content);
            } catch (error) {
                console.error('Failed to parse post content:', error);
                this.parsedContent = { text: 'Invalid content' };
            }
        } else {
            this.parsedContent = { text: 'No content available' };
        }
    },
    methods: {
        getMediaUrl(filePath) {
            return filePath ? `/linkedin/${filePath}` : '';
        },
    }
}
</script>

<style>
.post-card {
    transition: 0.2s all ease-in-out;
    cursor: pointer;
}

.post-card:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.336);
}
</style>