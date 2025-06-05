<template>
    <div class="flex flex-col justify-between mt-5 mb-3">
        <!-- Title & Icon -->
        <div class="flex items-center gap-4 mt-2">
            <i class="fa-solid fa-bolt text-4xl"></i>
            <h1 class="fw-semibold text-2xl mb-0">Demandes de Boost d'interaction :</h1>
        </div>

        <div class="p-2 mt-4 rounded-xl" style="background-color: #18181b;">
            <DataTable 
                :value="boostRequests" 
                paginator 
                stripedRows 
                :rows="5" 
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
                                :src="getProfilePicture(this.linkedinAccounts, slotProps.data.linkedin_user_id)" 
                                alt="Profile Picture" 
                                class="rounded-full" 
                                style="width: 32px; height: 32px;" 
                            />
                            <span>{{ getUsername(this.linkedinAccounts, slotProps.data.linkedin_user_id) }}</span>
                        </div>
                    </template>
                </Column>

                <Column header="Nombre des Likes">
                    <template #body="slotProps">
                        <p>{{ slotProps.data.nb_likes }}</p>
                    </template>
                </Column>

                <Column header="Nombre des Commentaires">
                    <template #body="slotProps">
                        <p>{{ slotProps.data.nb_comments }}</p>
                    </template>
                </Column>

                <Column header="Message">
                    <template #body="slotProps">
                        <div class="flex items-center w-full">
                            <button 
                                class="bg-white py-2 px-3 rounded-md fw-semibold text-black"
                                @click="openMessageDialog(slotProps.data.id)"
                            >
                                Voir
                            </button>
                        </div>
                    </template>
                </Column>

                <Column header="Post">
                    <template #body="slotProps">
                        <div class="flex items-center w-full">
                            <button 
                                class="bg-white py-2 px-3 rounded-md fw-semibold text-black"
                                @click="emitOpenPostInReadMode(slotProps.data.post_id)"
                            >
                                Voir
                            </button>
                        </div>
                    </template>
                </Column>

                <Column header="Statut">
                    <template #body="slotProps">
                        <div 
                            class="p-1 rounded-lg text-center"
                            :class="{
                                'bg-blue-600 text-white': slotProps.data.status === 'pending',
                                'bg-green-600 text-white': slotProps.data.status === 'accepted',
                                'bg-red-500 text-white': slotProps.data.status === 'declined',
                            }"
                        >
                            {{ translateBoostRequestStatus(slotProps.data.status) }}
                        </div>
                    </template>
                </Column>

                <Column header="Actions">
                    <template #body="slotProps">
                        <div class="flex items-center w-full gap-2">
                            <button 
                                class="bg-red-500 flex items-center py-2 px-3 rounded-md fw-semibold text-white"
                                @click="deleteBoostRequest(slotProps.data.id)"
                            >
                                <i class="fa-solid fa-trash-can"></i>
                            </button>

                            <button 
                                class="bg-blue-500 py-2 px-3 flex items-center rounded-md fw-semibold text-white"
                                @click="emitBoostRequestToEdit(slotProps.data)"
                            >
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                        </div>
                    </template>
                </Column>
            </DataTable>

            <!-- Single Dialog Outside DataTable -->
            <Dialog 
                v-model:visible="showMessage" 
                maximizable 
                modal 
                header="Message de votre Demande" 
                :style="{ width: '50rem' }" 
                :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            >
                {{ selectedMessage }}
            </Dialog>
        </div>
    </div>
</template>

<script>
import { getProfilePicture, getUsername } from '../services/datatables';
import ApiService from '../services/apiCalls';
import { useToast } from 'vue-toastification';

export default {
    name: 'BoostRequestDatatable',

    props: {
        boostRequests: {
            type: Array,
            required: true,
        },
        linkedinAccounts: {
            type: Array,
            required: true,
        },
    },

    emits: ['open-boosted-post', 'edit-boost-request'],

    setup() {
        const toast = useToast();
        return { toast };
    },

    data() {
        return {
            showMessage: false,
            selectedMessage: '',
        };
    },

    methods: {
        getProfilePicture,
        getUsername,

        translateBoostRequestStatus(status) {
            switch (status) {
                case "pending":
                    return "En attente";
                case "accepted":
                    return "Accepté";
                case "declined":
                    return "Refusé";
                default:
                    return "Inconnu";
            }
        },

        getRequestMessage(requestId) {
            const boost = this.boostRequests.find(request => request.id === requestId);
            return boost ? boost.message : 'Pas de Message';
        },

        openMessageDialog(requestId) {
            this.selectedMessage = this.getRequestMessage(requestId);
            this.showMessage = true;
        },

        emitOpenPostInReadMode(postId) {
            this.$emit('open-boosted-post', postId);
        },

        async deleteBoostRequest(requestId) {
            try {
                // A Call to The API Service I've created
                await ApiService.deleteBoostRequest(requestId);

            } catch(error) {
                console.error("Deletion Failed", error);
                this.toast.error("Une erreur  s'est produite ! Veuillez réessayer");
            }
        },

        emitBoostRequestToEdit(boostRequest) {
            this.$emit('edit-boost-request', boostRequest);
        }
    },
};
</script>