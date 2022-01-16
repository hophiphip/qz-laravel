<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Solve a Quiz</title>

    <link rel="icon" href="/favicon.png">

    <link href="/css/app.css" rel="stylesheet" />
    <link href="/css/quiz.css" rel="stylesheet" />

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.4.0/dist/confetti.browser.min.js"></script>
</head>

<!-- TODO: Cypress tests -->

<body x-data="quiz">

    @include('shared.gohome')

    <!-- Quiz title -->
    <h1 class="quiz-title" x-text="quiz.title"></h1>

    <!-- Quiz settings -->
    <div class="settings-container">
        <!-- Show one question per page -->
        <button class="settings-button" x-on:click="oneQuestion">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd" />
            </svg>
        </button>

        <!-- Show all questions on one page -->
        <button class="settings-button" x-on:click="allQuestions">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <!-- Quiz questions -->
    <section id="quiz-contents" class="quiz-contents">
        <template x-for="(question, questionIndex) in quiz.questions">
            <section x-bind:id="question.uuid" class="quiz-question" x-show="showAllQuestions || (currentQuestionIdx == questionIndex)">
                <h3 x-text="`Question ${questionIndex + 1}`"></h3>

                <fieldset>
                    <legend x-text="question.text">
                    </legend>

                    <template x-for="(choice, choiceIndex) in question.choices">
                        <div>
                            <input type="checkbox" x-bind:id="choice.uuid" name="choice" x-bind:value="choice.uuid" x-bind:checked="choice.isSelected" x-on:click="choice.isSelected = !choice.isSelected">
                            <label x-bind::for="choice.uuid" x-text="choice.text"></label>
                        </div>
                    </template>
                </fieldset>
            </section>
        </template>

        <!-- Quiz completions status -->
        <div class="quiz-status">
            <template x-for="(question , questionIndex) in quiz.questions">
                <button x-show="!showAllQuestions" x-text="questionIndex + 1" :class="{ 'question-status-visited': question.isVisited, 'question-status': !question.isVisited, 'question-status-current': questionIndex === currentQuestionIdx }" x-on:click="currentQuestionIdx = questionIndex; question.isVisited = true;"></button>
            </template>
        </div>
    </section>

    <!-- Next & prev question button (only visible in `one question per page mode` -->
    <div class="button-container" x-show="!showAllQuestions">
        <button x-on:click="prevQuestion">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <button x-on:click="nextQuestion">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    <!-- Submit button -->
    <div class="button-container">
        <button x-on:click="submitAnswers">Submit</button>
    </div>

    <div class="result-container">
        <h3 x-text="quizResultContainerText"></h3>
    </div>

    <script>
        const initialQuestionIndex = 0;

        document.addEventListener('alpine:init', () => {
            Alpine.data('quiz', () => ({
                // default is one-question-per-page mode, but only if there is more than 1 question
                showAllQuestions: {{ count($quiz->getQuestions()) }} <= 1,

                currentQuestionIdx: initialQuestionIndex,
                questionCount: {{ count($quiz->getQuestions()) }},

                quizResultContainerText: 'Submit to get quiz results..',

                // NOTE: I think it would be better to store questions like that to show their completion status is real time
                quiz: {
                    title: `{{ $quiz->getTitle() }}`,
                    uuid: `{{ $quiz->getUUID() }}`,
                    questions: [
                            @foreach($quiz->getQuestions() as $questionKey => $question)
                        {
                            uuid: `{{ $question->getUUID() }}`,
                            text: `{{ $question->getText() }}`,
                            choices: [
                                    @foreach($question->getChoices() as $choiceKey => $choice)
                                {
                                    uuid: `{{ $choice->getUUID() }}`,
                                    text: `{{ $choice->getText() }}`,

                                    // was choice selected by the user
                                    isSelected: false,
                                },
                                @endforeach
                            ],

                            // was question visited by the user (was at least one choice provided)
                            isVisited: {{ $questionKey }} === initialQuestionIndex,
                        },
                        @endforeach
                    ],
                },

                submitAnswers() {
                    // Flat map questions to answers
                    const answers = this.quiz.questions.flatMap(question => [
                        {
                            questionUUID: question.uuid,

                            // Filter only the selected choices and map a choice to it's UUID
                            choices: question.choices.filter(choice => choice.isSelected).flatMap(selectedChoice => [ selectedChoice.uuid ]),
                        }
                    ]);

                    // Submit quiz answers
                    fetch('/api/submit', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            quizUUID: this.quiz.uuid,
                            answers: answers,
                        }),
                    })
                        .then(response => response.json())
                        // Set the quiz result percentage
                        .then(data => {
                            // 100% Completion is celebrated with confetti !!!
                            if (data.result === 1) {
                                confetti({
                                    spread: 180,
                                });
                            }

                            this.quizResultContainerText = data.result !== undefined ? `Quiz completion rate: ${data.result * 100}%` : 'Could not calculate quiz results!';
                        })
                        // Handle the quiz validation errors
                        .catch((err) => {
                            // Log error
                            console.error(err);
                            // Change result container text
                            this.quizResultContainerText = 'Could not submit quiz answers!';
                        });
                },

                nextQuestion() {
                    this.currentQuestionIdx = (this.currentQuestionIdx + 1) % this.questionCount;
                    this.quiz.questions[this.currentQuestionIdx].isVisited = true;
                },

                prevQuestion() {
                    this.currentQuestionIdx = (this.currentQuestionIdx - 1 + this.questionCount) % this.questionCount;
                    this.quiz.questions[this.currentQuestionIdx].isVisited = true;
                },

                oneQuestion() {
                    this.quiz.questions[this.currentQuestionIdx].isVisited = true;

                    // show one question only when question count > 1
                    this.showAllQuestions = this.quiz.questions.length < 2;
                },

                allQuestions() {
                    this.showAllQuestions = true;
                },
            }));
        });
    </script>
</body>

</html>
