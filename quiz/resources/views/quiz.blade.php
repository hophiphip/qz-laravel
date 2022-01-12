<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Solve a Quiz</title>

    <link rel="icon" href="/favicon.png">

    <link href="/css/app.css" rel="stylesheet" />
    <link href="/css/quiz.css" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.4.0/dist/confetti.browser.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<!-- TODO: Add one question per page feature and make it default but the user can choose to show all questions at once -->

<body x-data="quiz">
    @include('shared.gohome')

    <div x-show="showAllQuestions">
        <section id="quiz-contents" class="quiz-contents">
            <h1 class="quiz-title">{{ $quiz->getTitle() }}</h1>

            @foreach($quiz->getQuestions() as $key => $question)
                <section id="{{ $question->getUUID() }}" class="quiz-question">
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
    </div>

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
