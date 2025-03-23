<template>
    <div class="bg-white wh-100 vh-100 relative mt-8 plateforme-card">
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
                />
                <p class="rocket-pg text-black fw-semibold">
                    Cette étape est obligatoire!
                </p>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div v-if="isLoading" class="loading-overlay">
            <div class="loader-container">
                <div class="loader"></div>
                <p class="mt-3 text-white">Traitement en cours...</p>
            </div>
        </div>

        <!-- Card Main Content -->
        <div class="flex flex-col align-items-center justify-center card-main">
            <!-- Start Screen - Shows different button text based on existingData -->
            <div
                v-if="!startedQuestionnaire"
                class="w-full h-[80%] flex align-items-center justify-content-center"
            >
                <div class="flex flex-col gap-4 text-center align-items-center">
                    <h2 class="text-black">Plateforme de Marque</h2>
                    <p class="text-muted">
                        {{
                            existingData
                                ? "Modifiez les informations de votre marque."
                                : "Complétez les informations de votre marque."
                        }}
                    </p>
                    <button
                        class="py-2 px-4 rounded-full bg-black text-white fw-semibold"
                        @click="startQuestionnaire"
                    >
                        {{
                            existingData
                                ? "Recommencer la plateforme de marque"
                                : "Commencer"
                        }}
                    </button>
                </div>
            </div>

            <!-- Questionnaire -->
            <div
                v-else
                class="w-full h-[80%] flex align-items-center justify-content-center"
            >
                <div v-if="currentQuestionIndex < questions.length">
                    <div
                        class="flex flex-col gap-4 text-center align-items-center"
                    >
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
                                v-for="(choice, index) in questions[
                                    currentQuestionIndex
                                ].choices"
                                :key="index"
                                class="choix-btns"
                            >
                                <p
                                    style="color: #374957"
                                    class="text-lg fw-semibold"
                                >
                                    {{
                                        questions[currentQuestionIndex]
                                            .choicesTitle[index]
                                    }}
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

            <div
                class="flex align-items-center flex-wrap justify-content-center gap-2 mt-4"
            >
                <!-- Navigation buttons -->
                <button
                    v-if="currentQuestionIndex > 0 && startedQuestionnaire"
                    class="py-2 px-5 rounded-full bg-gray-300 text-black fw-semibold"
                    @click="previousQuestion"
                    :disabled="isLoading"
                >
                    Précédent
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
                    v-if="
                        currentQuestionIndex === questions.length - 1 &&
                        startedQuestionnaire
                    "
                    class="py-2 px-5 rounded-full bg-green-500 text-white fw-semibold"
                    @click="submitAnswers"
                    :disabled="isLoading"
                >
                    <span v-if="!isLoading">Soumettre</span>
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
export default {
    name: "PlateformeCard",
    props: {
        // Pass existing data from parent component
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
            existingLogo: null,
            showSuccess: false,
            successMessage: "",
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
        // Computed property to handle logo URL properly
        logoUrl() {
            // If existingLogo exists, make sure to prepend the storage path
            if (this.existingLogo) {
                return this.existingLogo.startsWith("/storage/")
                    ? this.existingLogo
                    : "/storage/" + this.existingLogo;
            }
            return "";
        },
    },
    created() {
        // Check if user has existing data
        this.checkExistingData();
    },
    methods: {
        // Check for existing data and pre-fill form if it exists
        checkExistingData() {
            if (this.existingPlateforme) {
                this.existingData = true;
                this.existingLogo = this.existingPlateforme.logo_marque || null;

                // Pre-populate answers for fields that exist
                if (this.existingPlateforme.nom_marque) {
                    this.answers["Le nom de ta marque"] =
                        this.existingPlateforme.nom_marque;
                }
                if (this.existingPlateforme.domaine_marque) {
                    this.answers["Domaine de la marque"] =
                        this.existingPlateforme.domaine_marque;
                }
                if (this.existingPlateforme.description_marque) {
                    this.answers["Description de ta marque"] =
                        this.existingPlateforme.description_marque;
                }
                // Also store the logo in answers
                if (this.existingLogo) {
                    this.answers["Logo de ta marque"] = "existing";
                }
            }
        },
        startQuestionnaire() {
            this.startedQuestionnaire = true;
            this.currentQuestionIndex = 0; // Reset to first question
            this.newLogoSelected = false; // Reset logo selection flag

            // Pre-fill the first question's input if existing data is available
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
        validateAndProceed() {
            const currentQuestion = this.questions[this.currentQuestionIndex];
            let isValid = false;

            if (currentQuestion.textInput) {
                isValid = this.getInputValue() !== "";
            }
            if (currentQuestion.imgInput) {
                // For image, if there's an existing logo and user hasn't chosen to replace, it's valid
                isValid = this.getSelectedFile() !== null;
            }
            if (currentQuestion.selectInput) {
                isValid = this.getSelectedOption() !== "";
            }

            if (isValid) {
                this.storeAnswer();
                this.nextQuestion();
            } else {
                alert(
                    "Veuillez répondre à la question avant de passer à la suivante."
                );
            }
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
            // Reset input values
            this.inputValue = "";
            this.selectedOption = "";

            // Set input values based on stored answers
            const currentQuestion = this.questions[this.currentQuestionIndex];
            if (
                currentQuestion.textInput &&
                this.answers[currentQuestion.title]
            ) {
                this.inputValue = this.answers[currentQuestion.title];
            } else if (
                currentQuestion.selectInput &&
                this.answers[currentQuestion.title]
            ) {
                this.selectedOption = this.answers[currentQuestion.title];
            } else if (currentQuestion.imgInput) {
                // Make sure to reset newLogoSelected when we arrive at the logo question
                // so the existing logo displays properly
                if (
                    this.existingLogo &&
                    this.answers[currentQuestion.title] === "existing"
                ) {
                    this.newLogoSelected = false;
                }
            }
        },
        submitAnswers() {
            if (this.isLoading) return; // Prevent multiple submissions

            this.isLoading = true; // Activate loading state
            this.storeAnswer(); // Make sure to store the final answer too

            const formData = new FormData();
            formData.append("nom_marque", this.answers["Le nom de ta marque"]);
            formData.append(
                "domaine_marque",
                this.answers["Domaine de la marque"]
            );
            formData.append(
                "description_marque",
                this.answers["Description de ta marque"]
            );

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

            fetch("/save-platform-info", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: formData,
            })
                .then((response) => {
                    if (!response.ok) {
                        return response.text().then((text) => {
                            throw new Error(text);
                        });
                    }
                    return response.json();
                })
                .then((data) => {
                    console.log("Form submission successful:", data);
                    this.isLoading = false; // Deactivate loading state
                    this.showSuccessMessage();
                })
                .catch((error) => {
                    console.error("Error submitting form:", error);
                    this.isLoading = false; // Deactivate loading state even on error
                    alert(
                        "Une erreur s'est produite lors de la soumission du formulaire."
                    );
                });
        },
        showSuccessMessage() {
            this.successMessage = this.existingData
                ? "Les informations de votre marque ont été mises à jour avec succès!"
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
.plateforme-card {
    overflow: hidden;
}

.waves {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    z-index: 1;
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

.rocket-sticker {
    width: 70px;
    height: 70px;
}

.rocket-pg {
    margin-left: -15px;
    background-color: white;
    padding: 8px 15px;
    border-radius: 20px 0 0 20px;
    font-size: 14px;
}

.card-main {
    position: relative;
    z-index: 10;
    height: 75%;
    padding: 20px;
}

.bg-img {
    width: 50px;
    height: 50px;
    opacity: 0.4;
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

/* Loading overlay and spinner styles */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.loader-container {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.loader {
    border: 5px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top: 5px solid white;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
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
