<template>
    <div class="wh-100 vh-100 relative mt-8 plateforme-card">
        <!-- Heart Sticker -->
        <div class="absolute top-[-40px] left-[40px] flex align-items-center">
            <img
                src="/build/assets/icons/heart-sticker.svg"
                alt="Heart Sticker"
                class="z-10"
                height="90"
                width="90"
            />
            <img
                src="/build/assets/icons/plateforme-text.svg"
                alt="Heart Sticker"
                style="margin-left: -25px"
                class="d-none d-lg-block"
                width="300"
            />
        </div>

        <!-- Wave Image -->
        <img
            src="/build/assets/images/waves.svg"
            alt="Waves Image"
            class="waves"
        />

        <!-- Rocket Side Image -->
        <div class="rocket-div">
            <div class="rocket-content">
                <img
                    src="/build/assets/icons/rocket-sticker.svg"
                    alt="Rocket Sticker"
                    class="rocket-sticker"
                    style="z-index: 10; margin-right: -15px;"
                />
                <p class="rocket-pg text-black fw-semibold">
                    Cette étape est obligatoire!
                </p>
            </div>
        </div>

        <!-- Loading Overlay -->
        <loading-overlay :isLoading="isLoading" message="Traitement en cours..." />

        <!-- Card Main Content -->
        <div class="flex flex-col align-items-center justify-center card-main">
            <!-- Start Screen - Shows different button text based on existingData -->
            <plateforme-start
                v-if="!startedQuestionnaire"
                :has-completed-data="hasCompletedData"
                :has-partial-data="hasPartialData"
                @start="startQuestionnaire"
            />

            <!-- Questionnaire -->
            <div v-else class="w-full h-[80%] flex align-items-center justify-content-center">
                <div v-if="currentQuestionIndex < questions.length">
                    <!-- Progress Indicator -->
                    <div class="mb-4 w-full flex justify-center">
                        <progress-indicator 
                            :total-steps="questions.length" 
                            :current-step="currentQuestionIndex + 1" 
                        />
                    </div>
                    <!-- Questions Header -->
                    <div class="flex flex-col gap-4 text-center align-items-center">
                        <h2 class="text-black">
                            {{ questions[currentQuestionIndex].title }}
                        </h2>
                        <p class="text-muted">
                            {{ questions[currentQuestionIndex].paragraph }}
                        </p>
                    </div>

                    <div
                        class="bg-white border px-8 py-10 flex flex-col align-items-center gap-12 rounded-xl"
                        style="box-shadow: 0 0 12px 6px #b4eaed"
                    >
                        <!-- Question Choices -->
                        <div v-if="questions[currentQuestionIndex].choices">
                            <div
                                v-for="(choice, index) in questions[currentQuestionIndex].choices"
                                :key="index"
                                class="choix-btns"
                            >
                                <p style="color: #374957" class="text-lg fw-semibold">
                                    {{ questions[currentQuestionIndex].choicesTitle[index] }}
                                </p>
                                <button
                                    @click="selectAndNext(choice)"
                                    class="py-2 px-4 rounded-full"
                                    :class="{ 'first-btn': index === 0 }"
                                >
                                    {{ choice }}
                                </button>
                            </div>
                        </div>

                        <!-- Text and File Inputs -->
                        <div v-if="questions[currentQuestionIndex].textInput">
                            <input
                                id="textInput"
                                v-model="inputValue"
                                type="text"
                                :placeholder="
                                    questions[currentQuestionIndex]
                                        .placeholder || 'Entrez votre texte ici'
                                "
                                class="p-2 border border-gray-300 rounded-md w-[300px] h-[40px]"
                            />
                        </div>
                        <div v-if="questions[currentQuestionIndex].selectInput">
                            <select
                                v-model="selectedOption"
                                class="p-2 border border-gray-300 rounded-md w-[300px] h-[40px]"
                            >
                                <option disabled value="">
                                    Sélectionner une option
                                </option>
                                <option
                                    v-for="option in questions[
                                        currentQuestionIndex
                                    ].selectOptions"
                                    :key="option"
                                >
                                    {{ option }}
                                </option>
                            </select>
                        </div>
                        <div v-if="questions[currentQuestionIndex].imgInput">
                            <div
                                v-if="existingLogo && !newLogoSelected"
                                class="mb-4 text-center"
                            >
                                <button
                                    @click="newLogoSelected = true"
                                    class="py-1 px-3 rounded-full bg-gray-200 text-black text-sm"
                                >
                                    Changer le logo
                                </button>
                            </div>
                            <input
                                v-if="!existingLogo || newLogoSelected"
                                type="file"
                                @change="handleFileChange"
                                class="p-2 border border-gray-300 rounded-md w-[300px]"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex align-items-center flex-wrap justify-content-center gap-2 mt-4">
                <!-- Navigation buttons -->
                <button
                    v-if="currentQuestionIndex > 0 && startedQuestionnaire"
                    class="py-2 px-5 rounded-full bg-gray-300 text-black fw-semibold"
                    @click="previousQuestion"
                    :disabled="isLoading"
                >
                    Précédent
                </button>

                <!-- Save and return later button -->
                <button
                    v-if="startedQuestionnaire && currentQuestionIndex >= 0"
                    class="py-2 px-5 rounded-full bg-blue-500 text-white fw-semibold"
                    @click="saveAndReturnLater"
                    :disabled="isLoading"
                >
                    Enregistrer et revenir plus tard
                </button>

                <button
                    v-if="
                        currentQuestionIndex < questions.length - 1 &&
                        startedQuestionnaire
                    "
                    class="py-2 px-5 rounded-full bg-black text-white fw-semibold"
                    @click="validateAndProceed"
                    :disabled="isLoading"
                >
                    Suivant
                </button>
                <button
                    v-if ="
                        currentQuestionIndex === questions.length - 1 &&
                        startedQuestionnaire
                    "
                    class="py-2 px-5 rounded-full bg-green-500 text-white fw-semibold"
                    @click="submitAnswers"
                    :disabled="isLoading"
                >
                    <span v-if = "!isLoading">Soumettre</span>
                    <span v-else class="btn-loader"></span>
                </button>
            </div>

            <!-- Success Message -->
            <div
                v-if="showSuccess"
                class="mt-4 p-3 bg-green-100 text-green-700 rounded-md w-full max-w-md text-center"
            >
                {{ successMessage }}
            </div>

            <!-- Error Message -->
            <Popup
                v-if="errorMessage"
                path="/build/assets/popups/sad-face.svg"
                @close ="errorMessage = null"
            >
                {{ errorMessage }}
            </Popup>
        </div>

        <!-- Icons in The Background -->
        <img
            src="/build/assets/icons/fi-ss-palette.svg"
            alt="Calendar Icon"
            class="absolute top-[10%] left-[12%] bg-img"
        />
        <img
            src="/build/assets/icons/fi-oo-palette.svg"
            alt="Calendar Icon"
            class="absolute bottom-[5%] left-[25%] bg-img"
        />
        <img
            src="/build/assets/icons/fi-purple-calendar.svg"
            alt="Calendar Icon"
            class="absolute bottom-[15%] right-[15%] bg-img"
        />
        <img
            src="/build/assets/icons/fi-sr-calendar.svg"
            alt="Calendar Icon"
            class="absolute top-[20%] right-[12%] bg-img"
        />
    </div>
