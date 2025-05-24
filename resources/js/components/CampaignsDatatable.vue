<template>
    <div class="flex items-center justify-between mt-5 mb-3">
        <!-- Title & Icon -->
        <div class="flex items-center gap-2 mt-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="mb-0" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000">
                <path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v480q0 33-23.5 56.5T800-160H160Zm0-80h420v-140H160v140Zm500 0h140v-360H660v360ZM160-460h420v-140H160v140Z"/>
            </svg>
            <h1 class="fw-semibold text-2xl mb-0">Campagnes :</h1>
        </div>

        <div class="flex items-center gap-2">
            <!-- Filtrer par Status -->
            <div class="flex flex-col">
                <label for="statuy">Statut</label>
                <Select v-model="selectedStatus" :options="statuses" showClear optionLabel="name" placeholder="Choisissez un statut" class="w-full md:w-56">
                    <template #option="slotProps">
                        <div
                            class="px-2 py-1 rounded"
                            :style="{ color: statusColors[slotProps.option.value], fontWeight: '500' }"
                        >
                            {{ slotProps.option.name }}
                        </div>
                        </template>

                        <!-- Custom display for selected value -->
                        <template #value="slotProps">
                        <div v-if="slotProps.value" :style="{ color: statusColors[slotProps.value.value], fontWeight: '500' }">
                            {{ slotProps.value.name }}
                        </div>
                        <span v-else class="text-gray-400">Choisissez un statut</span>
                    </template>
                </Select>
            </div>

            <!-- Filtrer par Date de Début -->
            <div>
                <label for="date-debut">Date de Début</label>
                <DatePicker v-model="filtredStartDate" showIcon iconDisplay="input" fluid />
            </div>

            <!-- Filtrer par Date de Fin -->
            <div>
                <label for="date-debut">Date de Fin</label>
                <DatePicker v-model="filtredEndDate" showIcon fluid iconDisplay="input" />
            </div>
        </div>

    </div>
    <div class="p-2 mt-2 rounded-xl" style="background-color: #18181b;">
        <DataTable v-model:filters="filters" :value="filteredCampaigns" paginator stripedRows :rows="5" filterDisplay="row"
            :rowsPerPageOptions="[5, 10, 20, 50]" 
            :globalFilterFields="['name', 'campaign.name', 'campaign.target_audience', 'campaign.status']"
            tableStyle="min-width: 50rem"
            paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
            currentPageReportTemplate="{first} to {last} of {totalRecords}"
            class="mt-4 w-full"
        >
            <template #header>
                <div class="flex justify-end">
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search"></i>
                        </InputIcon>
                        <InputText v-model="filters['global'].value" placeholder="Nom du Campagne..." />
                    </IconField>
                </div>
            </template>
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
            <Column header="Nom">
                <template #body="slotProps">
                    <div class="text-center">
                        {{ slotProps.data.name }}
                    </div>
                </template>
            </Column>

            <Column field="target_audience" header="Audience"></Column>

            <Column header="Date de début">
                <template #body="slotProps">
                    {{ formatDate(slotProps.data.start_date) }}
                </template>
            </Column>
            <Column header="Date de fin">
                <template #body="slotProps">
                    {{ formatDate(slotProps.data.end_date) }}
                </template>
            </Column>
            <Column header="Statut">
                <template #body="slotProps">
                    <div 
                        class="p-1 rounded-lg text-center"
                        :class="{
                            'bg-blue-600 text-white': slotProps.data.status === 'scheduled',
                            'bg-green-600 text-white': slotProps.data.status === 'completed',
                            'bg-red-500 text-white': slotProps.data.status === 'failed',
                            'bg-orange-500 text-white': slotProps.data.status === 'draft',
                        }"
                    >
                        {{ translateStatus(slotProps.data.status) }}
                    </div>
                </template>
            </Column>
            <Column header="Fréquence">
                <template #body="slotProps">
                    <div class="text-center">
                        {{ slotProps.data.frequency_per_day }}
                    </div>
                </template>
            </Column>

            <Column header="Couleur">
                <template #body="slotProps">
                    <div 
                        class="p-2 rounded-lg border"
                        :style="{backgroundColor: getCampaignColor(campaigns, slotProps.data.id)}"
                    ></div>
                </template>
            </Column>
            <Column header="Actions">
                <template #body="slotProps">
                    <SplitButton 
                        icon="pi pi-eye" 
                        @click="openReadMode(slotProps.data.linkedin_user_id, slotProps.data.id)" 
                        :model="getItemsCampaignsDatatable(slotProps.data.id)" 
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
import { FilterMatchMode } from '@primevue/core/api';
import { 
    getProfilePicture, 
    getUsername, 
    getCampaignColor,
    formatDate,
} from '../services/datatables.js';

export default {
    name: 'CampaignsDatatable',

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
        openCampaignReadMode: Function,
    },

    emits: ['delete-campaign'],

    data() {
        return {
            filtredStartDate: null,
            filtredEndDate: null,
            selectedStatus: null,
            statuses: [
                { name: 'En attente', value: 'scheduled' },
                { name: 'Complété', value: 'completed' },
                { name: 'Échoué', value: 'failed' },
                { name: 'Brouillons', value: 'draft' }
            ],
            statusColors: {
                scheduled: 'rgb(37 99 235)',
                completed: '#10b981',
                failed: '#ef4444',
                draft: 'rgb(249 115 22)',
            },
            filters: {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
                name: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
                'campaign.target_audience': { value: null, matchMode: FilterMatchMode.STARTS_WITH },
                'campaign.status': { value: null, matchMode: FilterMatchMode.EQUALS },
            },
        };
    },

    computed: {
        filteredCampaigns() {
            let filtered = this.campaigns;

            if (this.selectedStatus && this.selectedStatus.value) {
                filtered = filtered.filter(campaign => campaign.status === this.selectedStatus.value);
            }

            if (this.filtredStartDate) {
                const startFilter = new Date(this.filtredStartDate);
                startFilter.setHours(0, 0, 0, 0);
                filtered = filtered.filter(campaign => {
                    const campaignStart = new Date(campaign.start_date);
                    campaignStart.setHours(0, 0, 0, 0);
                    return campaignStart.getTime() === startFilter.getTime();
                });
            }

            if (this.filtredEndDate) {
                const endFilter = new Date(this.filtredEndDate);
                endFilter.setHours(0, 0, 0, 0);
                filtered = filtered.filter(campaign => {
                    const campaignEnd = new Date(campaign.end_date);
                    campaignEnd.setHours(0, 0, 0, 0);
                    return campaignEnd.getTime() === endFilter.getTime();
                });
            }

            return filtered;
        },
    },

    methods: {
        getUsername,
        getProfilePicture,
        getCampaignColor,
        formatDate,

        getItemsCampaignsDatatable(id) {
            return [
                {
                    label: 'Supprimer',
                    icon: 'pi pi-times',
                    command: () => this.emitCampaignToDelete(id)
                },
            ];
        },

        openReadMode(linkedinUserId, campaignId) {
            this.openCampaignReadMode(linkedinUserId, campaignId);
        },

        emitCampaignToDelete(campaignId) {
            this.$emit('delete-campaign', campaignId);
        },

        translateStatus(status) {
            const statusMap = {
                'scheduled': 'En attente',
                'completed': 'Complété',
                'failed': 'Echoué',
                'draft': 'Brouillons'
            };
            return statusMap[status] || status;
        },
    },
};
</script>