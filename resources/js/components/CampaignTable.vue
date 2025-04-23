<template>
    <div class="w-full my-10">
        <table class="w-full">
            <thead class="bg-blue-500 text-white">
                <tr>
                    <th scope="col">Nom</th>
                    <th scope="col">Description</th>
                    <th scope="col">Audience</th>
                    <th scope="col">Fréquence</th>
                    <th scope="col">Date de Début</th>
                    <th scope="col">Date de Fin</th>
                    <th scope="col">Statut</th>
                    <th scope="col">Couleur</th>
                </tr>
            </thead>
            <tbody>
                <tr 
                    v-for="(campaign, index) in campaigns" 
                    :key="index" 
                    class="hover:bg-gray-200 cursor-pointer transition-all duration-300"
                    @click="openCampaignModal(campaign)"
                >
                    <td>{{ campaign.name }}</td>
                    <td>{{ campaign.description }}</td>
                    <td>{{ campaign.target_audience }}</td>
                    <td>{{ campaign.frequency_per_day }}</td>
                    <td>{{ formattedDate(campaign.start_date) }}</td>
                    <td>{{ formattedDate(campaign.end_date) }}</td>
                    <td>{{ campaign.status }}</td>
                    <td>{{ campaign.color }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <CampaignModal 
        :isOpen="openModal" 
        :campaign="campaignDetails"
        @close="closeCampaignModal"
    />
</template>

<script>
import CampaignModal from './CampaignModal.vue';

export default {
    name: 'CampaignTable',
    components: {
        CampaignModal
    },

    props: {
        campaigns: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            openModal: false,
            campaignDetails: null,
        };
    },

    methods: {
        formattedDate(date) {
            if (!date || isNaN(new Date(date).getTime())) {
                return 'Date invalide';
            }
            const options = { year: 'numeric', month: 'long', day: '2-digit', hour: '2-digit', minute: '2-digit' };
            return new Date(date).toLocaleDateString('fr-FR', options);
        },

        openCampaignModal(campaign) {
            this.campaignDetails = campaign;
            this.openModal = true;
        },

        closeCampaignModal() {
            this.openModal = false;
            this.campaignDetails = null;
        }
    }
}
</script>

<style>
table thead th {
    padding: 20px 30px;
    text-align: center;
}

table tbody tr {
    text-align: center;
}

table tbody tr td {
    padding: 20px 10px;
    font-weight: 500;
}

table tbody {
    border: 1px solid #dddddda9;
}
</style>