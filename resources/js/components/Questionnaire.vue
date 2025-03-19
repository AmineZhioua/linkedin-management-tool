<template>
    <PlateformeCard>
        <div v-if="currentQuestionIndex < questions.length">
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
            <!-- Questions with Choice Buttons -->
            <div v-else>
                <div class="flex flex-col align-items-start">
                    <h2>{{ questions[currentQuestionIndex].title }}</h2>
                    <p>{{ questions[currentQuestionIndex].paragraph }}</p>
                </div>
            
                <div class="choices">
                    <button 
                        v-for="(choice, index) in questions[currentQuestionIndex].choices" 
                        :key="index" 
                        @click="selectChoice(choice)"
                        class="flex flex-col align-items-center text-2xl gap-2 fw-bold px-5 py-5 rounded-lg"
                        :class="{ 'selected': choice === selectedChoice }"
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

                <input 
                    type="text" 
                    v-if="currentQuestionIndex == 3" 
                    class="w-full px-4 py-2 mt-4 rounded-lg"
                    style="border: 3px solid #B4EAED;"
                    placeholder="Ecrire ici..."
                />

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
                        @click="nextQuestion"
                    >
                        Suivant
                    </button>
                </div>
            </div>
        </div>
    </PlateformeCard>
</template>

<script>
    import PlateformeCard from './PlateformeCard.vue';

    export default {
        name: "Questionnaire",
        components: {
            PlateformeCard,
        },

        data() {
            return {
                currentQuestionIndex: 0,
                selectedChoice: null,
                questions: [
                    {
                        title: "Plateforme de Marque",
                        paragraph: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        choices: ["Réaliser ma plateforme de marque", "Continuer ma plateforme de marque"],
                        choicesTitle: ["Commencer", "Continuer"]
                    },
                    {
                        title: "Question 1",
                        paragraph: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        choices: ["Choix 1", "Choix 2", "Choix 3"],
                        icons: ["choice-1", "choice-2", "choice-3"]
                    },
                    {
                        title: "Question 2",
                        paragraph: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        choices: ["Choix 1", "Choix 2", "Choix 3", "Choix 4", "Choix 5", "Choix 6"],
                        icons: ["choice-1", "choice-2", "choice-3", "choice-1", "choice-2", "choice-3"]
                    },
                    {
                        title: "Question 3",
                        paragraph: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        choices: ["Choix 1", "Choix 2", "Choix 3"],
                        icons: ["choice-1", "choice-2", "choice-3"]
                    }
                ]
            };
        },
        methods: {
            selectChoice(choice) {
                this.selectedChoice = choice;
            },
            nextQuestion() {
                if (this.selectedChoice !== null) {
                    this.selectedChoice = null;
                    this.currentQuestionIndex++;
                } else {
                    alert("Veuillez sélectionner une réponse.");
                }
            },
            selectAndNext(choice) {
                this.selectChoice(choice);
                this.nextQuestion();
            },
            submitAnswers() {
                alert("Questionnaire terminé !");
            }
        }
    };
</script>

<style scoped>
    .choices {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        justify-content: center;
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