<template>
    <div class="flex flex-col gap-3" v-if="currentStep === 1">
        <!-- Dates -->
        <div class="flex items-center justify-between gap-2">
            <div class="w-full">
                <label class="block text-md fw-semibold mb-2">Date de Début :</label>
                <div class="p-2 border rounded-lg">{{ formatDate(campaign.start_date) }}</div>
            </div>
            <div class="w-full">
                <label class="block text-md fw-semibold mb-2">Date de Fin :</label>
                <div class="p-2 border rounded-lg">{{ formatDate(campaign.end_date) }}</div>
            </div>
        </div>

        <!-- Audience -->
        <div>
            <label class="block text-md fw-semibold mb-2">Audience cible :</label>
            <div class="py-2 px-3 border rounded-lg">{{ audienceName }}</div>
        </div>

        <!-- Frequency -->
        <div>
            <label class="block fw-semibold text-md mb-2">Fréquence des Posts :</label>
            <div class="relative">
                <div class="w-full border bg-white rounded-lg p-2 px-4">{{ campaign.frequency_per_day }}</div>
                <button class="bg-black text-white absolute right-0 top-0 bottom-0 py-2 px-3" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;" disabled>par Jour</button>
            </div>
        </div>

        <!-- Name and Color -->
        <div class="flex items-center flex-wrap gap-2 mt-2">
            <div class="mb-2 flex-1">
                <label class="block fw-semibold text-md mb-2">Nom de Campagne :</label>
                <div class="min-w-[300px] w-full h-[50px] border rounded-xl p-3 bg-white mb-1">{{ campaign.name }}</div>
            </div>
            <div class="mb-2">
                <label class="block fw-semibold text-md mb-2">Couleur :</label>
                <div :style="{ backgroundColor: campaign.color }" style="border: 4px solid white; outline: 1px solid #ddd;" class="min-w-[300px] max-w-[500px] h-[50px] rounded-xl p-2 mb-1"></div>
            </div>
        </div>

        <!-- Description -->
        <div>
            <label class="block fw-semibold text-md mb-2">Description du Campagne :</label>
            <ScrollPanel style="max-width: 700px; height: 200px; border: 1px solid #eee;" class="p-2 rounded-lg">
                <p>{{ campaign.description }}</p>
            </ScrollPanel>
        </div>

        <Button 
            @click="currentStep = 2"
            style="background-color: black; color: white; border: 1px solid black; font-weight: 500;"
        > 
            Voir les Posts
        </Button>
    </div>


    <div class="flex-flex-col items-center" v-if="currentStep === 2">
            <div class="max-h-[500px] overflow-y-scroll">
                <div 
                    v-if="getCampaignPosts().length > 0"
                >
                    <post-view  
                        v-for="post in getCampaignPosts()"
                        :key="post.id" 
                        :post="post" 
                        :account="linkedinAccount" 
                    />
                </div>

                <div class="w-full text-center min-w-[300px]" v-else>
                    <p class="text-muted py-4">pas de Posts</p>
                </div>
            </div>
    </div>
</template>

<script>
export default {
    name: 'CampaignDetails',

    props: {
        linkedinAccount: {
            type: Object,
            required: true,
        },

        campaign: { 
            type: Object, 
            required: true 
        },
        cibles: { 
            type: Array, 
            required: true 
        },

        posts: {
            type: Array,
            required: true
        },
    },

    data() {
        return {
            currentStep: 1,
        };
    },

    computed: {
        audienceName() {
            const cible = this.cibles.find(c => c.id === this.campaign.target_audience);
            return cible ? cible.name : 'Unknown';
        },
    },

    methods: {
        getCampaignPosts() {
            const campaignPosts = this.posts.filter(post => post.campaign_id === this.campaign.id);
            return campaignPosts;
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

        getPostTypeIcon(type) {
            switch (type) {
                case 'text': return '📝';
                case 'image': return '🖼️';
                case 'video': return '🎬';
                case 'article': return '📰';
                default: return '📌';
            }
        },

        formatTime(dateTime) {
            return new Date(dateTime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },
    },
};
</script>