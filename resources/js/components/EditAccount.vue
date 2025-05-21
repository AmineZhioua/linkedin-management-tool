<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="relative bg-white p-6 rounded-lg shadow-lg w-full max-w-4xl mx-4">
            <!-- Close Button -->
            <button @click="handleClose" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 transition-colors duration-200">
                <i class="fas fa-times text-2xl"></i>
            </button>
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Profile Edit Form -->
                <div class="w-full">
                    <h3 class="text-2xl font-bold mb-4">Gérer mon compte</h3>
                    <!-- Tab Navigation -->
                    <div class="flex mb-4 border-b">
                        <button 
                            @click="activeTab = 'profile'" 
                            class="py-2 px-4 text-base font-medium transition-colors duration-200"
                            :class="activeTab === 'profile' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'"
                        >
                            Modifier le Compte
                        </button>
                        <button 
                            @click="activeTab = 'extraInfo'" 
                            class="py-2 px-4 text-base font-medium transition-colors duration-200"
                            :class="activeTab === 'extraInfo' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'"
                        >
                            Informations Supplémentaires
                        </button>
                    </div>
                    <!-- Alerts -->
                    <div class="mb-4">
                        <p v-if="errorMessage" class="bg-red-100 text-red-700 p-4 rounded-lg mb-4 flex items-center">
                            <i class="fa-solid fa-circle-exclamation mr-2 text-xl"></i>
                            {{ errorMessage }}
                        </p>
                        <p v-if="successMessage" class="bg-green-100 text-green-700 p-4 rounded-lg mb-4 flex items-center">
                            <i class="fa-solid fa-check-circle mr-2 text-xl"></i>
                            {{ successMessage }}
                        </p>
                    </div>
                    <!-- Profile Tab Content -->
                    <div v-if="activeTab === 'profile'">
                        <form action="/edit-account/profile" method="POST" class="space-y-4">
                            <input type="hidden" name="_token" :value="csrfToken">
                            <input type="hidden" name="_method" value="PUT">
                            <div class="space-y-4">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-base font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                                    <input 
                                        type="text" 
                                        id="name" 
                                        name="name" 
                                        v-model="profile.name" 
                                        class="border rounded-md w-full p-3 text-base focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-200"
                                        required
                                    >
                                </div>
                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-base font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                    <input 
                                        type="email" 
                                        id="email" 
                                        name="email" 
                                        v-model="profile.email" 
                                        class="border rounded-md w-full p-3 text-base focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-200"
                                        required
                                    >
                                </div>
                                <!-- Password -->
                                <div>
                                    <label for="password" class="block text-base font-medium text-gray-700 mb-1">Nouveau Mot de Passe</label>
                                    <input 
                                        type="password" 
                                        id="password" 
                                        name="password" 
                                        v-model="profile.password" 
                                        class="border rounded-md w-full p-3 text-base focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-200"
                                    >
                                    <p class="mt-1 text-sm text-gray-500">Laissez vide pour conserver le mot de passe actuel</p>
                                </div>
                                <!-- Confirm Password -->
                                <div>
                                    <label for="password_confirmation" class="block text-base font-medium text-gray-700 mb-1">Confirmer le Mot de Passe</label>
                                    <input 
                                        type="password" 
                                        id="password_confirmation" 
                                        name="password_confirmation" 
                                        v-model="profile.password_confirmation" 
                                        class="border rounded-md w-full p-3 text-base focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-200"
                                    >
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button 
                                    type="submit" 
                                    class="bg-blue-600 text-white py-2 px-4 rounded-md text-base font-semibold hover:bg-blue-700 transition-colors duration-200"
                                >
                                    Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                    <!-- Extra Info Tab Content -->
                    <div v-if="activeTab === 'extraInfo'">
                        <form 
                            :action="extraInfo.has_image ? '/edit-account/extra-info/update' : '/edit-account/extra-info/add'" 
                            method="POST" 
                            enctype="multipart/form-data"
                            class="space-y-4"
                        >
                            <input type="hidden" name="_token" :value="csrfToken">
                            <input v-if="extraInfo.has_image" type="hidden" name="_method" value="PUT">
                            <!-- Scrollable Container -->
                            <div class="max-h-[400px] overflow-y-scroll pr-4 extra-info-scroll">
                                <div class="space-y-4">
                                    <!-- User Image -->
                                    <div>
                                        <label class="block text-base font-medium text-gray-700 mb-1">Photo de Profil <span class="text-red-500">*</span></label>
                                        <div class="flex items-center space-x-4">
                                            <div class="shrink-0">
                                                <img 
                                                    v-if="imagePreviewUrl || (extraInfo.has_image && extraInfo.image_url)"
                                                    :src="imagePreviewUrl || extraInfo.image_url"
                                                    alt="Profile preview"
                                                    class="h-16 w-16 rounded-full object-cover border-2 border-gray-200"
                                                    data-image-type="preview"
                                                />
                                                <img 
                                                    v-else
                                                    src="/build/assets/images/default-profile.png"
                                                    alt="Default Profile"
                                                    class="h-16 w-16 rounded-full object-cover border-2 border-gray-200"
                                                    data-image-type="default"
                                                />
                                            </div>
                                            <div class="flex-1">
                                                <label class="bg-blue-100 text-blue-700 px-4 py-2 rounded-md cursor-pointer hover:bg-blue-200 transition-colors duration-200">
                                                    Choisir un fichier
                                                    <input 
                                                        type="file" 
                                                        name="user_image" 
                                                        id="user_image"
                                                        @change="handleImageUpload" 
                                                        class="hidden"
                                                        :required="!extraInfo.has_image"
                                                        accept="image/*"
                                                    />
                                                </label>
                                                <p v-if="extraInfo.user_image_filename" class="mt-1 text-sm text-gray-600">
                                                    Fichier sélectionné : {{ extraInfo.user_image_filename }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Profession -->
                                    <div>
                                        <label for="profession" class="block text-base font-medium text-gray-700 mb-1">Profession <span class="text-red-500">*</span></label>
                                        <input 
                                            type="text" 
                                            id="profession" 
                                            name="profession" 
                                            v-model="extraInfo.profession" 
                                            class="border rounded-md w-full p-3 text-base focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-200"
                                            required
                                        >
                                    </div>
                                    <!-- Telephone -->
                                    <div>
                                        <label for="telephone" class="block text-base font-medium text-gray-700 mb-1">Téléphone <span class="text-red-500">*</span></label>
                                        <input 
                                            type="text" 
                                            id="telephone" 
                                            name="telephone" 
                                            v-model="extraInfo.telephone" 
                                            class="border rounded-md w-full p-3 text-base focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-200"
                                            required
                                        >
                                    </div>
                                    <!-- Adresse -->
                                    <div>
                                        <label for="adresse" class="block text-base font-medium text-gray-700 mb-1">Adresse <span class="text-red-500">*</span></label>
                                        <input 
                                            type="text" 
                                            id="adresse" 
                                            name="adresse" 
                                            v-model="extraInfo.adresse" 
                                            class="border rounded-md w-full p-3 text-base focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-200"
                                            required
                                        >
                                    </div>
                                    <!-- Nom Entreprise -->
                                    <div>
                                        <label for="nom_entreprise" class="block text-base font-medium text-gray-700 mb-1">Nom de l'Entreprise <span class="text-red-500">*</span></label>
                                        <input 
                                            type="text" 
                                            id="nom_entreprise" 
                                            name="nom_entreprise" 
                                            v-model="extraInfo.nom_entreprise" 
                                            class="border rounded-md w-full p-3 text-base focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-200"
                                            required
                                        >
                                    </div>
                                    <!-- Adresse Entreprise -->
                                    <div>
                                        <label for="adresse_entreprise" class="block text-base font-medium text-gray-700 mb-1">Adresse de l'Entreprise <span class="text-red-500">*</span></label>
                                        <input 
                                            type="text" 
                                            id="adresse_entreprise" 
                                            name="adresse_entreprise" 
                                            v-model="extraInfo.adresse_entreprise" 
                                            class="border rounded-md w-full p-3 text-base focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-200"
                                            required
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end mt-4">
                                <button 
                                    type="submit" 
                                    class="bg-blue-600 text-white py-2 px-4 rounded-md text-base font-semibold hover:bg-blue-700 transition-colors duration-200"
                                >
                                    {{ extraInfo.has_image ? 'Mettre à jour' : 'Enregistrer' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Profile Preview Section -->
                <div class="w-full">
                    <h3 class="text-2xl font-bold mb-4">Aperçu du Profil</h3>
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <div class="border rounded-md">
                            <!-- Profile Header -->
                            <div class="flex items-center gap-4 p-4">
                                <img 
                                    v-if="imagePreviewUrl || (extraInfo.has_image && extraInfo.image_url)"
                                    :src="imagePreviewUrl || extraInfo.image_url"
                                    alt="Profile Picture"
                                    class="rounded-full h-16 w-16 object-cover"
                                    data-image-type="preview"
                                />
                                <img 
                                    v-else
                                    src="/build/assets/images/default-profile.png"
                                    alt="Default Profile Picture"
                                    class="rounded-full h-16 w-16"
                                    data-image-type="default"
                                />
                                <div class="flex flex-col">
                                    <h4 class="font-bold text-lg">{{ profile.name || 'Votre Nom' }}</h4>
                                    <p class="text-gray-500 text-base">{{ extraInfo.profession || 'Votre Profession' }}</p>
                                </div>
                            </div>
                            <!-- Profile Info -->
                            <div class="p-4 space-y-3">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope w-6 text-gray-500"></i>
                                    <span class="ml-2 text-base text-gray-600">{{ profile.email || 'votre.email@exemple.com' }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-phone w-6 text-gray-500"></i>
                                    <span class="ml-2 text-base text-gray-600">{{ extraInfo.telephone || '+33 123 456 789' }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt w-6 text-gray-500"></i>
                                    <span class="ml-2 text-base text-gray-600">{{ extraInfo.adresse || 'Votre adresse' }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-building w-6 text-gray-500"></i>
                                    <span class="ml-2 text-base text-gray-600">{{ extraInfo.nom_entreprise || 'Nom de l\'entreprise' }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-map w-6 text-gray-500"></i>
                                    <span class="ml-2 text-base text-gray-600">{{ extraInfo.adresse_entreprise || 'Adresse de l\'entreprise' }}</span>
                                </div>
                            </div>
                            <!-- Profile Status -->
                            <div class="border-t p-4 bg-gray-50">
                                <div class="flex items-center text-base">
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                    <span class="text-gray-600">Profil actif</span>
                                    <div class="ml-auto">
                                        <button class="text-blue-600 hover:text-blue-800 text-base font-medium">
                                            <i class="fas fa-share-alt mr-1"></i> Partager
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Exit Popup -->
    <div 
        v-if="showConfirmExit"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full mx-4">
            <h3 class="text-2xl font-bold mb-4">Annuler les Modifications ?</h3>
            <p class="text-base mb-4">Vous perdrez définitivement toutes les modifications que vous avez apportées.</p>
            <div class="flex justify-end gap-2">
                <button @click="showConfirmExit = false" class="bg-gray-300 text-black px-4 py-2 rounded text-base hover:bg-gray-400 transition-colors duration-200">Continuer à éditer</button>
                <button @click="confirmExit" class="bg-red-500 text-white px-4 py-2 rounded text-base hover:bg-red-600 transition-colors duration-200">Ignorer les modifications</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'EditAccount',
    emits: ['close'],
    props: {
        initialProfile: {
            type: Object,
            default: () => ({})
        },
        initialExtraInfo: {
            type: Object,
            default: () => ({
                user_image: null,
                has_image: false,
                image_url: null,
                profession: '',
                adresse: '',
                telephone: '',
                nom_entreprise: '',
                adresse_entreprise: ''
            })
        },
        flashSuccess: {
            type: String,
            default: ''
        },
        flashError: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            activeTab: 'profile',
            profile: {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                role: ''
            },
            extraInfo: {
                user_image: null,
                has_image: false,
                image_url: null,
                user_image_filename: '',
                profession: '',
                adresse: '',
                telephone: '',
                nom_entreprise: '',
                adresse_entreprise: ''
            },
            imagePreviewUrl: null,
            defaultImageUrl: '/build/assets/images/default-profile.png',
            showConfirmExit: false,
            successMessage: this.flashSuccess,
            errorMessage: this.flashError,
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        };
    },
    mounted() {
        document.body.classList.add('no-scroll');
    },
    beforeUnmount() {
        document.body.classList.remove('no-scroll');
        if (this.imagePreviewUrl) {
            URL.revokeObjectURL(this.imagePreviewUrl);
        }
    },
    created() {
        this.profile = {
            ...this.initialProfile,
            password: '',
            password_confirmation: ''
        };
        this.extraInfo = { ...this.initialExtraInfo };
        console.log('Created: initialExtraInfo:', this.initialExtraInfo);
        if (this.extraInfo.has_image && this.extraInfo.image_url) {
            this.imagePreviewUrl = this.extraInfo.image_url;
            console.log('Created: Using stored image URL:', this.imagePreviewUrl);
        } else {
            this.imagePreviewUrl = null;
            console.log('Created: No image, using default icon');
        }
    },
    methods: {
        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                if (this.imagePreviewUrl && this.imagePreviewUrl.startsWith('blob:')) {
                    URL.revokeObjectURL(this.imagePreviewUrl);
                }
                this.imagePreviewUrl = URL.createObjectURL(file);
                this.extraInfo.user_image_filename = file.name;
                this.extraInfo.user_image = file;
                this.extraInfo.has_image = true;
                console.log('Image uploaded: imagePreviewUrl:', this.imagePreviewUrl);
            } else {
                this.imagePreviewUrl = this.extraInfo.image_url;
                this.extraInfo.user_image_filename = '';
                this.extraInfo.user_image = null;
                console.log('No image selected, reverting to:', this.imagePreviewUrl || 'default');
            }
        },
        handleClose() {
            const profileChanged = JSON.stringify(this.profile) !== JSON.stringify({
                ...this.initialProfile,
                password: '',
                password_confirmation: ''
            });
            const extraInfoChanged = JSON.stringify(this.extraInfo) !== JSON.stringify(this.initialExtraInfo);
            if (profileChanged || extraInfoChanged) {
                this.showConfirmExit = true;
            } else {
                this.confirmExit();
            }
        },
        confirmExit() {
            this.showConfirmExit = false;
            this.$emit('close');
        }
    }
};
</script>