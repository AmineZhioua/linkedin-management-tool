<template>
    <div class="w-full h-[100vh] flex flex-col gap-4">
        <loading-overlay :is-loading="isLoading" message="Envoi en cours..." />
        <div class="flex flex-col">
            <label for="image">Ajouter une image : </label>
            <!-- Image Preview -->
            <div class="flex justify-center mb-2">
                <img 
                    :src="imagePreview" 
                    alt="" 
                    class="preview relative w-[200px] h-[200px] rounded-full object-cover border" 
                />
            </div>
            <input 
                type="file" 
                class="custom-file-input p-2 border" 
                id="image" 
                name="image" 
                @change="handleFileUpload($event)"
            />
        </div>

        <div class="flex flex-col">
            <label for="profession">Profession* :</label>
            <input 
                v-model="userProfession"
                type="text" 
                class="border" 
                name="profession" 
                id="profession"
            />
        </div>

        <div class="flex flex-col">
            <label for="adresse">Adresse* :</label>
            <input 
                v-model="userAdresse"
                type="text" 
                class="border" 
                name="adresse" 
                id="adresse" 
            />
        </div>

        <div class="flex flex-col">
            <label for="numero-telephone">Numéro de Téléphone* :</label>
            <div class="flex items-center relative">
                <div 
                    class="border p-2 gap-3 cursor-pointer flex items-center justify-between"
                    @click="toggleDropdown"
                >
                    <i class="fa-solid fa-chevron-down text-black"></i>
                    <span v-if="selectedCountry">
                        <img 
                            :src="getFlagImage(selectedCountry)" 
                            :alt="selectedCountry" 
                            height="20"
                            width="20"
                            class="inline-block mr-2"
                        />
                        {{ countries.find(c => c.code === selectedCountry).name }} ({{ countries.find(c => c.code === selectedCountry).dialCode }})
                    </span>
                    <span v-else>Select a country</span>
                </div>
                <!-- Dropdown List -->
                <div 
                    v-if="isDropdownOpen"
                    class="absolute bg-white border top-[40px] shadow-lg max-h-60 overflow-y-auto z-10"
                >
                    <div
                        v-for="country in countries"
                        :key="country.code"
                        @click="selectCountry(country.code)"
                        class="p-2 hover:bg-gray-100 cursor-pointer flex items-center"
                    >
                        <img 
                            :src="getFlagImage(country.code)" 
                            :alt="country.code" 
                            height="20"
                            width="20"
                            class="mr-2"
                        />
                        {{ country.name }} ({{ country.dialCode }})
                    </div>
                </div>
                <input 
                    v-model="userPhoneNumber"
                    type="text" 
                    class="border p-2 flex-1" 
                    name="numero-telephone" 
                    id="numero-telephone" 
                />
            </div>
        </div>

        <div class="flex items-center gap-2">
            <div class="flex flex-col">
                <label for="nom-entreprise">Nom de l'Entreprise* :</label>
                <input 
                    v-model="nomEntreprise"
                    type="text" 
                    class="border" 
                    name="nom-entreprise" 
                    id="nom-entreprise" 
                />
            </div>
            <div class="flex flex-col">
                <label for="adresse-entreprise">Adresse de l'Entreprise* :</label>
                <input 
                    v-model="adresseEntreprise"
                    type="text" 
                    class="border" 
                    name="adresse-entreprise" 
                    id="adresse-entreprise" 
                />
            </div>
        </div>

        <button 
            @click="submitInfo"
            id="submitBtn"
            class="px-4 py-2 bg-blue-500 text-white fw-semibold rounded-full"
        >
            Envoyer
        </button>
    </div>
</template>

<script>
import axios from 'axios';
import { useToast } from "vue-toastification";
import { parsePhoneNumber, isValidPhoneNumber } from 'libphonenumber-js';

