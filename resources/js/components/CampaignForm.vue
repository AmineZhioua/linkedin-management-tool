<template>
    <div class="w-[80%]">
        <h1 class="text-center text-lg font-bold mb-4">Formulaire du Campagne</h1>
        <form>
            <!-- Durée du Campagne -->
            <div class="flex flex-col md:flex-row justify-between gap-4 mb-4">
                <!-- Date de Debut -->
                <div class="w-full">
                    <label for="startDate" class="block text-md mb-2">
                        Date de Début <span class="text-red-500">*</span> :
                    </label>
                    <input
                        type="datetime-local"
                        id="startDate"
                        v-model="startDate"
                        class="w-full border rounded-lg p-2 mb-1"
                        :class="{'border-red-500': startDateErrorMessage}"
                        :min="todayDate"
                        @change="updateDates"
                    />
                    <p v-if="startDateErrorMessage" class="text-red-500 text-sm mb-2">
                        {{ startDateErrorMessage }}
                    </p>
                </div>
        
                <!-- Date de Fin -->
                <div class="w-full">
                    <label for="endDate" class="block text-md mb-2">
                    Date de Fin <span class="text-red-500">*</span> :
                    </label>
                    <input
                        type="datetime-local"
                        id="endDate"
                        v-model="endDate"
                        class="w-full border rounded-lg p-2 mb-1"
                        :class="{'border-red-500': endDateErrorMessage}"
                        :min="startDate"
                    />
                    <p v-if="endDateErrorMessage" class="text-red-500 text-sm mb-2">
                    {{ endDateErrorMessage }}
                    </p>
                </div>
            </div>
    
            <!-- Audience Cible -->
            <div class="mb-4">
                <label for="cible" class="block text-md mb-2">
                    Choissisez votre Audience cible <span class="text-red-500">*</span> :
                </label>
                <select
                    id="cible"
                    v-model="selectedCible"
                    class="w-full border rounded-lg p-2 mb-1"
                    :class="{'border-red-500': cibleErrorMessage}"
                >
                    <option value="" disabled>Choisissez votre audience</option>
                    <option v-for="cible in cibles" :key="cible.id" :value="cible.id">
                        {{ cible.name }}
                    </option>
                </select>
                <p v-if="cibleErrorMessage" class="text-red-500 text-sm mb-2">
                    {{ cibleErrorMessage }}
                </p>
            </div>
    
            <!-- Frequence des Posts -->
            <div class="mb-4">
                <label for="frequence" class="block text-md mb-2">Fréquence des Posts <span style="color: red;">*</span> :</label>
                <div class="relative">
                    <input
                        type="number"
                        id="frequence"
                        v-model="frequenceParJour"
                        class="w-full border rounded-lg p-2 px-4"
                        placeholder="Nombre de Posts"
                        min="1"
                        max="10"
                    />
                    <button
                        class="bg-black text-white absolute right-0 top-0 bottom-0 py-2 px-3"
                        style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;"
                        disabled
                    >
                        par Jour
                    </button>
                </div>
            </div>

            <div class="flex items-center flex-wrap gap-5">
                <!-- Nom de Campagne -->
                <div class="mb-2 flex-1">
                    <label for="couleurCampgne" class="block text-md mb-2">Nom de Campagne <span style="color: red;">*</span> :</label>
                    <input
                        type="text"
                        id="nomCampagne"
                        v-model="nomCampagne"
                        placeholder="e.g: Campagne de Noël"
                        class="min-w-[300px] w-full h-[50px] border rounded-xl p-3 bg-white mb-1"
                    />
                </div>
                <!-- Couleur du Campagne -->
                <div class="mb-2">
                    <label for="couleurCampgne" class="block text-md mb-2">Choissisez un couleur spécial <span style="color: red;">*</span> :</label>
                    <input
                        type="color"
                        id="couleurCampgne"
                        v-model="couleurCampagne"
                        class="min-w-[300px] max-w-[500px] h-[50px] border rounded-xl p-2 bg-white mb-1"
                    />
                </div>
            </div>

            <!-- Description du Campagne -->
            <div class="mb-4">
                <label for="descriptionCampagne" class="block text-md mb-2">Description <span style="color: red;">*</span> :</label>
                <textarea
                    id="descriptionCampagne"
                    v-model="descriptionCampagne"
                    class="w-full border rounded-lg p-3 h-32"
                    placeholder="Décrivez votre Campagne ici..."
                    min="10"
                    max="500"
                ></textarea>

                <p v-if="descriptionErrorMessage" class="text-red-500 text-sm mb-2">
                    {{ descriptionErrorMessage }}
                </p>
            </div>
        </form>
    </div>
  </template>
  
