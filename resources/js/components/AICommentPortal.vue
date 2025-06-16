<template>
    <div class="bg-black bg-opacity-50 inset-0 h-full w-full absolute" @click="closeAICommentPortal"></div>
    <div class="flex items-center flex-wrap w-[400px] rounded-lg p-4 justify-center gap-2 absolute top-[50%] left-[50%] translate-y-[-50%] translate-x-[-50%] bg-white">
        <!-- Heading & Title -->
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-robot text-3xl"></i>
                <h4 class="mb-0 mt-2 fw-semibold">Assistant IA</h4>
            </div>
            <button @click="closeAICommentPortal">
                <img src="/build/assets/icons/close.svg" alt="Close Icon" height="20" width="20" />
            </button>
        </div>

        <p class="mt-2 text-sm text-muted">
            Utilisez votre assistant IA pour répondre ou écrire un commentaire pour une meilleure interaction avec votre audience.
        </p>

        <div class="flex flex-col gap-3 w-full">
            <div class="flex flex-col gap-2">
                <label for="instructions" class="fw-semibold">Instruction :</label>
                <input 
                    type="text" 
                    name="ai-instruction" 
                    id="ai-instruction" 
                    placeholder="E.g: parlez comme un enfant de 6 ans" 
                    class="p-2 bg-white border rounded-lg"
                    v-model="userInstruction"
                />
            </div>
            <textarea 
                type="text" 
                name="ai-comment" 
                id="ai-comment" 
                class="bg-white border rounded-lg w-full p-2 h-[100px]" 
                placeholder="Décrire votre commentaire..."
                v-model="userInput"
            ></textarea>
        </div>

        <div class="w-full flex justify-end">
            <div 
                v-if="isGenerating" 
                class="py-2 px-3 w-[100px] flex items-center justify-center rounded-md text-white" 
                style="border: 2px solid pink;"
            >
                <progress-spinner 
                    style="width: 15px; height: 15px" strokeWidth="10" fill="transparent" 
                    animationDuration=".5s" 
                    aria-label="Custom ProgressSpinner" 
                />
            </div>

            <div v-else class="w-full flex items-center justify-end gap-2 mt-2">
                <button 
                    class="flex items-center gap-2 py-2 px-3 rounded-md border w-fit text-white bg-gray-500 fw-semibold"
                >
                    Utilise-le
                </button>
                <button 
                    class="flex items-center gap-2 py-2 px-3 rounded-md border w-fit text-white generate-btn"
                    @click="generateAIComment"
                >
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <p class="mb-0 fw-semibold">Générer</p>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { useToast } from "vue-toastification";


export default {
    name: "AICommentPortal",
    props: {},

    emits: ['close-ai-comment-portal'],

    setup() {
        const toast = useToast();
        return { toast };
    },

    data() {
        return {
            userInput: '',
            userInstruction: '',
            isGenerating: false,
        };
    },

    methods: {
        closeAICommentPortal() {
            this.$emit('close-ai-comment-portal');
            this.userInput = '';
            this.userInstruction = '';
        },

        async generateAIComment() {
            if(this.userInput.trim() === '') {
                this.showErrorToast('La description ne peut pas être vide');
                return;
            } else if(this.userInput.trim().length <= 20) {
                this.showErrorToast('La description doit dépasser 20 caractéres');
                return;
            }

            try {
                this.isGenerating = true;
                const openaiKey = import.meta.env.VITE_OPENAI_API_KEY;

                const response = await axios.post('https://api.openai.com/v1/responses', {
                    model: 'gpt-4.1',
                    instructions: this.userInstruction.trim() === '' ? 'parler professionnellement' : this.userInstruction,
                    input: `${this.userInput}` 
                }, 
                {
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": `Bearer ${openaiKey}`
                    }
                });

                if (response.status === 200) {
                    const output = response.data.output;
                    if (Array.isArray(output) && output.length > 0 && 
                        Array.isArray(output[0].content) && output[0].content.length > 0) {
                        const generatedText = output[0].content[0].text;
                        this.userInput = generatedText;
                    } else {
                        console.error('Invalid response structure: output or content missing');
                        this.userInput = '';
                        this.showErrorToast('Une Erreur s\'est produite lors de la génération du commentaire');
                    }
                } else {
                    this.userInput = '';
                    this.showErrorToast('Une Erreur s\'est produite lors de la génération du commentaire');
                }
            }  catch(error) {
                console.error(error);
                this.showErrorToast("Une erreur s'est produite! Veuillez réessayer.");

            } finally {
                this.isGenerating = false;
            }
        },


        showErrorToast(error) {
            this.toast.error(`${error}`, {
                position: "bottom-right",
                timeout: 3000,
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
        },
    },
}
</script>


<style scoped>
.generate-btn {
    background: linear-gradient(
        135deg,
        rgb(255 16 185) 0%,
        rgb(255 125 82) 100%
    );
    border: none;
}
</style>