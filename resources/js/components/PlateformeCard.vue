<template>
    <div class="bg-white wh-100 vh-100 relative mt-8 plateforme-card">
        <!-- Heart Sticker -->
        <div class="absolute top-[-40px] left-[40px] flex align-items-center" >
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
                style="margin-left: -25px;"
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

        <!-- Card Main Content -->
        <div class="flex flex-col align-items-center justify-center card-main">
            <div 
                v-if="currentQuestionIndex < questions.length" 
                class="w-full h-[80%] flex align-items-center justify-content-center"
            >
                <!-- First Question -->
                <div v-if="currentQuestionIndex == 0">
                    <div class="flex flex-col gap-4 text-center align-items-center">
                        <h2 class="text-black">{{ questions[currentQuestionIndex].title }}</h2>
                        <p class="text-muted">{{ questions[currentQuestionIndex].paragraph }}</p>
                    </div>

                    <div 
                        class="bg-white border px-8 py-10 flex flex-col align-items-center gap-12 rounded-xl"
                        style="box-shadow: 0 0 12px 6px #B4EAED;"
                    >
                        <h2 class="text-black text-2xl">Fais ton choix !</h2>
                        <div class="flex gap-4 text-center">
                            <div 
                                v-for="(choice, index) in questions[currentQuestionIndex].choices" 
                                :key="index"
                                class="choix-btns"
                            >
                                <p style="color: #374957;" class="text-lg fw-semibold">
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
                    </div>
                </div>

                <!-- Other Questions -->
                <div v-else class="container w-full h-full flex flex-col justify-content-evenly">
                    <div class="flex flex-col align-items-start">
                        <h2>{{ questions[currentQuestionIndex].title }}</h2>
                        <p>{{ questions[currentQuestionIndex].paragraph }}</p>
                    </div>
                
                    <!-- Display choices only when 'multiChoices' is true -->
                    <div v-if="questions[currentQuestionIndex].multiChoices" class="choices">
                        <button 
                            v-for="(choice, index) in questions[currentQuestionIndex].choices" 
                            :key="index" 
                            @click="selectChoice(choice)"
                            class="flex flex-col align-items-center text-2xl gap-2 fw-bold px-5 py-5 rounded-lg"
                            :class="{ 'selected': selectedChoice && selectedChoice.includes(choice) }"
                        >
                            {{ choice }}
                            <img 
                                :src="'/build/assets/icons/' + questions[currentQuestionIndex].icons[index] + '.svg'" 
                                alt="Choice Icon"
                                class="mt-2"
                                height="40"
                                width="40"
                            />
                        </button>
                    </div>

                    <!-- Text input -->
                    <input 
                        v-if="questions[currentQuestionIndex].textInput"
                        type="text" 
                        id="textInput"
                        class="w-full px-4 py-2 mt-4 rounded-lg"
                        style="border: 3px solid #B4EAED;"
                        placeholder="Écrire ici..."
                    />

                    <!-- Image Upload Input -->
                    <input 
                        v-if="questions[currentQuestionIndex].imgInput"
                        type="file"
                        accept="image/*"
                        class="w-full px-4 py-2 mt-4 rounded-lg"
                        style="border: 3px solid #B4EAED;"
                    />

                    <!-- Select Input -->
                    <select 
                        v-if="questions[currentQuestionIndex].selectInput"
                        class="w-full px-4 py-2 mt-4 rounded-lg"
                        style="border: 3px solid #B4EAED;"
                    >
                        <option value="disabled" class="text-black" disabled selected>--- Choisir une option ---</option>
                        <option v-for="(option, index) in questions[currentQuestionIndex].selectOptions" :key="index">
                            {{ option }}
                        </option>
                    </select>

                    <div class="flex align-items-center flex-wrap justify-content-center gap-2 mt-4">
                        <button 
                            class="py-2 px-5 rounded-full fw-semibold bg-transparent" 
                            style="border: 1px solid black;"
                            @click="submitAnswers"
                        >
                            Engresitrer et revenir plus tard
                        </button>
                        <button 
                            class="py-2 px-5 rounded-full bg-black text-white fw-semibold" 
                            v-if="currentQuestionIndex < questions.length - 1" 
                            @click="validateAndProceed"
                        >
                            Suivant
                        </button>
                    </div>
                </div>
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

        data() {
            return {
                currentQuestionIndex: 0,
                selectedChoice: null,
                // Questions data (UPDATE THE QUESTIONS AS YOU NEED)
                questions: [
                    {
                        // DO NOT CHANGE THIS QUESTION AT ALL ONLY IF NECESSARY
                        title: "Plateforme de Marque",
                        paragraph: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        choices: ["Réaliser ma plateforme de marque", "Continuer ma plateforme de marque"],
                        choicesTitle: ["Commencer", "Continuer"],
                    },
                    {
                        title: "Question 1",
                        paragraph: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        choices: ["Choix 1", "Choix 2", "Choix 3"],
                        icons: ["choice-1", "choice-2", "choice-3"],
                        multiChoices: true,
                        textInput: false,
                        imgInput: false,
                        selectInput: false,
                    },
                    {
                        title: "Question 2",
                        paragraph: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        choices: ["Choix 1", "Choix 2", "Choix 3", "Choix 4", "Choix 5", "Choix 6"],
                        icons: ["choice-1", "choice-2", "choice-3", "choice-1", "choice-2", "choice-3"],
                        multiChoices: true,
                        textInput: false,
                        imgInput: false,
                        selectInput: false,
                    },
                    {
                        title: "Question 3",
                        paragraph: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        choices: ["Choix 1", "Choix 2", "Choix 3"],
                        icons: ["choice-1", "choice-2", "choice-3"],
                        multiChoices: true,
                        textInput: true,
                        imgInput: false,
                        selectInput: false,
                    },
                    {
                        title: "Question 4",
                        paragraph: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        choices: ["Choix 1", "Choix 2", "Choix 3"],
                        icons: ["choice-1", "choice-2", "choice-3"],
                        multiChoices: false,
                        textInput: true,
                        imgInput: false,
                        selectInput: false,
                    },
                    {
                        title: "Question 5",
                        paragraph: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        choices: ["Choix 1", "Choix 2", "Choix 3"],
                        icons: ["choice-1", "choice-2", "choice-3"],
                        selectOptions: ["Option 1", "Option 2", "Option 3"],
                        multiChoices: false,
                        textInput: true,
                        imgInput: false,
                        selectInput: true,
                    }
                ]
            };
        },

    methods: {
        selectChoice(choice) {
            this.selectedChoice = choice;
        },

        getInputValue() {
            const inputField = document.getElementById("textInput");
            return inputField ? inputField.value.trim() : "";
        },

        getSelectedFile() {
            const fileInput = document.querySelector("input[type='file']");
            return fileInput ? fileInput.files[0] : null;
        },

        getSelectedOption() {
            const selectField = document.querySelector("select");
            return selectField ? selectField.value : "";
        },

        validateAndProceed() {
            const currentQuestion = this.questions[this.currentQuestionIndex];
            let isValid = false;

            if (currentQuestion.multiChoices) {
                isValid = this.selectedChoice && this.selectedChoice.length > 0;
            } else if (currentQuestion.choices) {
                isValid = this.selectedChoice !== null;
            }
            if (currentQuestion.textInput) {
                isValid = this.getInputValue() !== "";
            }
            if (currentQuestion.imgInput) {
                isValid = this.getSelectedFile() !== null;
            }
            if (currentQuestion.selectInput) {
                isValid = this.getSelectedOption() !== "disabled";
            }

            if (isValid) {
                this.nextQuestion();
                document.getElementById("textInput").value = "";
            } else {
                alert("Veuillez répondre à la question avant de passer à la suivante.");
            }
        },

        nextQuestion() {
            if (this.currentQuestionIndex < this.questions.length - 1) {
                this.selectedChoice = null;
                this.currentQuestionIndex++;
            } else {
                this.submitAnswers();
            }
        },

        selectAndNext(choice) {
            this.selectChoice(choice);
            this.validateAndProceed();
        },

        submitAnswers() {
            alert("Questionnaire terminé !");
        }
    }
};
</script>

