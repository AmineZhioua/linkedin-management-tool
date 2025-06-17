<template>
    <div class="flex flex-col justify-between mt-4 mb-3">
        <!-- Title & Icon -->
        <div class="flex items-center gap-3 mt-2">
            <img 
                src="/build/assets/icons/profile-black.svg" 
                alt="Accounts Icon"
                height="50"
                width="50"
            />
            <h1 class="fw-semibold text-2xl mb-0">Vos Comptes Linkedin :</h1>
        </div>

        <div class="p-2 mt-4 rounded-xl" style="background-color: #18181b;">
            <DataTable 
                :value="userLinkedinAccounts" 
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
                                :src="getProfilePicture(userLinkedinAccounts, slotProps.data.id)" 
                                alt="Profile Picture" 
                                class="rounded-full" 
                                style="width: 50px; height: 50px;" 
                            />
                            <span>{{ getUsername(userLinkedinAccounts, slotProps.data.id) }}</span>
                        </div>
                    </template>
                </Column>

                <Column header="Ajouté en">
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            {{ formatDateWithMonth(slotProps.data.created_at) }}
                        </div>
                    </template>
                </Column>

                <Column header="Expiration de Jeton">
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            {{ formatDateWithMonth(slotProps.data.expire_at) }}
                        </div>
                    </template>
                </Column>

                <Column header="Actions">
                    <template #body="slotProps">
                        <div class="flex items-center w-full gap-2">
                            <button 
                                class="bg-red-500 flex items-center py-2 px-3 rounded-md fw-semibold text-white"
                                @click="deleteLinkedinAccount(slotProps.data.id)"
                            >
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </template>
                </Column>
            </DataTable>

        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { getProfilePicture, getUsername, formatDateWithMonth } from '../services/datatables';
import Swal from 'sweetalert2';
import { useToast } from 'vue-toastification';

export default {
    name: 'UsersDatatable',

    props: {
        userLinkedinAccounts: {
            type: Array,
            required: true,
        },
    },

    setup() {
        const toast = useToast();
        return { toast };
    },

    methods: {
        getProfilePicture,
        getUsername,
        formatDateWithMonth,


        async deleteLinkedinAccount(accountId) {
            const result = await Swal.fire({
                title: "Vous êtes sûr ?",
                text: "Tous vos posts et campagnes seront supprimés seulement sur notre plateforme. Cette action est irréversible !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Oui, supprimer !",
                cancelButtonText: "Annuler"
            });

            if(result.isConfirmed) {
                try {
                    const deletionResponse = await axios.delete("/linkedin/account/delete", {
                        data: {
                            linkedin_id: accountId
                        },
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        }
                    });

                    if(deletionResponse.status === 200) {
                        this.toast.success("Votre compte a été supprimé avec succès !");
                    } else {
                        this.toast.error("Une erreur s'est produite ! Réessayer plus tard");
                    }
                } catch(error) {
                    this.toast.error("Une erreur s'est produite !");
                }
            }
        },
    }
};
</script>