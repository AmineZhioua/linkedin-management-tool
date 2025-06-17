<template>
    <div class="fixed bottom-10 right-10 z-50">
        <div class="relative p-4 bg-white shadow-md rounded-full border cursor-pointer" @click="toggleAlerts">
            <button>
                <i class="fa-solid fa-circle-exclamation text-2xl hover:text-red-500 transition-all duration-300"></i>
            </button>

            <!-- Alerts List -->
            <div 
                v-if="showAlertsDropdown"
                class="bg-white rounded-md absolute bottom-[90px] right-[60px] shadow-lg overflow-y-scroll max-h-[350px] flex flex-col"
            >
                <!-- Message if there are no alerts -->
                <div v-if="alertsArray.length === 0" class="p-4 text-center min-w-[200px] flex justify-center">
                    <p class="mb-0 text-muted">Aucun alerte pour le moment</p>
                </div>

                <div 
                    v-for="alert in alertsArray"
                    class="py-4 px-2 min-w-[300px] cursor-pointer hover:bg-gray-200 transition-all duration-300 border-t-black border-t-2 flex flex-col gap-2"
                >
                    <div class="flex w-full items-center justify-between">
                        <!-- Related Linkedin Account -->
                        <div class="flex items-center gap-2">
                            <div class="h-[35px] w-[35px] rounded-full border flex items-center justify-center bg-black">
                                <i class="fa-solid fa-user-tie text-white text-xl"></i>
                            </div>
                            <p class="text-muted text-sm mb-0">Admin</p>
                        </div>
                        <!-- Badge for to display if something FAILS -->
                        <div 
                            class="bg-red-500 rounded-xl py-1 px-2 w-fit"
                        >
                            <p class="mb-0 text-white text-xs">Alerte</p>
                        </div>
                    </div>

                    <!-- Alert Message -->
                    <div class="flex items-center justify-between gap-3 px-1">
                        <ScrollPanel style="max-width: 700px; height: 80px;" class="p-2 rounded-lg border w-full text-white mt-2">
                            <p class="text-black text-sm mb-0 ml-2">{{ alert.message }}</p>
                        </ScrollPanel>
                    </div>

                    <p class="text-xs text-muted px-2 mb-0">Jusqu'à: {{ formatDateWithMonth(alert.end_at) }}</p>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import axios from 'axios';
import { useToast } from 'vue-toastification';
import { formatDateWithMonth } from '../services/datatables';
import { ScrollPanel } from 'primevue';

export default {
    name: 'AlertsBell',

    setup() {
        const toast = useToast();
        return { toast };
    },

    data() {
        return {
            alertsArray: [],
            showAlertsDropdown: false,
        };  
    },

    mounted() {
        this.getAllAlerts();
    },

    methods: {
        formatDateWithMonth,

        toggleAlerts() {
            this.showAlertsDropdown = !this.showAlertsDropdown;
        },

        async getAllAlerts() {
            try {
                const alertResponse = await axios.get('/alerts/all', {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                });

                if(alertResponse.status === 200) {
                    alertResponse.data.alerts.forEach(alert => {
                        this.alertsArray.push(alert);
                    });
                }
            } catch(error) {
                this.toast.error("Une erreur s'est produite lors de la chargement des alertes");
                console.log(error);
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