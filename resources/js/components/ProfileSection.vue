<template>
    <div class="w-full h-full p-4 overflow-y-scroll">
        <div class="flex items-center w-full justify-between pb-4 mb-4" style="border-bottom: 1px solid #ddd;">
            <div class="flex items-center gap-4">
                <img 
                    :src="userAdditionalInfo.user_image ? '/storage/' + userAdditionalInfo.user_image : '/build/assets/images/default-profile.png'" 
                    alt="Profile Picture" 
                    height="120"
                    width="120"
                    class="rounded-full"
                />
                <div class="flex flex-col gap-1">
                    <h4 class="mb-0 fw-semibold">{{ user.name }}</h4>
                    <p class="text-muted mb-0">Crée le: {{ formatDateWithMonth(user.created_at) }}</p>
                    <p class="mb-0 text-muted fw-semibold">Rôle : <span class="text-black">{{ user.role === 'user' ? 'Utilisateur' : 'Administrateur' }}</span></p>
                </div>
            </div>

            <button 
                @click="deleteAccount(user.id)"
                class="bg-red-500 flex items-center gap-2 text-white p-2 rounded-lg shadow-md"
            >
                <i class="fa-solid fa-trash-can"></i>
                <p class="mb-0 fw-semibold">Supprimer votre compte</p>
            </button>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <extra-info-form
                :user-additional-info="userAdditionalInfo"
            />

            <div class="flex flex-col mt-4 p-2">
                <h1 class="text-2xl text-black fw-semibold mb-4">Détails de votre Abonnement :</h1>
                <div class="flex flex-col gap-2">
                    <!-- Fixed subscription display -->
                    <div 
                        v-if="currentSubscription" 
                        class="bg-white p-4 rounded-lg flex flex-col justify-between min-w-[300px] min-h-[400px]"
                        style="border: 2px solid black;"
                    >
                        <h5 class="fw-semibold mb-2">{{ currentSubscription.name }}</h5>
                        <p class="text-muted mb-1">{{ currentSubscription.description }}</p>
                        <p class="mb-1"><strong>Prix payé:</strong> {{ currentSubscription.monthly_price }}€</p>
                        <p class="mb-1"><strong>Posts disponibles:</strong> {{ currentSubscription.available_posts }}</p>
                        <p class="mb-1"><strong>Commentaire à Booster:</strong> {{ currentSubscription.boost_comments }}</p>
                        <p class="mb-1"><strong>Likes à Booster:</strong> {{ currentSubscription.boost_likes }}</p>
                        <p class="mb-1"><strong>LinkedIn:</strong> {{ currentSubscription.linkedin ? 'Activé' : 'Désactivé' }}</p>
                        <p class="mb-1"><strong>Date d'achat:</strong> {{ formatDateWithMonth(userSubscription.created_at) }}</p>
                        <p class="mb-1"><strong>Date d'expiration:</strong> {{ formatDateToFrenchWithDay(userSubscription.date_expiration) }}</p>
                    </div>
                    <div v-else class="bg-gray-100 p-4 rounded-lg">
                        <p class="mb-0 text-muted">Aucun abonnement trouvé</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { formatDate, formatDateWithMonth } from '../services/datatables';
import Swal from 'sweetalert2';

export default {
    name: 'ProfileSection',

    props: {
        user: {
            type: Object,
            required: true,
        },

        userAdditionalInfo: {
            type: Object,
            required: true,
            default: {},
        },

        userSubscription: {
            type: Object,
            required: true,
        },

        subscriptions: {
            type: Array,
            required: true,
        }
    },

    data() {
        return {
            userActiveSubscription: null,
        };
    },

    computed: {
        currentSubscription() {
            if (!this.userSubscription?.subscription_id || !this.subscriptions?.length) {
                return null;
            }
            
            return this.subscriptions.find(sub => sub.id === this.userSubscription.subscription_id) || null;
        }
    },

    methods: {
        formatDateWithMonth,
        formatDateToFrenchWithDay(dateString) {
            if (!dateString) return '';
            
            const date = new Date(dateString);
            
            // Check if date is valid
            if (isNaN(date.getTime())) return '';
            
            const days = [
                'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 
                'Jeudi', 'Vendredi', 'Samedi'
            ];
            
            const months = [
                'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
            ];
            
            const dayName = days[date.getDay()];
            const day = date.getDate();
            const monthName = months[date.getMonth()];
            const year = date.getFullYear();
            
            return `${dayName} ${day}, ${monthName} ${year}`;
        },

        async deleteAccount(userId) {
            const result = await Swal.fire({
                title: "Vous êtes sûr ?",
                text: "Tous vos données sur notre plate-forme seront perdus. Cette action est irréversible !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Oui, supprimer !",
                cancelButtonText: "Annuler"
            });

            if(result.isConfirmed) {
                try {
                    const deletionResponse = await axios.delete('/profile/delete-account', {
                        data: {
                            user_id: userId
                        },
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        }
                    });

                    if (deletionResponse.status === 200) {
                        await Swal.fire({
                            title: "Supprimé !",
                            text: "Votre compte a été supprimé.",
                            icon: "success"
                        });
                        window.location.reload();
                    }
                } catch(error) {
                    console.error('Error while deleting user:', error);
                    this.toast.error("Une erreur s'est produite lors de la suppression du compte !", {
                        position: "bottom-right",
                        timeout: 2000,
                        closeOnClick: true,
                        pauseOnFocusLoss: true,
                        pauseOnHover: true,
                        draggable: true,
                        draggablePercent: 0.6,
                        showCloseButtonOnHover: false,
                        hideProgressBar: false,
                        closeButton: "button",
                        icon: true,
                        rtl: false
                    });
                }
            }
        },
    },
};
</script>

<style scoped>
::-webkit-scrollbar {
    width: 6px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
.no-scroll {
    overflow: hidden;
}
::-webkit-scrollbar-thumb {
    background: transparent;
    border-radius: 1px;
}
</style>