</template>

<script>
import axios from 'axios';
import Popup from './Popup.vue';

export default {
    components: {
        Popup,
    },
    name: "PlateformeCard",
    props: {
        existingPlateforme: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            startedQuestionnaire: false,
            currentQuestionIndex: 0,
            inputValue: "",
            selectedOption: "",
            selectedFile: null,
            newLogoSelected: false,
            existingData: false,
            hasPartialData: false,
            hasCompletedData: false,
            existingLogo: null,
            showSuccess: false,
            successMessage: "",
            errorMessage: null,
            isLoading: false,
            answers: {},
            questions: [
                {
                    title: "Le nom de ta marque",
                    paragraph: "Quel est le nom de votre marque ?",
                    textInput: true,
                    placeholder: "Entrez le nom de votre marque",
                },
                {
                    title: "Domaine de la marque",
                    paragraph: "Quel est le domaine de votre marque ?",
                    selectInput: true,
                    selectOptions: ["Option 1", "Option 2", "Option 3"],
                },
                {
                    title: "Logo de ta marque",
                    paragraph: "Téléchargez le logo de votre marque.",
                    imgInput: true,
                },
                {
                    title: "Description de ta marque",
                    paragraph: "Entrez une description pour votre marque.",
                    textInput: true,
                    placeholder: "Décrivez votre marque en quelques phrases",
                },
            ],
        };
    },

    computed: {
        logoUrl() {
            if (this.existingLogo) {
                return this.existingLogo.startsWith("/storage/") ? this.existingLogo : "/storage/" + this.existingLogo;
            }
            return "";
        },
    },

    created() {
        this.checkExistingData();
    },

    methods: {
        // Check for existing data and pre-fill form if it exists
        checkExistingData() {
            if (this.existingPlateforme) {
                this.existingData = true;
                this.existingLogo = this.existingPlateforme.logo_marque || null;

                // Check if it's partial data or complete data
                const hasNom = !!this.existingPlateforme.nom_marque;
                const hasDomaine = !!this.existingPlateforme.domaine_marque;
                const hasLogo = !!this.existingPlateforme.logo_marque;
                const hasDescription = !!this.existingPlateforme.description_marque;

                // If all fields are filled, it's complete data
                if (hasNom && hasDomaine && hasLogo && hasDescription) {
                    this.hasCompletedData = true;
                    this.hasPartialData = false;
                }
                // If at least one field is filled but not all, it's partial data
                else if (hasNom || hasDomaine || hasLogo || hasDescription) {
                    this.hasPartialData = true;
                    this.hasCompletedData = false; // Make sure these are mutually exclusive
                }

                // Pre-populate answers for fields that exist
                if (this.existingPlateforme.nom_marque) {
                    this.answers["Le nom de ta marque"] = this.existingPlateforme.nom_marque;
                }
                if (this.existingPlateforme.domaine_marque) {
                    this.answers["Domaine de la marque"] = this.existingPlateforme.domaine_marque;
                }
                if (this.existingPlateforme.description_marque) {
                    this.answers["Description de ta marque"] = this.existingPlateforme.description_marque;
                }
                if (this.existingLogo) {
                    this.answers["Logo de ta marque"] = "existing";
                }
            }
        },
        startQuestionnaire() {
            this.startedQuestionnaire = true;

            // If there's partial data, find the first unanswered question
            if (this.hasPartialData && !this.hasCompletedData) {
                if (!this.answers["Le nom de ta marque"]) {
                    this.currentQuestionIndex = 0;
                } else if (!this.answers["Domaine de la marque"]) {
                    this.currentQuestionIndex = 1;
                } else if (!this.answers["Logo de ta marque"]) {
                    this.currentQuestionIndex = 2;
                } else if (!this.answers["Description de ta marque"]) {
                    this.currentQuestionIndex = 3;
                } else {
                    this.currentQuestionIndex = 0;
                }
            } else {
                this.currentQuestionIndex = 0;
            }

            this.newLogoSelected = false; // Reset logo selection flag

            // Pre-fill the current question's input if existing data is available
            this.updateInputsForCurrentQuestion();
        },
        getInputValue() {
            return this.inputValue.trim();
        },
        getSelectedFile() {
            return (
                this.selectedFile ||
                (this.existingLogo && !this.newLogoSelected ? "existing" : null)
            );
        },
        handleFileChange(event) {
            this.selectedFile = event.target.files[0];
        },
        getSelectedOption() {
            return this.selectedOption;
        },

        previousQuestion() {
            if (this.currentQuestionIndex > 0) {
                this.storeAnswer();
                this.currentQuestionIndex--;
                this.updateInputsForCurrentQuestion();
            }
        },

        resetErrorMessage() {
            this.errorMessage = null;
        },
        validateAndProceed() {
            const currentQuestion = this.questions[this.currentQuestionIndex];
            let isValid = false;

            this.resetErrorMessage();

            if (currentQuestion.textInput) {
                isValid = this.getInputValue() !== "";
            }
            if (currentQuestion.imgInput) {
                isValid = this.getSelectedFile() !== null;
            }
            if (currentQuestion.selectInput) {
                isValid = this.getSelectedOption() !== "";
            }

            if (isValid) {
                this.storeAnswer();
                this.nextQuestion();
            } else {
                this.errorMessage = "Veuillez remplir ce champ avant de continuer.";
            }

        },
        selectAndNext(choice) {
            // Store the selected choice and move to next question
            const currentQuestion = this.questions[this.currentQuestionIndex];
            this.answers[currentQuestion.title] = choice;
            this.nextQuestion();
        },
        storeAnswer() {
            const currentQuestion = this.questions[this.currentQuestionIndex];

            if (currentQuestion.textInput) {
                this.answers[currentQuestion.title] = this.getInputValue();
            }
            if (currentQuestion.imgInput) {
                // Only store file reference if it's a new file
                if (this.selectedFile) {
                    this.answers[currentQuestion.title] = this.selectedFile;
                } else if (this.existingLogo && !this.newLogoSelected) {
                    this.answers[currentQuestion.title] = "existing";
                }
            }
            if (currentQuestion.selectInput) {
                this.answers[currentQuestion.title] = this.getSelectedOption();
            }
        },
        nextQuestion() {
            if (this.currentQuestionIndex < this.questions.length - 1) {
                this.currentQuestionIndex++;
                this.updateInputsForCurrentQuestion();
            }
        },
        updateInputsForCurrentQuestion() {
            this.inputValue = "";
            this.selectedOption = "";

            // Set input values based on stored answers
            const currentQuestion = this.questions[this.currentQuestionIndex];
            if (currentQuestion.textInput && this.answers[currentQuestion.title]) {
                this.inputValue = this.answers[currentQuestion.title];
            } else if (currentQuestion.selectInput && this.answers[currentQuestion.title]) {
                this.selectedOption = this.answers[currentQuestion.title];
            } else if (currentQuestion.imgInput) {
                // Make sure to reset newLogoSelected when we arrive at the logo question
                // so the existing logo displays properly
                if (this.existingLogo && this.answers[currentQuestion.title] === "existing") {
                    this.newLogoSelected = false;
                }
            }
        },
        async saveAndReturnLater() {
            if (this.isLoading) return; // Prevent multiple submissions

            // Store the current answer
            this.storeAnswer();

            // Check if at least one answer is provided
            const hasAtLeastOneAnswer = Object.keys(this.answers).length > 0;

            if (!hasAtLeastOneAnswer) {
                // Show error message if no answers are provided
                this.errorMessage = "Veuillez remplir au moins un champ avant d'enregistrer.";
                return;
            }

            this.isLoading = true; // Activate loading state

            const formData = new FormData();

            // Only append fields that have answers
            if (this.answers["Le nom de ta marque"]) {
                formData.append("nom_marque", this.answers["Le nom de ta marque"]);
            }

            if (this.answers["Domaine de la marque"]) {
                formData.append("domaine_marque", this.answers["Domaine de la marque"]);
            }

            if (this.answers["Description de ta marque"]) {
                formData.append("description_marque", this.answers["Description de ta marque"]);
            }

            // Set mode to partial for save and return later
            formData.append("mode", "partial");

            // Add ID if updating
            if (this.existingData && this.existingPlateforme.id) {
                formData.append("id", this.existingPlateforme.id);
            }

            // File handling - only if we have a logo answer
            if (this.selectedFile) {
                formData.append("logo_marque", this.selectedFile);
                formData.append("logo_changed", "true");
            } else if (this.existingLogo && !this.newLogoSelected) {
                formData.append("logo_changed", "false");
            }

            await axios.post("/save-platform-info", formData, {
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
            })
            .then((response) => {
                this.isLoading = false;
                this.successMessage = "Informations enregistrées. Vous pouvez revenir compléter plus tard.";
                this.showSuccess = true;

                setTimeout(() => {
                    this.showSuccess = false;
                    this.startedQuestionnaire = false;
                    window.location.reload();
                }, 2000);
            })
            .catch((error) => {
                this.isLoading = false; // Deactivate loading state even on error
                this.errorMessage = "Une erreur s'est produite lors de l'enregistrement du formulaire.";
            });

        },
        async submitAnswers() {
            if (this.isLoading) return; // Prevent multiple submissions

            this.isLoading = true;
            this.storeAnswer(); // Make sure to store the final answer too

            const formData = new FormData();
            formData.append("nom_marque", this.answers["Le nom de ta marque"]);
            formData.append("domaine_marque", this.answers["Domaine de la marque"]);
            formData.append("description_marque", this.answers["Description de ta marque"]);

            // Add mode parameter for update/create
            formData.append("mode", this.existingData ? "update" : "create");

            // Add ID if updating
            if (this.existingData && this.existingPlateforme.id) {
                formData.append("id", this.existingPlateforme.id);
            }

            // File handling
            if (this.selectedFile) {
                formData.append("logo_marque", this.selectedFile);
                formData.append("logo_changed", "true");
            } else if (this.existingLogo && !this.newLogoSelected) {
                // Keep existing logo
                formData.append("logo_changed", "false");
            }

            await axios.post("/save-platform-info", formData, {
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
            })
            .then((response) => {
                this.isLoading = false;
                this.showSuccessMessage();
            })
            .catch((error) => {
                this.isLoading = false;
                this.errorMessage = "Une erreur s'est produite lors de la soumission du formulaire.";
            });

        },
        showSuccessMessage() {
            this.successMessage = this.existingData ? "Les informations de votre marque ont été mises à jour avec succès!"
                : "Les informations de votre marque ont été enregistrées avec succès!";
            this.showSuccess = true;

            // Reset form after 2 seconds
            setTimeout(() => {
                this.showSuccess = false;
                this.startedQuestionnaire = false;

                // Refresh existing data if needed
                if (this.existingData) {
                    window.location.reload();
                }
            }, 2000);
        },
    },
};
</script>

<style scoped>
.waves {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 100%;
}

.rocket-div {
    position: absolute;
    top: 15%;
    right: 0;
    z-index: 10;
}

.rocket-content {
    display: flex;
    align-items: center;
}

.rocket-pg {
    margin-left: -15px;
    background-color: white;
    background: linear-gradient(90deg, rgba(255, 0, 204, 0.645) 0%, rgba(247, 206, 71, 0.656) 100%);
    padding: 20px 30px;
    font-size: 14px;
}

.card-main {
    position: relative;
    z-index: 10;
    height: 100%;
    padding: 20px;
}


.first-btn {
    background-color: #f0f4fa;
}

.choix-btns {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 15px;
}


.btn-loader {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top: 3px solid white;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

/* Disable pointer events when loading */
button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
</style>
