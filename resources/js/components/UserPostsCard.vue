<template>
    <div class="w-full h-full p-4 overflow-y-scroll">
        <!-- style="background-color: #FEF4E5;" -->
        <!-- Heading Section -->
        <div class="flex items-center justify-between">
            <!-- Accounts Buttons Sections -->
            <div class="grid grid-cols-3 gap-2">
                <!-- LinkedIn Account Selection -->
                <div 
                    v-for="linkedinAccount in userLinkedinAccounts" 
                    :key="linkedinAccount.id"
                    @click="selectAccount(linkedinAccount)"
                    class="flex items-center justify-end gap-2 py-2 px-3 rounded-xl cursor-pointer shadow-lg"
                    style="background-color: #18181b;"
                    :class="{ 'text-red-500 border': this.selectedAccount === linkedinAccount }"
                >
                    <div class="relative">
                        <img 
                            v-if="linkedinAccount.linkedin_picture"
                            :src="linkedinAccount.linkedin_picture" 
                            alt="Linkedin Picture" 
                            class="rounded-full"
                            height="45" 
                            width="45"
                        />

                        <img 
                            v-else
                            src="/build/assets/images/default-profile.png"
                            alt="Linkedin Picture" 
                            class="rounded-full px-0"
                            height="45" 
                            width="45"
                        />
                        <div 
                            class="p-1 absolute rounded-full bottom-[-3px] right-[-5px]" 
                            style="background-color: rgb(23 92 179); border: 3px solid white;"
                        >
                            <img 
                                src="/build/assets/icons/linkedin.svg" 
                                alt="Linkedin Icon" 
                                height="12"
                                width="12"
                            />
                        </div>
                    </div>

                    <div class="flex flex-col ml-2 flex-1">
                        <h3 class="fw-semibold text-lg mb-0 text-white">{{ linkedinAccount.linkedin_firstname }} {{ linkedinAccount.linkedin_lastname }}</h3>
                        <p class="mb-0 text-sm text-gray-400">Compte personnel</p>
                    </div>

                    <button class="cursor-pointer ml-2">
                        <!-- <RadioButton v-model="selectedAccount" inputId="linkedinAccount" name="account" :value="selectedAccount" /> -->
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff">
                            <path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm70-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- New Post & Campaign Buttons Section -->
            <div class="flex items-center gap-2">
                <Button 
                    @click="showPortal = true" 
                    class="cursor-pointer bg-black text-white px-3 py-2 rounded-md flex items-center gap-2"
                    style="border: 1px solid pink;"
                >
                    <img src="/build/assets/icons/add-white.svg" alt="Add Icon" />
                    <span>Nouveau Post</span>
                </Button>
                <Button 
                    @click="openCampaignPortal"
                    class="cursor-pointer bg-black text-white px-4 py-2 rounded-md flex items-center gap-2"
                    style="border: 1px solid pink;"
                >
                    <img src="/build/assets/icons/add-white.svg" alt="Add Icon" />
                    <span>Nouveau Campagne</span>
                </Button>
            </div>
        </div>

        <!-- En Attente & Brouillons & Publié & Echoué Buttons -->
        <div class="flex items-center justify-between px-2 mt-2" style="border-bottom: 1px solid #888;">
            <div class="grid grid-cols-4 gap-5">
                <button 
                    @click="filterPostsByStatus('queued')"
                    class="flex items-center justify-center py-4 px-1 gap-2 text-black"
                >
                    <span>En Attente</span>
                    <span class="px-2 bg-gray-300 rounded-full">{{ queuedPostsCount }}</span>
                </button>

                <button 
                    @click="filterPostsByStatus('draft')"
                    class="flex items-center justify-center gap-2 py-4 px-1 text-black">
                    <span>Brouillons</span>
                    <span class="px-2 bg-gray-300 rounded-full">{{ draftPostsCount }}</span>
                </button>

                <button 
                    @click="filterPostsByStatus('posted')"
                    class="flex items-center justify-center gap-2 py-4 px-1 text-black"
                >
                    <span>Publié</span>
                    <span class="px-2 bg-gray-300 rounded-full">{{ postedPostsCount }}</span>
                </button>

                <button 
                    @click="filterPostsByStatus('failed')"
                    class="flex items-center justify-center gap-2 py-4 px-1 text-black"
                >
                    <span>Echoué</span>
                    <span class="px-2 bg-gray-300 rounded-full">{{ failedPostsCount }}</span>
                </button>
            </div>

            <div class="flex items-center gap-4">
                <FloatLabel class="w-full md:w-40" variant="over">
                    <Select 
                        v-model="view" 
                        inputId="over_label" 
                        :options="views" 
                        optionLabel="name" 
                        class="w-full" 
                        variant="filled"
                        style="background-color: black;"
                    >
                    </Select>
                    <label for="over_label">Vue</label>
                </FloatLabel>
            </div>
        </div>

        <!-- Posts Cards Container -->
        <div class="flex flex-col justify-between h-full relative" v-if="view && view.name === 'Cartes'">
            <div v-if="filteredPosts.length === 0" class="flex items-center justify-center">
                <svg viewBox="0 0 200 200" width="220" height="220" xmlns="http://www.w3.org/2000/svg">
                    <!-- Ghost body -->
                    <path d="M50 120 Q60 70 100 70 Q140 70 150 120 Q150 130 140 130 Q130 130 125 120 Q120 130 110 130 Q100 130 95 120 Q90 130 80 130 Q70 130 65 120 Q60 130 50 130 Q50 125 50 120Z" fill="#F9F9F9" stroke="#CCC" stroke-width="2"/>
                    <circle cx="80" cy="100" r="5" fill="#333"/>
                    <circle cx="120" cy="100" r="5" fill="#333"/>
                    <path d="M75 90 Q80 85 85 90" stroke="#333" stroke-width="2" fill="none"/>
                    <path d="M115 90 Q120 85 125 90" stroke="#333" stroke-width="2" fill="none"/>
                    <circle cx="60" cy="60" r="12" fill="none" stroke="#555" stroke-width="3"/>
                    <line x1="68" y1="68" x2="75" y2="75" stroke="#555" stroke-width="3"/>
                    <path d="M90 110 Q100 115 110 110" stroke="#333" stroke-width="2" fill="none"/>
                    <ellipse cx="100" cy="140" rx="40" ry="6" fill="#eee"/>
                    <text x="50%" y="180" text-anchor="middle" font-size="14" fill="#666">
                        Pas des Posts...
                    </text>
                </svg>
            </div>

            <div class="grid grid-cols-4 gap-3 w-full h-full pb-3 mt-4">
                <linkedin-post 
                    v-for="post in paginatedPosts" 
                    :key="post.id" 
                    :user-linkedin-accounts="userLinkedinAccounts" 
                    :post="post" 
                    @edit-post="editPost"
                    @delete-post="deletePost"
                    @delete-post-from-linkedin="deletePostFromLinkedIn"
                />
            </div>
            <Paginator 
                :rows="rowsPerPage" 
                :totalRecords="filteredPosts.length" 
                :rowsPerPageOptions="[10, 20, 30]"
                @page="onPageChange"
            ></Paginator>
        </div>

        <!-- DataTable for Tableau view -->
        <!-- Posts DataTable -->
        <div v-if="view && view.name === 'Tableau'" class="flex flex-col">
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
                        <label for="statuy">Statut</label>
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
                                    :src="getProfilePicture(slotProps.data.linkedin_user_id)" 
                                    alt="Profile Picture" 
                                    class="rounded-full" 
                                    style="width: 32px; 
                                    height: 32px;" 
                                />
                                <span>{{ getUsername(slotProps.data.linkedin_user_id) }}</span>
                            </div>
                        </template>
                    </Column>
                    <Column header="Campagne">
                        <template #body="slotProps">
                            <div 
                                class="p-1 rounded-lg text-center" 
                                :style="{backgroundColor: getCampaignColor(slotProps.data.campaign_id)}"
                            >
                                {{ getCampaignName(slotProps.data.campaign_id) }}
                            </div>
                        </template>
                    </Column>

                    <Column field="type" header="Type"></Column>

                    <Column header="Heure de Publication Prévue">
                        <template #body="slotProps">
                            {{ formatDate(slotProps.data.scheduled_time) }}
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
                            {{ formatDate(slotProps.data.created_at) }}
                        </template>
                    </Column>
                    <Column header="Actions">
                        <template #body="slotProps">
                            <SplitButton 
                                icon="pi pi-eye" 
                                @click="openPostInReadMode(slotProps.data.id)" 
                                :model="getItemsPostsDatatable(slotProps.data)" 
                                style="color: red;" 
                                severity="contrast" 
                                rounded
                            />
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>



        <!-- Campaigns DataTable -->
        <div v-if="view && view.name === 'Tableau'" class="flex flex-col">
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
                                    :src="getProfilePicture(slotProps.data.linkedin_user_id)" 
                                    alt="Profile Picture" 
                                    class="rounded-full" 
                                    style="width: 32px; 
                                    height: 32px;" 
                                />
                                <span>{{ getUsername(slotProps.data.linkedin_user_id) }}</span>
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
                                :style="{backgroundColor: getCampaignColor(slotProps.data.id)}"
                            ></div>
                        </template>
                    </Column>
                    <Column header="Actions">
                        <template #body="slotProps">
                            <SplitButton 
                                icon="pi pi-eye" 
                                @click="openCampaignInReadMode(slotProps.data.linkedin_user_id, slotProps.data.id)" 
                                :model="getItemsCampaignsDatatable(slotProps.data.id)" 
                                style="color: red;" 
                                severity="contrast" 
                                rounded
                            />
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>



        <!-- Post Portal Component -->
        <post-portal 
            v-if="showPortal"
            :all-linkedin-accounts="userLinkedinAccounts" 
            :selected-post="selectedPost"
            :read-mode="readModeStatus"
            @close="showPortal = false; selectedPost = null"
            @close-readmode="showPortal = false; selectedPost = null; readModeStatus = false"
        />

        <campaign-portal
            v-if="showCampaignPortal"
            ref="campaignPortal"
            :selected-account="selectedAccount"
            :read-mode="readModeStatus"
            :selectedCampaign="selectedCampaign"
            :linkedin-posts="userLinkedinPosts"
            @campaign-post-editing="editCampaignPost"
            @posts-generated="handlePostsGenerated"
            @dates-updated="updateCampaignDates"
            @update:form-data="updateFormData"
            @validate="isFormValid = $event"
            @close-campaign-portal="showCampaignPortal = false; selectedCampaign = null; readModeStatus = false; selectedAccount = null;"
        />

        <campaign-post-portal 
            v-if="showCampaignPostPortal"
            :selected-account="selectedAccount"
            :selected-post="selectedPost"
            :all-linkedin-accounts="userLinkedinAccounts"
            :campaign-post-error="campaignPostError"
            :on-save="saveChanges"
            @close="showCampaignPostPortal = false; selectedPost = null; campaignPostError = ''"
        />

    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import { FilterMatchMode } from '@primevue/core/api';


