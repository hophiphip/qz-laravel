<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Create a Quiz</title>

    <link rel="icon" href="{{ asset('favicon.png') }}">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/new.css') }}" rel="stylesheet" />

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body x-data="quiz">

    @include('shared.gohome')

    <!-- Quiz title -->
    <div class="new-quiz-title-wrapper">
        <label for="new-quiz-title"></label>
        <input id="new-quiz-title" type="text" x-model="title" placeholder="Quiz title">

        <strong class="error" x-text="titleError"></strong>
    </div>

    <!-- Quiz questions -->
    <div class="new-quiz-questions">
        <ul x-data="questions">
            <template x-for="(question, questionIndex) in questions">

                <li class="new-quiz-question">
                    <!-- Question text -->
                    <label>
                        <input class="new-quiz-question-text" :id="$id(`${questionIndex}`)" type="text" x-model="question.text" placeholder="Question text">
                    </label>

                    <!-- Question error message -->
                    <strong class="error" x-text="question.error"></strong>

                    <!-- Question choices -->
                    <template x-for="(choice, choiceIndex) in question.choices">

                        <!-- Question choice -->
                        <div class="new-quiz-question-choice">
                            <!-- Correctness checkbox -->
                            <label>
                                <input type="checkbox" :id="(`${questionIndex}-${choiceIndex}-checkbox`)" name="choice" x-bind:value="choice.text" x-bind:checked="choice.isCorrect" x-on:click="choice.isCorrect = !choice.isCorrect">
                            </label>

                            <!-- Choice text -->
                            <label>
                                <input class="new-quiz-choice-text" :id="$id(`${questionIndex}-${choiceIndex}-text`)" type="text" x-model="choice.text" placeholder="Choice text">
                            </label>

                            <!-- Choice error message -->
                            <strong class="error" x-text="choice.error"></strong>
                        </div>
                    </template>

                    <!-- Question delete button -->
                    <div class="new-quiz-question-delete-button">
                        <button x-on:click="questions.splice(questionIndex, 1)">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </li>

            </template>
        </ul>
    </div>


    <div class="button-container">
        <!-- Question add button -->
        <button x-on:click="addQuestion">Add question</button>

        <!-- Quiz submit button -->
        <button class="new-quiz-submit-button" x-on:click="submitQuiz">Submit</button>
    </div>

    <div class="global-error">
        <h2 x-text="globalError"></h2>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('quiz', () => ({
                title: '',
                titleError: '',
                globalError: '',

                questions: [],

                addQuestion() {
                    this.questions.push({
                        text: '',
                        error: '',

                        choices: [
                            { text: '', isCorrect: false, error: '', },
                            { text: '', isCorrect: false, error: '', },
                            { text: '', isCorrect: false, error: '', },
                            { text: '', isCorrect: false, error: '', },
                        ],
                    });
                },

                validateQuiz() {
                    this.globalError = '';

                    // reset title error value
                    this.titleError = '';

                    // validate quiz title
                    if (this.title === '') {
                        this.titleError = 'Quiz title is required.';
                        return false;
                    }

                    // at lest one question is required
                    if (this.questions.length < 1) {
                        this.titleError = 'At least one question is required.';
                        return false;
                    }

                    // validate quiz questions
                    for (let question of this.questions) {
                        // reset question error value
                        question.error = '';

                        if (question.text === '') {
                            question.error = 'Question text is required.';
                            return false;
                        }

                        // validate choices
                        let correctChoiceCount = 0;
                        let choiceErrorCount = 0;
                        for (let choice of question.choices) {
                            // reset choice error value
                            choice.error = '';

                            // check if choice has a text
                            if (choice.text === '') {
                                choiceErrorCount++;
                                choice.error = 'Choice text is required.'
                            }

                            // count the correct choice
                            if (choice.isCorrect) {
                                correctChoiceCount++;
                            }
                        }

                        // error count must be 0
                        if (choiceErrorCount !== 0) {
                            return false;
                        }

                        // at least one correct choice is required
                        if (correctChoiceCount <= 0) {
                            question.error = 'At least one correct choice is required.';
                            return false;
                        }
                    }

                    return true;
                },

                submitQuiz() {
                    // validate quiz form correctness
                    if (this.validateQuiz()) {

                        // Submit a new quiz
                        fetch('/api/new', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                title: this.title,
                                questions: this.questions,
                            }),
                        })
                            .then(response =>  {
                                if (response.status === 201) {
                                    // Redirect to the home page
                                    document.location.href = "/";
                                } else {
                                    this.globalError = 'Could not submit a new quiz.';
                                }
                            })
                            // Handle errors
                            .catch((err) => {
                                console.error(err);

                                this.globalError = 'Could not submit a new quiz.';
                            });
                    }

                    // TODO: Remove later
                    console.log({title: this.title, questions: JSON.parse(JSON.stringify(this.questions))});
                },
            }))
        })
    </script>
</body>

</html>
