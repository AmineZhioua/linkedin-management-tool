<template>
    <div class="flex items-center justify-between mt-5 mb-3">
        <div class="flex items-center gap-2 mt-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="mb-0" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000">
                <path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v480q0 33-23.5 56.5T800-160H160Zm0-80h420v-140H160v140Zm500 0h140v-360H660v360ZM160-460h420v-140H160v140Z"/>
            </svg>
            <h1 class="fw-semibold text-2xl mb-0">Posts :</h1>
        </div>

        <div class="flex items-center gap-2">
            <!-- Filtrer par Status -->
            <div class="flex flex-col">
                <label for="statut">Statut</label>
                <Select v-model="selectedPostStatus" :options="PostStatuses" showClear optionLabel="name" 
                    placeholder="Choisissez un statut" class="w-full md:w-56"
                >
                    <template #option="slotProps">
                        <div
                            class="px-2 py-1 rounded"
                            :style="{ color: postStatusColors[slotProps.option.value], fontWeight: '500' }"
                        >
                            {{ slotProps.option.name }}
                        </div>
                        </template>

                        <!-- Custom display for selected value -->
                        <template #value="slotProps">
                        <div v-if="slotProps.value" :style="{ color: postStatusColors[slotProps.value.value], fontWeight: '500' }">
                            {{ slotProps.value.name }}
                        </div>
                        <span v-else class="text-gray-400">Choisissez un statut</span>
                    </template>
                </Select>
            </div>

            <!-- Filtrer par Date de Début -->
            <div>
                <label for="date-post">Date de Publication</label>
                <DatePicker v-model="filtredScheduledPostTime" showIcon iconDisplay="input" fluid />
            </div>
        </div>
    </div>
    <div class="p-2 mt-2 rounded-xl" style="background-color: #18181b;">
        <DataTable :value="filtredPostsArray" paginator stripedRows :rows="5" 
            :rowsPerPageOptions="[5, 10, 20, 50]" 
            tableStyle="min-width: 50rem"
            paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
            currentPageReportTemplate="{first} to {last} of {totalRecords}"
            class="mt-4 w-full"
        >
            <Column header="Compte">
                <template #body="slotProps">
                    <div class="flex items-center gap-2">
                        <img 
                            :src="getProfilePicture(linkedinAccounts, slotProps.data.linkedin_user_id)" 
                            alt="Profile Picture" 
                            class="rounded-full" 
                            style="width: 32px; 
                            height: 32px;" 
                        />
                        <span>{{ getUsername(linkedinAccounts, slotProps.data.linkedin_user_id) }}</span>
                    </div>
                </template>
            </Column>
            <Column header="Campagne">
                <template #body="slotProps">
                    <div 
                        class="p-1 rounded-lg text-center" 
                        :style="{backgroundColor: getCampaignColor(campaigns, slotProps.data.campaign_id)}"
                    >
                        {{ getCampaignName(slotProps.data.campaign_id) }}
                    </div>
                </template>
            </Column>

            <Column header="Type">
                <template #body="slotProps">
                    {{ formatPostType(slotProps.data.type) }}
                </template>
            </Column>

            <Column header="Heure de Publication Prévue">
                <template #body="slotProps">
                    {{ formatDateWithMonth(slotProps.data.scheduled_time) }}
                </template>
            </Column>
            <Column header="Statut">
                <template #body="slotProps">
                    <div 
                        class="p-1 rounded-lg text-center"
                        :class="{
                            'bg-blue-600 text-white': slotProps.data.status === 'queued',
                            'bg-green-600 text-white': slotProps.data.status === 'posted',
                            'bg-red-500 text-white': slotProps.data.status === 'failed',
                            'bg-orange-500 text-white': slotProps.data.status === 'draft',
                        }"
                    >
                        {{ translatePostStatus(slotProps.data.status) }}
                    </div>
                </template>
            </Column>
            <Column header="Créé en">
                <template #body="slotProps">
                    {{ formatDateWithMonth(slotProps.data.created_at) }}
                </template>
            </Column>
            <Column header="Actions">
                <template #body="slotProps">
                    <SplitButton 
                        icon="pi pi-eye" 
                        @click="openReadMode(slotProps.data.id)" 
                        :model="getItemsPostsDatatable(slotProps.data)" 
                        style="color: red;" 
                        severity="contrast" 
                        rounded
                    />
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script>
import { 
    getProfilePicture, 
    getUsername, 
    getCampaignColor,
    formatDate,
    formatDateWithMonth,
} from '../services/datatables.js';