export default {
    name: 'ExtraInfoForm',

    setup() {
        const toast = useToast();
        return { toast };
    },

    data() {
        return {
            userImage: null,
            isLoading: false,
            imagePreview: null,
            userProfession: '',
            userAdresse: '',
            userPhoneNumber: '',
            nomEntreprise: '',
            adresseEntreprise: '',
            selectedCountry: 'FR',
            isDropdownOpen: false,
            countries: [
                { name: "United States", code: "US", dialCode: "+1" },
                { name: "United Kingdom", code: "GB", dialCode: "+44" },
                { name: "Afghanistan", code: "AF", dialCode: "+93" },
                { name: "Albania", code: "AL", dialCode: "+355" },
                { name: "Algeria", code: "DZ", dialCode: "+213" },
                { name: "Andorra", code: "AD", dialCode: "+376" },
                { name: "Angola", code: "AO", dialCode: "+244" },
                { name: "Argentina", code: "AR", dialCode: "+54" },
                { name: "Armenia", code: "AM", dialCode: "+374" },
                { name: "Australia", code: "AU", dialCode: "+61" },
                { name: "Austria", code: "AT", dialCode: "+43" },
                { name: "Azerbaijan", code: "AZ", dialCode: "+994" },
                { name: "Bahamas", code: "BS", dialCode: "+1" },
                { name: "Bahrain", code: "BH", dialCode: "+973" },
                { name: "Bangladesh", code: "BD", dialCode: "+880" },
                { name: "Barbados", code: "BB", dialCode: "+1" },
                { name: "Belarus", code: "BY", dialCode: "+375" },
                { name: "Belgium", code: "BE", dialCode: "+32" },
                { name: "Belize", code: "BZ", dialCode: "+501" },
                { name: "Benin", code: "BJ", dialCode: "+229" },
                { name: "Bhutan", code: "BT", dialCode: "+975" },
                { name: "Bolivia", code: "BO", dialCode: "+591" },
                { name: "Brazil", code: "BR", dialCode: "+55" },
                { name: "Bulgaria", code: "BG", dialCode: "+359" },
                { name: "Cambodia", code: "KH", dialCode: "+855" },
                { name: "Cameroon", code: "CM", dialCode: "+237" },
                { name: "Canada", code: "CA", dialCode: "+1" },
                { name: "Chile", code: "CL", dialCode: "+56" },
                { name: "China", code: "CN", dialCode: "+86" },
                { name: "Colombia", code: "CO", dialCode: "+57" },
                { name: "Costa Rica", code: "CR", dialCode: "+506" },
                { name: "Croatia", code: "HR", dialCode: "+385" },
                { name: "Cuba", code: "CU", dialCode: "+53" },
                { name: "Cyprus", code: "CY", dialCode: "+357" },
                { name: "Czech Republic", code: "CZ", dialCode: "+420" },
                { name: "Denmark", code: "DK", dialCode: "+45" },
                { name: "Egypt", code: "EG", dialCode: "+20" },
                { name: "Estonia", code: "EE", dialCode: "+372" },
                { name: "Ethiopia", code: "ET", dialCode: "+251" },
                { name: "Finland", code: "FI", dialCode: "+358" },
                { name: "France", code: "FR", dialCode: "+33" },
                { name: "Germany", code: "DE", dialCode: "+49" },
                { name: "Ghana", code: "GH", dialCode: "+233" },
                { name: "Greece", code: "GR", dialCode: "+30" },
                { name: "Hong Kong", code: "HK", dialCode: "+852" },
                { name: "Hungary", code: "HU", dialCode: "+36" },
                { name: "Iceland", code: "IS", dialCode: "+354" },
                { name: "India", code: "IN", dialCode: "+91" },
                { name: "Indonesia", code: "ID", dialCode: "+62" },
                { name: "Iran", code: "IR", dialCode: "+98" },
                { name: "Iraq", code: "IQ", dialCode: "+964" },
                { name: "Ireland", code: "IE", dialCode: "+353" },
                { name: "Italy", code: "IT", dialCode: "+39" },
                { name: "Jamaica", code: "JM", dialCode: "+1" },
                { name: "Japan", code: "JP", dialCode: "+81" },
                { name: "Jordan", code: "JO", dialCode: "+962" },
                { name: "Kenya", code: "KE", dialCode: "+254" },
                { name: "Korea, South", code: "KR", dialCode: "+82" },
                { name: "Kuwait", code: "KW", dialCode: "+965" },
                { name: "Lebanon", code: "LB", dialCode: "+961" },
                { name: "Libya", code: "LY", dialCode: "+218" },
                { name: "Malaysia", code: "MY", dialCode: "+60" },
                { name: "Maldives", code: "MV", dialCode: "+960" },
                { name: "Mexico", code: "MX", dialCode: "+52" },
                { name: "Morocco", code: "MA", dialCode: "+212" },
                { name: "Netherlands", code: "NL", dialCode: "+31" },
                { name: "New Zealand", code: "NZ", dialCode: "+64" },
                { name: "Nigeria", code: "NG", dialCode: "+234" },
                { name: "Norway", code: "NO", dialCode: "+47" },
                { name: "Pakistan", code: "PK", dialCode: "+92" },
                { name: "Panama", code: "PA", dialCode: "+507" },
                { name: "Peru", code: "PE", dialCode: "+51" },
                { name: "Philippines", code: "PH", dialCode: "+63" },
                { name: "Poland", code: "PL", dialCode: "+48" },
                { name: "Portugal", code: "PT", dialCode: "+351" },
                { name: "Qatar", code: "QA", dialCode: "+974" },
                { name: "Romania", code: "RO", dialCode: "+40" },
                { name: "Russia", code: "RU", dialCode: "+7" },
                { name: "Saudi Arabia", code: "SA", dialCode: "+966" },
                { name: "Singapore", code: "SG", dialCode: "+65" },
                { name: "South Africa", code: "ZA", dialCode: "+27" },
                { name: "Spain", code: "ES", dialCode: "+34" },
                { name: "Sweden", code: "SE", dialCode: "+46" },
                { name: "Switzerland", code: "CH", dialCode: "+41" },
                { name: "Taiwan", code: "TW", dialCode: "+886" },
                { name: "Thailand", code: "TH", dialCode: "+66" },
                { name: "Turkey", code: "TR", dialCode: "+90" },
                { name: "Tunisia", code: "TN", dialCode: "+216" },
                { name: "Ukraine", code: "UA", dialCode: "+380" },
                { name: "United Arab Emirates", code: "AE", dialCode: "+971" },
                { name: "Vietnam", code: "VN", dialCode: "+84" },
                { name: "Zimbabwe", code: "ZW", dialCode: "+263" }
            ],
        };
    },

    methods: {
        getFlagImage(code) {
            return `https://flagcdn.com/w20/${code.toLowerCase()}.png`;
        },

        toggleDropdown() {
            this.isDropdownOpen = !this.isDropdownOpen;
        },

        selectCountry(code) {
            this.selectedCountry = code;
            this.isDropdownOpen = false;
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.userImage = file;
                this.imagePreview = URL.createObjectURL(file);
            } else {
                this.userImage = null;
                this.imagePreview = null;
            }
        },

        validatePhoneNumber() {
            if (!this.userPhoneNumber || !this.selectedCountry) {
                return false;
            }
            try {
                const fullNumber = `${this.countries.find(c => c.code === this.selectedCountry).dialCode}${this.userPhoneNumber}`;
                const parsedNumber = parsePhoneNumber(fullNumber, this.selectedCountry);
                return parsedNumber && isValidPhoneNumber(fullNumber, this.selectedCountry);
            } catch (e) {
                return false;
            }
        },

        async submitInfo() {
            if (!this.userImage || !(this.userImage instanceof File)) {
                this.toast.error("Veuillez sélectionner une image.");
                return;
            }
            if (!this.userProfession) {
                this.toast.error("Veuillez remplir le champ Profession.");
                return;
            }
            if (!this.userAdresse) {
                this.toast.error("Veuillez remplir le champ Adresse.");
                return;
            }
            if (!this.userPhoneNumber) {
                this.toast.error("Veuillez remplir le champ Numéro de Téléphone.");
                return;
            }
            if (!this.validatePhoneNumber()) {
                this.toast.error("Veuillez entrer un numéro de téléphone valide pour le pays sélectionné.");
                return;
            }
            if (!this.nomEntreprise) {
                this.toast.error("Veuillez remplir le champ Nom de l'Entreprise.");
                return;
            }
            if (!this.adresseEntreprise) {
                this.toast.error("Veuillez remplir le champ Adresse de l'Entreprise.");
                return;
            }

            this.isLoading = true;
            try {
                const infoData = new FormData();
                infoData.append("user_image", this.userImage);
                infoData.append("profession", this.userProfession);
                infoData.append("adresse", this.userAdresse);
                infoData.append("telephone", this.userPhoneNumber);
                infoData.append("nom_entreprise", this.nomEntreprise);
                infoData.append("adresse_entreprise", this.adresseEntreprise);
                // infoData.append("country_code", this.selectedCountry);

                const infoResponse = await axios.post('/extra-info/add', infoData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                });

                if (infoResponse.data.status === 201) {
                    this.toast.success("Vos informations ont été enregistrées avec succès !", {
                        position: "top-right",
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

                    document.getElementById("submitBtn").disabled = true;

                    setTimeout(() => {
                        window.location.href = "/dashboard/linkedin"; 
                    }, 2000);
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    const errors = error.response.data.errors;
                    for (let field in errors) {
                        this.toast.error(errors[field][0]);
                    }
                } else {
                    console.error(error);
                    this.toast.error("Une erreur s'est produite lors de l'envoi !");
                }
            } finally {
                this.isLoading = false;
            }
        },
    },
}
</script>

<style scoped>
/* .custom-file-input::-webkit-file-upload-button {
  visibility: hidden;
} */

.preview::before {
    content: "Image Preview";
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    font-weight: 500;
    color: black;
}
</style>