export default {
    name: 'UserPostsCard',

    props: {
        userLinkedinAccounts: {
            type: Array,
            required: true,
        },
        userLinkedinPosts: {
            type: Array,
            required: true,
        },
        campaigns: {
            type: Array,
            required: false,
            default: () => []
        },
    },

    data() {
        return {
            view: { name: 'Tableau' },
            views: [
                { name: 'Tableau' },
                { name: 'Cartes' },
            ],
            postStatus: "queued",
            filters: {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
                name: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
                'country.name': { value: null, matchMode: FilterMatchMode.STARTS_WITH },
                representative: { value: null, matchMode: FilterMatchMode.IN },
                status: { value: null, matchMode: FilterMatchMode.EQUALS },
            },
            showPortal: false,
            selectedCampaign: null,
            showCampaignPostPortal: false,
            selectedAccount: null,
            showCampaignPortal: false,
            selectedPost: null,
            readModeStatus: false,
            campaignPosts: [],
            campaignStartDateTime: null,
            campaignEndDateTime: null,
            isFormValid: false,
            selectedCible: '',
            frequenceParJour: '',
            descriptionCampagne: '',
            couleurCampagne: '',
            nomCampagne: '',
            campaignPostError: '',
            // Variables used For Posts Datatable Filters
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
            // Variables used For Campaigns Datatable Filters
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
            // Paginator Variables
            currentPage: 0,
            rowsPerPage: 10,
        };
    },

    computed: {
        filteredPosts() {
            return this.userLinkedinPosts.filter(post => post.status === this.postStatus);
        },
        queuedPostsCount() {
            return this.userLinkedinPosts.filter(post => post.status === 'queued').length;
        },
        draftPostsCount() {
            return this.userLinkedinPosts.filter(post => post.status === 'draft').length;
        },
        postedPostsCount() {
            return this.userLinkedinPosts.filter(post => post.status === 'posted').length;
        },
        failedPostsCount() {
            return this.userLinkedinPosts.filter(post => post.status === 'failed').length;
        },
        filtredPostsArray() {
            let filtredPosts = this.userLinkedinPosts;

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
        paginatedPosts() {
            const start = this.currentPage * this.rowsPerPage;
            const end = start + this.rowsPerPage;
            return this.filteredPosts.slice(start, end);
        },
    },

    watch: {
        postStatus() {
            this.currentPage = 0;
        },
    },

    methods: {
        // METHOD FOR THE PAGINATOR
        onPageChange(event) {
            this.currentPage = event.page;
            this.rowsPerPage = event.rows;
        },
        // THIS FUNCTION WAS MADE FOR THE ITEMS IN DATATABLE
        getItemsPostsDatatable(post) {
            if(post.status === "queued") {
                return [
                    {
                        label: 'Modifier',
                        icon: 'pi pi-file-edit',
                        command: () => this.editPost(post)
                    },
                    {
                        label: 'Supprimer',
                        icon: 'pi pi-times',
                        command: () => this.deletePost(post.id)
                    },
                ];
            } else if(post.status === "failed") {
                return [
                    {
                        label: 'Supprimer',
                        icon: 'pi pi-times',
                        command: () => this.deletePost(post.id)
                    },
                ];
            } else if(post.status === "posted") {
                return [
                    {
                        label: 'Supprimer de LinkedIn',
                        icon: 'pi pi-times',
                        command: () => this.deletePostFromLinkedIn(post)
                    },
                ];
            }
        },

        getItemsCampaignsDatatable(id) {
            return [
                {
                    label: 'Supprimer',
                    icon: 'pi pi-times',
                    command: () => this.deleteCampaign(id)
                },
            ];
        },

        editPost(post) {
            this.selectedPost = post;
            this.showPortal = true;
        },

        async deletePost(postId) {
            const result = await Swal.fire({
                title: "Vous êtes sûr ?",
                text: "Vous ne pourrez pas revenir en arrière !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Oui, supprimer !",
                cancelButtonText: "Annuler"
            });

            if (result.isConfirmed) {
                try {
                    const post = this.userLinkedinPosts.find(p => p.id === postId);
                    const response = await axios.delete("/linkedin/delete-post", {
                        data: {
                            post_id: postId,
                            linkedin_user_id: post ? post.linkedin_user_id : null,
                        },
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        },
                    });

                    if (response.status === 200) {
                        await Swal.fire({
                            title: "Supprimé !",
                            text: "Votre post a été supprimé.",
                            icon: "success"
                        });
                        window.location.reload();
                    } else {
                        throw new Error("Failed to delete post");
                    }
                } catch (error) {
                    console.error("Error deleting post:", error);
                    await Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Une erreur s'est produite lors de la suppression du post !",
                    });
                }
            }
        },

        async deletePostFromLinkedIn(post) {
            const result = await Swal.fire({
                title: "Vous êtes sûr ?",
                text: "Vous ne pourrez pas revenir en arrière !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Oui, supprimer !",
                cancelButtonText: "Annuler"
            });

            if (result.isConfirmed) {
                try {
                    const urnId = post.post_urn.split(':')[3];
                    const deleteData = new FormData();
                    deleteData.append("post_id", post.id);
                    deleteData.append("linkedin_user_id", post.linkedin_user_id);
                    deleteData.append("urn_id", urnId);

                    const deleteResponse = await axios.delete("/linkedin/post/delete", {
                        data: {
                            post_id: post.id,
                            linkedin_user_id: post.linkedin_user_id,
                            urn_id: urnId
                        },
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        }
                    });

                    if (deleteResponse.status === 200) {
                        await Swal.fire({
                            title: "Supprimé !",
                            text: "Votre post a été supprimé.",
                            icon: "success"
                        });
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                } catch (error) {
                    console.error(error);
                    await Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Une erreur s'est produite lors de la suppression du post !",
                    });
                }
            }
        },

        async deleteCampaign(campaignId) {
            const result = await Swal.fire({
                title: "Vous êtes sûr ?",
                text: "Vous ne pourrez pas revenir en arrière !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Oui, supprimer !",
                cancelButtonText: "Annuler"
            });

            if(result.isConfirmed) {
                try {
                    const response = await axios.delete('/campaign/delete', {
                        data: {
                            campaign_id: campaignId,
                        },
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        },
                    });

                    if(response.status === 200) {
                        await Swal.fire({
                            title: "Campagne supprimé !",
                            text: "Votre campagne a été supprimé avec succès.",
                            icon: "success"
                        });
                        window.location.reload();
                    } else {
                        throw new Error("Failed to delete post");
                    }
                    
                } catch (error) {
                    console.error("Error deleting post:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Une erreur s'est produite lors de la suppression du post !",
                    });
                }
            }
        },

        getLinkedinUserByID(id) {
            return this.userLinkedinAccounts.find(account => account.id === id);
        },

        getPostByID(id) {
            return this.userLinkedinPosts.find(post => post.id === id);
        },

        getUsername(linkedinUserId) {
            const account = this.getLinkedinUserByID(linkedinUserId);
            return account ? `${account.linkedin_firstname} ${account.linkedin_lastname}` : 'inconnu';
        },

        getProfilePicture(linkedinUserId) {
            const account = this.getLinkedinUserByID(linkedinUserId);
            if(account) {
                if(account.linkedin_picture != null) return account.linkedin_picture;
            }
            return '/build/assets/images/default-profile.png';
        },

        getCampaignByID(id) {
            return this.campaigns.find(campaign => campaign.id === id);
        },

        getCampaignName(campaignId) {
            if (!campaignId) return 'UNIQUE';
            const campaign = this.campaigns.find(c => c.id === campaignId);
            return campaign ? campaign.name : 'inconnu';
        },

        getCampaignColor(campaignId) {
            if(campaignId === null) {
                return '#FFFFFF00'
            }
            const campaign = this.campaigns.find(c => c.id === campaignId);
            return campaign ? campaign.color : 'inconnu';
        },

        getCaption(post) {
            if (post.type === 'text') {
                return post.content.text || '';
            } else if (post.type === 'image' || post.type === 'video' || post.type === 'article') {
                return post.content.caption || '';
            }
            return '';
        },

        getFileUrl(filePath) {
            return filePath ? `/linkedin/${filePath}` : '';
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

        translatePostStatus(status) {
            const statusMap = {
                'queued': 'En attente',
                'posted': 'Publié',
                'failed': 'Echoué',
                'draft': 'Brouillons'
            };
            return statusMap[status] || status;
        },

        selectAccount(account) {
            this.selectedAccount = account;
        },

        filterPostsByStatus(status) {
            this.postStatus = status;
        },

        // OPEN POST PORTAL IN READ MODE
        openPostInReadMode(id) {
            this.showPortal = true; 
            this.selectedPost = this.getPostByID(id); 
            this.readModeStatus = true;
        },

        // OPEN CAMPAIGN PORTAL IN READ MODE
        openCampaignInReadMode(linkedin_id, campaign_id) {
            this.showCampaignPortal = true;
            this.selectedAccount = this.getLinkedinUserByID(linkedin_id);
            this.selectedCampaign = this.getCampaignByID(campaign_id);
            this.readModeStatus = true;
        },

        handlePostsGenerated(posts) {
            this.campaignPosts = posts.map(post => ({ ...post }));
        },

        editCampaignPost(post) {
            this.selectedPost = { ...post };
            this.showCampaignPostPortal = true;
        },

        // OPEN CAMPAIGN PORTAL FOR EDIT OR NEW CAMPAIGN
        openCampaignPortal() {
            if (this.selectedAccount) {
                this.showCampaignPortal = true;
                this.isCreatingCampaign = true;
            } else {
                alert("Please select a LinkedIn account first.");
            }
        },

        // CLOSE THE CAMPAIGN PORTAL
        closeCampaignPortal() {
            this.showCampaignPortal = false;
            if (!this.campaignPosts.length) {
                this.resetCampaignState();
            }
        },

        // SAVE CHANGES MADE TO A GENERATED POST
        saveChanges(updatedPost) {
            const now = new Date();
            const scheduled = new Date(updatedPost.scheduledDateTime);
            const start = new Date(this.campaignStartDateTime);
            const end = new Date(this.campaignEndDateTime);

            if (scheduled < now) {
                this.campaignPostError = "La date de publication ne peut pas être dans le passé!";
                return;
            }
            if (scheduled < start || scheduled > end) {
                this.campaignPostError = `La date de publication doit être comprise entre ${this.campaignDateTimeOutput(this.campaignStartDateTime)} et ${this.campaignDateTimeOutput(this.campaignEndDateTime)} !`;
                return;
            }

            if (updatedPost.type === "text" && updatedPost.content.text.trim() === "") {
                this.campaignPostError = "Le contenu du post ne peut pas être vide !";
                return;
            } else if (updatedPost.type === "text" && updatedPost.content.text.length > 3000) {
                this.campaignPostError = "Le contenu du post ne peut pas dépasser 3000 caractères !";
                return;
            } else if (updatedPost.type === "text" && updatedPost.content.text.length < 50) {
                this.campaignPostError = "Le contenu du post doit contenir au moins 50 caractères !";
                return;
            }
            if ((updatedPost.type === "image" || updatedPost.type === "video") && !updatedPost.content.file) {
                this.campaignPostError = "Veuillez sélectionner un fichier pour le publier !";
                return;
            }
            if (updatedPost.type === "article") {
                const { url, title } = updatedPost.content;
                if (!url.trim() || !title.trim()) {
                    this.campaignPostError = "Veuillez remplir au moins l'URL et le titre de l'article.";
                    return;
                } else if (!url.startsWith("https://")) {
                    this.campaignPostError = "L'URL de l'article doit commencer par 'https://'";
                    return;
                } else if (title.length > 200) {
                    this.campaignPostError = "Le titre de l'article ne peut pas dépasser 200 caractères !";
                    return;
                } else if (title.length < 5) {
                    this.campaignPostError = "Le titre de l'article doit contenir au moins 5 caractères !";
                    return;
                }
            }

            const postToSave = { ...updatedPost };
            if (updatedPost.content) {
                postToSave.content = { ...updatedPost.content };
                if (updatedPost.content.file) {
                    postToSave.content.file = updatedPost.content.file;
                }
            }

            const index = this.campaignPosts.findIndex(p => p.tempId === updatedPost.tempId);
            if (index !== -1) {
                this.campaignPosts.splice(index, 1, { ...postToSave });
                if (this.$refs.campaignPortal) {
                    this.$refs.campaignPortal.updatePost({ ...postToSave });
                }
            } else {
                console.error("Post not found in campaignPosts");
            }

            this.showCampaignPostPortal = false;
            this.selectedPost = null;
        },

        updateCampaignDates({ startDate, endDate }) {
            this.campaignStartDateTime = startDate;
            this.campaignEndDateTime = endDate;
        },

        updateFormData(formData) {
            this.campaignStartDateTime = formData.startDate;
            this.campaignEndDateTime = formData.endDate;
            this.selectedCible = formData.selectedCible;
            this.frequenceParJour = formData.frequenceParJour;
            this.descriptionCampagne = formData.descriptionCampagne;
            this.couleurCampagne = formData.couleurCampagne;
            this.nomCampagne = formData.nomCampagne;
        },

        // HUMAN READABLE CAMPAIGN DATES
        campaignDateTimeOutput(date) {
            const dateTime = new Date(date);
            const year = dateTime.getFullYear();
            const month = String(dateTime.getMonth() + 1).padStart(2, '0');
            const day = String(dateTime.getDate()).padStart(2, '0');
            const hours = String(dateTime.getHours()).padStart(2, '0');
            const minutes = String(dateTime.getMinutes()).padStart(2, '0');
            return `${month}/${day}/${year} ${hours}:${minutes}`;
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            
            const daysOfWeek = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
            const dayOfWeek = daysOfWeek[date.getDay()];
            
            const dayOfMonth = date.getDate();
            
            const year = date.getFullYear();
            
            let hours = date.getHours();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            
            const minutes = String(date.getMinutes()).padStart(2, '0');
            
            return `${dayOfWeek} ${dayOfMonth}, ${year} à ${hours}:${minutes}${ampm}`;
        },
    },
}
</script>