<template>
    <div class="bg-black bg-opacity-50 inset-0 h-full w-full absolute" @click="emitCloseBoostForm"></div>
    <div class="flex flex-col bg-white rounded-md p-4 justify-center gap-2 absolute top-[50%] left-[50%] translate-y-[-50%] translate-x-[-50%]">
        <!-- Title & Icon and Close Button -->
        <div class="flex items-center justify-between gap-12">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-bolt text-3xl"></i>
                <h3>{{ isEditMode ? 'Modifier la Demande de Boost' : 'Demande de Boost d\'interactions' }}</h3>
            </div>

            <button @click="emitCloseBoostForm">
                <img src="/build/assets/icons/close.svg" alt="Close Icon" height="20" width="20" />
            </button>
        </div>

        <!-- Likes & Comments Form -->
        <div class="flex flex-col gap-2 mt-4">
            <label for="nb_likes" class="fw-semibold text-md">Nombres des Likes :</label>
            <input 
                type="number" 
                id="nb_likes" 
                name="nb_likes" 
                placeholder="E.g: 10" 
                class="bg-white border py-2 px-3 rounded-md" 
                v-model="nbLikes"
            />
        </div>
        <div class="flex flex-col gap-2 mt-2">
            <label for="nb_comments" class="fw-semibold text-md">Nombres des Commentaires :</label>
            <input 
                type="number" 
                id="nb_comments" 
                name="nb_comments" 
                placeholder="E.g: 10" 
                class="bg-white border py-2 px-3 rounded-md" 
                v-model="nbComments"
            />
        </div>
        <div class="flex flex-col gap-2 mt-2">
            <label for="message" class="fw-semibold text-md">Message supplémentaires :</label>
            <textarea 
                class="bg-white border py-2 px-3 rounded-md min-h-[200px]" 
                placeholder="E.g: Je souhaite que vous approvez ma demande le plutôt possible, merci d'avance"
                v-model="message"
            ></textarea>
        </div>
        <div 
            v-if="isLoading"
            class="py-2 px-3 w-full flex items-center justify-center rounded-md text-white bg-blue-500 bg-opacity-80 mt-2" 
        >
            <progress-spinner 
                style="width: 20px; height: 20px; opacity: 1;" strokeWidth="10" fill="transparent" 
                animationDuration=".5s" 
                aria-label="Custom ProgressSpinner" 
            />
        </div>
        <button 
            v-else
            class="bg-blue-500 p-2 text-white fw-semibold rounded-md mt-2"
            @click="requestForBoost"
        >
            {{ buttonText }}
        </button>
    </div>
</template>

<script>
import ApiService from '../services/apiCalls';
import { useToast } from 'vue-toastification';

export default {
    name: 'BoostRequestForm',

    props: {
        post: {
            type: Object,
            required: true,
        },

        boostRequest: {
            type: Object,
            required: false,
            default: null,
        },
    },

    emits: ['close-boost-form'],

    setup() {
        const toast = useToast();
        return { toast };
    },

    data() {
        return {
            nbLikes: 0,
            nbComments: 0,
            message: '',
            isLoading: false,
        };
    },

    watch: {
        boostRequest: {
            handler(newVal) {
                if (newVal) {
                    this.nbLikes = newVal.nb_likes || 0;
                    this.nbComments = newVal.nb_comments || 0;
                    this.message = newVal.message || '';
                } else {
                    this.nbLikes = 0;
                    this.nbComments = 0;
                    this.message = '';
                }
            },
            immediate: true,
        },
    },

    computed: {
        isEditMode() {
            return !!this.boostRequest;
        },
        buttonText() {
            return this.isEditMode ? 'Modifier' : 'Envoyer';
        },
    },
    
    methods: {
        async requestForBoost() {
            try {
                this.isLoading = true;

                if (this.boostRequest) {
                    await ApiService.updateBoostRequest(this.boostRequest.id, this.nbLikes, this.nbComments, this.message);
                    this.toast.success("Demande de Boost mise à jour avec succès !", {
                        position: "bottom-right",
                        closeOnClick: true,
                        pauseOnFocusLoss: true,
                        pauseOnHover: true,
                        draggable: true,
                        draggablePercent: 0.6,
                        showCloseButtonOnHover: false,
                        hideProgressBar: false,
                        timeout: 2000,
                        closeButton: "button",
                        icon: true,
                        rtl: false
                    });

                } else {
                    await ApiService.requestBoost(this.post, this.nbLikes, this.nbComments, this.message);
                    this.toast.success("Demande envoyée avec succès !", {
                        position: "bottom-right",
                        closeOnClick: true,
                        pauseOnFocusLoss: true,
                        pauseOnHover: true,
                        draggable: true,
                        draggablePercent: 0.6,
                        showCloseButtonOnHover: false,
                        hideProgressBar: false,
                        timeout: 2000,
                        closeButton: "button",
                        icon: true,
                        rtl: false
                    });

                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }

            } catch (error) {
                console.error("Operation Failed", error);
                this.toast.error("Une erreur s'est produite lors de l'opération !");

            } finally {
                this.isLoading = false;
                this.emitCloseBoostForm();
            }
        },

        emitCloseBoostForm() {
            this.$emit('close-boost-form');
        },
    },
};
</script>