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
<!-- TODO: Moar alpine -->
<!-- TODO: Add question completion status in 'one question per page mode' -->

<body x-data="quiz">

    @include('shared.gohome')

    <!-- Quiz title -->
    <h1 class="quiz-title">{{ $quiz->getTitle() }}</h1>

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
        @foreach($quiz->getQuestions() as $key => $question)
            <section id="{{ $question->getUUID() }}" class="quiz-question" x-show="showAllQuestions || (currentQuestionIdx == {{ $key }})">
                <h3>Question {{ $key }}</h3>

                <fieldset>
                    <legend>
                        {{ $question->getText() }}
                    </legend>

                    @foreach($question->getChoices() as $key => $choice)
                        <div>
                            <input type="checkbox" id="{{ $choice->getUUID() }}" name="choice" value="{{ $choice->getUUID() }}" class="quiz-choice-checkbox">
                            <label for="{{ $choice->getUUID() }}">{{ $choice->getText() }}</label>
                        </div>
                    @endforeach
                </fieldset>
            </section>
        @endforeach
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
        <button id="quiz-submit-button" onclick="">Submit</button>
    </div>

    <div class="result-container">
        <h3 id="quiz-result">Submit to get quiz results..</h3>
    </div>

    <script>
        // Submit button for the quiz
        let submitButton = document.getElementById('quiz-submit-button');
        // Quiz result text container
        let quizResultContainer = document.getElementById('quiz-result');


        if (submitButton) {
            submitButton.addEventListener('click', (evt) => {
                const quizContents = document.getElementById('quiz-contents');

                if (quizContents) {
                    // Get quiz questions' choices
                    let answers = [];
                    const quizQuestions = quizContents.getElementsByClassName('quiz-question');

                    // `for` loop for older browser support
                    for (let questionIdx = 0; questionIdx < quizQuestions.length; questionIdx++) {
                        let answer = { questionUUID: quizQuestions[questionIdx].id ?? '', choices: [] };

                        const questionChoices = quizQuestions[questionIdx].getElementsByClassName('quiz-choice-checkbox');

                        for (let choiceIdx = 0; choiceIdx < questionChoices.length; choiceIdx++) {
                            if (questionChoices[choiceIdx].checked) {
                                if (questionChoices[choiceIdx].id) {
                                    answer.choices.push(questionChoices[choiceIdx].id);
                                }
                            }
                        }

                        answers.push(answer);
                    }

                    // Construct answers
                    const quizAnswers = {
                        quizUUID: `{{ $quiz->getUUID() }}`,
                        answers: answers,
                    };

                    // Submit quiz answers
                    fetch('/api/submit', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(quizAnswers),
                    })
                    .then(response => response.json())
                    // Set the quiz result percentage
                    .then(data => {
                        // TODO: Here for the debugging, remove later
                        console.log(data);

                        if (quizResultContainer) {
                            // 100% Completion is celebrated with confetti !!!
                            if (data.result === 1) {
                                confetti({
                                    spread: 180,
                                });
                            }

                            quizResultContainer.innerText = data.result !== undefined ? `Quiz completion rate: ${data.result * 100}%` : 'Could not calculate quiz results!';
                        }
                    })
                    // Handle the quiz validation errors
                    .catch((err) => {
                        if (quizResultContainer) {
                            // Log error
                            console.error(err);
                            // Change result container text
                            quizResultContainer.innerText = 'Could not submit quiz answers!';
                        }
                    });
                }
            });
        }
    </script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('quiz', () => ({
                showAllQuestions: true,

                currentQuestionIdx: 0,
                questionCount: {{ count($quiz->getQuestions()) }},

                // NOTE: I think it would be better to store questions like that to show their completion status is real time
                quiz: {
                    title: `{{ $quiz->getTitle() }}`,
                    uuid: `{{ $quiz->getUUID() }}`,
                    questions: [
                            @foreach($quiz->getQuestions() as $key => $question)
                        {
                            uuid: `{{ $question->getUUID() }}`,
                            text: `{{ $question->getText() }}`,
                            choices: [
                                    @foreach($question->getChoices() as $key => $choice)
                                {
                                    uuid: `{{ $choice->getUUID() }}`,
                                    text: `{{ $choice->getText() }}`,

                                    // did user select this question
                                    isSelected: false,

                                    // did user visit this question
                                    isVisited: false,
                                },
                                @endforeach
                            ],
                        },
                        @endforeach
                    ],
                },

                submitQuestions() {
                    // Flat map questions to answers
                    const answers = this.quiz.questions.flatMap(question => [
                        {
                            questionUUID: question.uuid,

                            // Filter only the selected choices and map a choice to it's UUID
                            choices: question.choices.filter(choice => choice.isSelected).flatMap(selectedChoice => [ selectedChoice.uuid ]),
                        }
                    ]);

                    // DO the fetch ...
                },

                nextQuestion() {
                    this.currentQuestionIdx = (this.currentQuestionIdx + 1) % this.questionCount;
                },

                prevQuestion() {
                    this.currentQuestionIdx = (this.currentQuestionIdx - 1 + this.questionCount) % this.questionCount;
                },

                oneQuestion() {
                    this.showAllQuestions = false;
                },

                allQuestions() {
                    this.showAllQuestions = true;
                },
            }));
        });
    </script>
</body>

</html>