<script>
export default {
    name: 'CampaignForm',
    props: {
        initialStartDate: {
            type: String,
        },
        initialEndDate: {
            type: String,
        },
        initialCible: {
            type: String,
        },
        initialFrequence: {
            type: Number,
        },
        initialDescription: {
            type: String,
        },
        initialCouleur: {
            type: String,
        },
        initialNom: {
            type: String,
        },
        cibles: {
            type: Array,
            default: [],
        }
    },
  
    data() {
        const today = new Date();
        const tomorrow = new Date();
        tomorrow.setDate(today.getDate() + 1);
    
        return {
            todayDate: this.formatDateTime(today),
            startDate: this.initialStartDate || this.formatDateTime(today),
            endDate: this.initialEndDate || this.formatDateTime(tomorrow),
            selectedCible: this.initialCible || '',
            frequenceParJour: this.initialFrequence,
            descriptionCampagne: this.initialDescription,
            cibleErrorMessage: '',
            descriptionErrorMessage: '',
            couleurCampagne: this.initialCouleur || '#fff000',
            nomCampagne: '',
        };
    },
  
    computed: {
        isStartDateValid() {
            if (!this.startDate) return false;
            
            const selectedStart = new Date(this.startDate);
            const now = new Date();
            
            const minStartTime = new Date(now);
            minStartTime.setHours(minStartTime.getHours());
            
            return selectedStart >= minStartTime;
        },
        
        isEndDateValid() {
            if (!this.endDate || !this.startDate) return false;
            
            const selectedStart = new Date(this.startDate);
            const selectedEnd = new Date(this.endDate);
            
            const minEndTime = new Date(selectedStart);
            minEndTime.setHours(minEndTime.getHours() + 2);
            
            return selectedEnd >= minEndTime;
        },
        
        startDateErrorMessage() {
            if (!this.startDate) return "Date de début requise";
            if (!this.isStartDateValid) {
                const now = new Date();
                const minStartTime = new Date(now);
                minStartTime.setHours(minStartTime.getHours() + 1);
                return `La date de début doit être au moins 1 heure après maintenant (${this.formatReadableDateTime(minStartTime)})`;
            }
            return "";
        },
        
        endDateErrorMessage() {
            if (!this.endDate) return "Date de fin requise";
            if (!this.isEndDateValid) {
                const selectedStart = new Date(this.startDate);
                const minEndTime = new Date(selectedStart);
                minEndTime.setHours(minEndTime.getHours() + 2);
                return `La date de fin doit être au moins 2 heures après la date de début (${this.formatReadableDateTime(minEndTime)})`;
            }
            return "";
        },

        isDescriptionValid() {
            if(this.descriptionCampagne.trim() === "") {
                this.descriptionErrorMessage = "La description ne peut pas être vide";
                return false;
            } else if (this.descriptionCampagne.length < 10) {
                this.descriptionErrorMessage = "La description doit contenir au moins 10 caractères";
                return false;
            } else if (this.descriptionCampagne.length > 500) {
                this.descriptionErrorMessage = "La description ne peut pas dépasser 500 caractères";
                return false;
            } else {
                this.descriptionErrorMessage = "";
                return true;
            }
        },
        
        isFormValid() {
            return this.isStartDateValid && 
                this.isEndDateValid && 
                this.selectedCible && 
                this.couleurCampagne &&
                this.nomCampagne &&
                this.isDescriptionValid &&
                this.frequenceParJour >= 1 && 
                this.frequenceParJour <= 10;
        },
        
        formData() {
            return {
                startDate: this.startDate,
                endDate: this.endDate,
                selectedCible: this.selectedCible,
                frequenceParJour: this.frequenceParJour,
                descriptionCampagne: this.descriptionCampagne,
                couleurCampagne: this.couleurCampagne,
                nomCampagne: this.nomCampagne,
            };
        },
    },
  
    watch: {
      formData: {
        deep: true,
        handler(newVal) {
          this.$emit('update:form-data', newVal);
          this.$emit('validate', this.isFormValid);
        }
      }
    },
  
    methods: {
        formatDateTime(date) {
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${date.toISOString().split('T')[0]}T${hours}:${minutes}`;
        },
  
        formatReadableDateTime(date) {
            const options = {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return date.toLocaleDateString('fr-FR', options).replace(',', ' à');
        },
      
        updateDates() {
            const now = new Date();
            this.todayDate = this.formatDateTime(now);
            
            if (this.startDate && (!this.endDate || new Date(this.endDate) < new Date(this.startDate))) {
                const suggestedStart = new Date(this.startDate);
                const suggestedEnd = new Date(suggestedStart);
                suggestedEnd.setHours(suggestedEnd.getHours() + 2);
                this.endDate = this.formatDateTime(suggestedEnd);
            }
    
            this.$emit('dates-updated', {
                startDate: this.startDate,
                endDate: this.endDate
            });
        },
    }
};
</script>