<style scoped>
    .plateforme-card {
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
    }

    .card-main {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100%;
    }

    .waves {
        position: absolute;
        top: 50%;
        left: 0;
        transform: translateY(-50%);
        width: 100%;
        height: auto;
    }

    .rocket-div {
        position: absolute;
        top: 5%;
        right: 0;
    }

    .rocket-content {
        display: flex;
        align-items: center;
    }

    .rocket-sticker {
        width: 100px;
        height: auto;
        margin-right: -25px;
        z-index: 10;
    }

    .rocket-pg {
        background: linear-gradient(90deg, rgba(255, 0, 204, 0.645) 0%, rgba(247, 206, 71, 0.656) 100%);
        padding: 20px 30px;
        font-weight: 400;
        margin: 0;
        white-space: nowrap;
    }

    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-15px);
        }
    }

    .bg-img {
        animation: bounce 2s infinite ease-in-out;
    }

    .choices {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        justify-content: center;
    }

    /* Medium screens: 2 columns */
    @media (max-width: 768px) {
        .choices {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Small screens: 1 column (stack vertically) */
    @media (max-width: 480px) {
        .choices {
            grid-template-columns: repeat(1, 1fr);
        }
    }

    .choices button {
        border: 3px solid #B4EAED;
        box-shadow: 0 0 12px 6px #B4EAED;
    }

    button {
        padding: 10px;
        border: 1px solid #ccc;
        background: white;
        cursor: pointer;
        font-family: "Inter", sans-serif;
    }

    button.selected {
        background: #B4EAED;
    }

    .choix-btns .first-btn {
        background-color: black;
        color: white;
    }
</style>