export default {
    name: 'PostsDatatable',

    props: {
        linkedinPosts: {
            type: Array,
            required: true,
        },
        campaigns: {
            type: Array,
            required: true,
        },
        linkedinAccounts: {
            type: Array,
            required: true,
        },

        openPostReadMode: {
            type: Function,
            required: true,
        },
    },

    emits: ['edit-linkedin-post', 'delete-post', 'delete-post-from-linkedin', 'request-boost-interaction'],

    data() {
        return {
            selectedPostStatus: null,
            filtredScheduledPostTime: null,
            PostStatuses: [
                { name: 'En attente', value: 'queued' },
                { name: 'Publié', value: 'posted' },
                { name: 'Échoué', value: 'failed' },
                { name: 'Brouillon', value: 'draft' }
            ],
            postStatusColors: {
                queued: 'rgb(37 99 235)',
                posted: '#10b981',
                failed: '#ef4444',
                draft: 'rgb(249 115 22)',
            },
        };
    },

    computed: {
        filtredPostsArray() {
            let filtredPosts = this.linkedinPosts;

            if(this.selectedPostStatus && this.selectedPostStatus.value) {
                filtredPosts = filtredPosts.filter(post => post.status === this.selectedPostStatus.value);
            }

            if (this.filtredScheduledPostTime) {
                const startFilter = new Date(this.filtredScheduledPostTime);
                startFilter.setHours(0, 0, 0, 0);
                filtredPosts = filtredPosts.filter(post => {
                    const campaignStart = new Date(post.scheduled_time);
                    campaignStart.setHours(0, 0, 0, 0);
                    return campaignStart.getTime() === startFilter.getTime();
                });
            }

            return filtredPosts;
        },
    },  

    methods: {
        getProfilePicture, 
        getUsername, 
        getCampaignColor,
        formatDate,
        formatDateWithMonth,


        // THIS FUNCTION WAS MADE FOR THE ITEMS IN DATATABLE
        getItemsPostsDatatable(post) {
            if(post.status === "queued") {
                return [
                    {
                        label: 'Modifier',
                        icon: 'pi pi-file-edit',
                        command: () => this.emitPostToEdit(post)
                    },
                    {
                        label: 'Supprimer',
                        icon: 'pi pi-times',
                        command: () => this.emitPostToDelete(post.id)
                    },
                ];
            } else if(post.status === "failed") {
                return [
                    {
                        label: 'Supprimer',
                        icon: 'pi pi-times',
                        command: () => this.emitPostToDelete(post.id)
                    },
                ];
            } else if(post.status === "posted") {
                return [
                    {
                        label: 'Demande le Boost d\'interactions',
                        icon: 'pi pi-bolt',
                        command: () => this.emitPostToRequestBoost(post)
                    },
                    {
                        label: 'Supprimer de LinkedIn',
                        icon: 'pi pi-times',
                        command: () => this.emitPostToDeleteFromLinkedin(post)
                    }
                ];
            }
        },

        emitPostToEdit(post) {
            this.$emit('edit-linkedin-post', post);
        },

        emitPostToDelete(postId) {
            this.$emit('delete-post', postId);
        },

        emitPostToDeleteFromLinkedin(post) {
            this.$emit('delete-post-from-linkedin', post);
        },

        emitPostToRequestBoost(post) {
            this.$emit('request-boost-interaction', post);
        },

        openReadMode(postId) {
            this.openPostReadMode(postId);
        },

        getCampaignName(campaignId) {
            if (!campaignId) return 'UNIQUE';
            const campaign = this.campaigns.find(c => c.id === campaignId);
            return campaign ? campaign.name : 'inconnu';
        },

        translatePostStatus(status) {
            const statusMap = {
                'queued': 'En attente',
                'posted': 'Publié',
                'failed': 'Echoué',
                'draft': 'Brouillons'
            };
            return statusMap[status] || status;
        },

        formatPostType(type) {
            switch(type) {
                case 'text': return "Texte";
                case 'video': return "Vidéo";
                case 'image': return 'Image';
                case 'multiimage': return 'Multi-image';
                case 'artcile': return 'Article';
                default: return 'Inconnu'
            }
        },
    },
};
</script>