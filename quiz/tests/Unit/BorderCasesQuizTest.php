<?php

namespace Tests\Unit;

use App\DTO\ChoiceDTO;
use App\Service\QuizResultService;
use App\Utils\Serializer;
use PHPUnit\Framework\TestCase;

class BorderCasesQuizTest extends TestCase
{
    public function testDifferentQuizUUID()
    {
        $quiz = Serializer::CreateQuiz(
            '1',
            'Test?',
            ...[
                // Question 1
                Serializer::CreateQuestion('1-1', 'Hehe?', ...[
                    new ChoiceDTO('1-1-2', 'Hehe', true),   // Choice A
                    new ChoiceDTO('1-1-3', 'haha', false),  // Choice B
                    new ChoiceDTO('1-1-4', 'what?', false), // Choice C
                    new ChoiceDTO('1-1-1', '...', false),   // Choice D
                ]),

                // Question 2
                Serializer::CreateQuestion('1-2', 'The correct answers are B and C.', ...[
                    new ChoiceDTO('1-2-1', 'A', false), // Choice A
                    new ChoiceDTO('1-2-2', 'B', true), // Choice B
                    new ChoiceDTO('1-2-3', 'C', true), // Choice C
                    new ChoiceDTO('1-2-4', 'D', false), // Choice D
                ]),

                // Question 3
                Serializer::CreateQuestion('1-3', 'Answer to everything.', ...[
                    new ChoiceDTO('1-3-1', '69', false), // Choice A
                    new ChoiceDTO('1-3-2', '420', false), // Choice B
                    new ChoiceDTO('1-3-3', '39', false), // Choice C
                    new ChoiceDTO('1-3-4', '42', true), // Choice D
                ]),
            ]
        );

        $answers = Serializer::CreateAnswers(
            'Different Quiz UUID',

            ...[
                // Question 1
                Serializer::CreateAnswer($quiz->getQuestions()[0]->getUUID(),
                    ...[
                        $quiz->getQuestions()[0]->getChoices()[0]->getUUID(), // 0 => A
                    ]
                ),

                // Question 2
                Serializer::CreateAnswer($quiz->getQuestions()[1]->getUUID(),
                    ...[
                        $quiz->getQuestions()[1]->getChoices()[1]->getUUID(), // 1 => B
                        $quiz->getQuestions()[1]->getChoices()[2]->getUUID(), // 2 => C
                    ]
                ),

                // Question 3
                Serializer::CreateAnswer($quiz->getQuestions()[2]->getUUID(),
                    ...[
                        $quiz->getQuestions()[2]->getChoices()[3]->getUUID(), // 3 => D
                    ]
                ),
            ]
        );

        $quizResultService = new QuizResultService(
            $quiz,
            $answers
        );

        $result = $quizResultService->getResult();

        $this->assertEquals(0, $result);
    }

    public function testExtraCorrectChoices()
    {
        $quiz = Serializer::CreateQuiz(
            '1',
            'Test?',
            ...[
                // Question 1
                Serializer::CreateQuestion('1-1', 'Hehe?', ...[
                    new ChoiceDTO('1-1-2', 'Hehe', true),   // Choice A
                    new ChoiceDTO('1-1-3', 'haha', false),  // Choice B
                    new ChoiceDTO('1-1-4', 'what?', false), // Choice C
                    new ChoiceDTO('1-1-1', '...', false),   // Choice D
                ]),

                // Question 2
                Serializer::CreateQuestion('1-2', 'The correct answers are B and C.', ...[
                    new ChoiceDTO('1-2-1', 'A', false), // Choice A
                    new ChoiceDTO('1-2-2', 'B', true), // Choice B
                    new ChoiceDTO('1-2-3', 'C', true), // Choice C
                    new ChoiceDTO('1-2-4', 'D', false), // Choice D
                ]),

                // Question 3
                Serializer::CreateQuestion('1-3', 'Answer to everything.', ...[
                    new ChoiceDTO('1-3-1', '69', false), // Choice A
                    new ChoiceDTO('1-3-2', '420', false), // Choice B
                    new ChoiceDTO('1-3-3', '39', false), // Choice C
                    new ChoiceDTO('1-3-4', '42', true), // Choice D
                ]),
            ]
        );

        $answers = Serializer::CreateAnswers(
            $quiz->getUUID(),
            ...[
                // Question 1
                Serializer::CreateAnswer($quiz->getQuestions()[0]->getUUID(),
                    ...[
                        $quiz->getQuestions()[0]->getChoices()[0]->getUUID(), // 0 => A

                        // Extra correct choices:
                        $quiz->getQuestions()[0]->getChoices()[0]->getUUID(), // 0 => A
                    ]
                ),

                // Question 2
                Serializer::CreateAnswer($quiz->getQuestions()[1]->getUUID(),
                    ...[
                        $quiz->getQuestions()[1]->getChoices()[1]->getUUID(), // 1 => B
                        $quiz->getQuestions()[1]->getChoices()[2]->getUUID(), // 2 => C

                        // Extra correct choices:
                        $quiz->getQuestions()[1]->getChoices()[1]->getUUID(), // 1 => B
                        $quiz->getQuestions()[1]->getChoices()[2]->getUUID(), // 2 => C
                    ]
                ),

                // Question 3
                Serializer::CreateAnswer($quiz->getQuestions()[2]->getUUID(),
                    ...[
                        $quiz->getQuestions()[2]->getChoices()[3]->getUUID(), // 3 => D

                        // Extra correct choices:
                        $quiz->getQuestions()[2]->getChoices()[3]->getUUID(), // 3 => D
                    ]
                ),
            ]
        );

        $quizResultService = new QuizResultService(
            $quiz,
            $answers
        );

        $result = $quizResultService->getResult();

        $this->assertEquals(1, $result);
    }

    public function testExtraIncorrectChoices()
    {
        $quiz = Serializer::CreateQuiz(
            '1',
            'Test?',
            ...[
                // Question 1
                Serializer::CreateQuestion('1-1', 'Hehe?', ...[
                    new ChoiceDTO('1-1-2', 'Hehe', true),   // Choice A
                    new ChoiceDTO('1-1-3', 'haha', false),  // Choice B
                    new ChoiceDTO('1-1-4', 'what?', false), // Choice C
                    new ChoiceDTO('1-1-1', '...', false),   // Choice D
                ]),

                // Question 2
                Serializer::CreateQuestion('1-2', 'The correct answers are B and C.', ...[
                    new ChoiceDTO('1-2-1', 'A', false), // Choice A
                    new ChoiceDTO('1-2-2', 'B', true), // Choice B
                    new ChoiceDTO('1-2-3', 'C', true), // Choice C
                    new ChoiceDTO('1-2-4', 'D', false), // Choice D
                ]),

                // Question 3
                Serializer::CreateQuestion('1-3', 'Answer to everything.', ...[
                    new ChoiceDTO('1-3-1', '69', false), // Choice A
                    new ChoiceDTO('1-3-2', '420', false), // Choice B
                    new ChoiceDTO('1-3-3', '39', false), // Choice C
                    new ChoiceDTO('1-3-4', '42', true), // Choice D
                ]),
            ]
        );

        $answers = Serializer::CreateAnswers(
            $quiz->getUUID(),
            ...[
                // Question 1
                Serializer::CreateAnswer($quiz->getQuestions()[0]->getUUID(),
                    ...[
                        $quiz->getQuestions()[0]->getChoices()[0]->getUUID(), // 0 => A

                        // Extra incorrect choices:
                        $quiz->getQuestions()[0]->getChoices()[1]->getUUID(), // 1 => B
                    ]
                ),

                // Question 2
                Serializer::CreateAnswer($quiz->getQuestions()[1]->getUUID(),
                    ...[
                        $quiz->getQuestions()[1]->getChoices()[1]->getUUID(), // 1 => B
                        $quiz->getQuestions()[1]->getChoices()[2]->getUUID(), // 2 => C

                        // Extra incorrect choices:
                        $quiz->getQuestions()[1]->getChoices()[3]->getUUID(), // 3 => D
                    ]
                ),

                // Question 3
                Serializer::CreateAnswer($quiz->getQuestions()[2]->getUUID(),
                    ...[
                        $quiz->getQuestions()[2]->getChoices()[3]->getUUID(), // 3 => D

                        // Extra incorrect choices:
                        $quiz->getQuestions()[2]->getChoices()[1]->getUUID(), // 1 => B
                    ]
                ),
            ]
        );

        $quizResultService = new QuizResultService(
            $quiz,
            $answers
        );

        $result = $quizResultService->getResult();

        $this->assertEquals(0, $result);
    }

    public function testExtraChoicesWithDifferentChoiceUUID()
    {
        $quiz = Serializer::CreateQuiz(
            '1',
            'Test?',
            ...[
                // Question 1
                Serializer::CreateQuestion('1-1', 'Hehe?', ...[
                    new ChoiceDTO('1-1-2', 'Hehe', true),   // Choice A
                    new ChoiceDTO('1-1-3', 'haha', false),  // Choice B
                    new ChoiceDTO('1-1-4', 'what?', false), // Choice C
                    new ChoiceDTO('1-1-1', '...', false),   // Choice D
                ]),

                // Question 2
                Serializer::CreateQuestion('1-2', 'The correct answers are B and C.', ...[
                    new ChoiceDTO('1-2-1', 'A', false), // Choice A
                    new ChoiceDTO('1-2-2', 'B', true), // Choice B
                    new ChoiceDTO('1-2-3', 'C', true), // Choice C
                    new ChoiceDTO('1-2-4', 'D', false), // Choice D
                ]),

                // Question 3
                Serializer::CreateQuestion('1-3', 'Answer to everything.', ...[
                    new ChoiceDTO('1-3-1', '69', false), // Choice A
                    new ChoiceDTO('1-3-2', '420', false), // Choice B
                    new ChoiceDTO('1-3-3', '39', false), // Choice C
                    new ChoiceDTO('1-3-4', '42', true), // Choice D
                ]),
            ]
        );

        $answers = Serializer::CreateAnswers(
            $quiz->getUUID(),
            ...[
                // Question 1
                Serializer::CreateAnswer($quiz->getQuestions()[0]->getUUID(),
                    ...[
                        $quiz->getQuestions()[0]->getChoices()[0]->getUUID(), // 0 => A

                        // Different UUID
                        'Different UUID',
                    ]
                ),

                // Question 2
                Serializer::CreateAnswer($quiz->getQuestions()[1]->getUUID(),
                    ...[
                        $quiz->getQuestions()[1]->getChoices()[1]->getUUID(), // 1 => B
                        $quiz->getQuestions()[1]->getChoices()[2]->getUUID(), // 2 => C

                        // Different UUID
                        'Different UUID',
                    ]
                ),

                // Question 3
                Serializer::CreateAnswer($quiz->getQuestions()[2]->getUUID(),
                    ...[
                        $quiz->getQuestions()[2]->getChoices()[3]->getUUID(), // 3 => D

                        // Different UUID
                        'Different UUID',
                    ]
                ),
            ]
        );

        $quizResultService = new QuizResultService(
            $quiz,
            $answers
        );

        $result = $quizResultService->getResult();

        $this->assertEquals(0, $result);
    }

    public function testWithExtraCorrectAnswer()
    {
        $quiz = Serializer::CreateQuiz(
            '1',
            'Test?',
            ...[
                // Question 1
                Serializer::CreateQuestion('1-1', 'Hehe?', ...[
                    new ChoiceDTO('1-1-2', 'Hehe', true),   // Choice A
                    new ChoiceDTO('1-1-3', 'haha', false),  // Choice B
                    new ChoiceDTO('1-1-4', 'what?', false), // Choice C
                    new ChoiceDTO('1-1-1', '...', false),   // Choice D
                ]),

                // Question 2
                Serializer::CreateQuestion('1-2', 'The correct answers are B and C.', ...[
                    new ChoiceDTO('1-2-1', 'A', false), // Choice A
                    new ChoiceDTO('1-2-2', 'B', true), // Choice B
                    new ChoiceDTO('1-2-3', 'C', true), // Choice C
                    new ChoiceDTO('1-2-4', 'D', false), // Choice D
                ]),

                // Question 3
                Serializer::CreateQuestion('1-3', 'Answer to everything.', ...[
                    new ChoiceDTO('1-3-1', '69', false), // Choice A
                    new ChoiceDTO('1-3-2', '420', false), // Choice B
                    new ChoiceDTO('1-3-3', '39', false), // Choice C
                    new ChoiceDTO('1-3-4', '42', true), // Choice D
                ]),
            ]
        );

        $answers = Serializer::CreateAnswers(
            $quiz->getUUID(),
            ...[
                // Question 1
                Serializer::CreateAnswer($quiz->getQuestions()[0]->getUUID(),
                    ...[
                        $quiz->getQuestions()[0]->getChoices()[0]->getUUID(), // 0 => A
                    ]
                ),

                // Question 2
                Serializer::CreateAnswer($quiz->getQuestions()[1]->getUUID(),
                    ...[
                        $quiz->getQuestions()[1]->getChoices()[1]->getUUID(), // 1 => B
                        $quiz->getQuestions()[1]->getChoices()[2]->getUUID(), // 2 => C
                    ]
                ),

                // Question 3
                Serializer::CreateAnswer($quiz->getQuestions()[2]->getUUID(),
                    ...[
                        $quiz->getQuestions()[2]->getChoices()[3]->getUUID(), // 3 => D
                    ]
                ),

                // Extra question answer
                Serializer::CreateAnswer($quiz->getQuestions()[2]->getUUID(),
                    ...[
                        $quiz->getQuestions()[2]->getChoices()[3]->getUUID(), // 3 => D
                    ]
                ),
            ]
        );

        $quizResultService = new QuizResultService(
            $quiz,
            $answers
        );

        $result = $quizResultService->getResult();

        $this->assertEquals(1, $result);
    }


    public function testWithExtraIncorrectAnswer()
    {
        $quiz = Serializer::CreateQuiz(
            '1',
            'Test?',
            ...[
                // Question 1
                Serializer::CreateQuestion('1-1', 'Hehe?', ...[
                    new ChoiceDTO('1-1-2', 'Hehe', true),   // Choice A
                    new ChoiceDTO('1-1-3', 'haha', false),  // Choice B
                    new ChoiceDTO('1-1-4', 'what?', false), // Choice C
                    new ChoiceDTO('1-1-1', '...', false),   // Choice D
                ]),

                // Question 2
                Serializer::CreateQuestion('1-2', 'The correct answers are B and C.', ...[
                    new ChoiceDTO('1-2-1', 'A', false), // Choice A
                    new ChoiceDTO('1-2-2', 'B', true), // Choice B
                    new ChoiceDTO('1-2-3', 'C', true), // Choice C
                    new ChoiceDTO('1-2-4', 'D', false), // Choice D
                ]),

                // Question 3
                Serializer::CreateQuestion('1-3', 'Answer to everything.', ...[
                    new ChoiceDTO('1-3-1', '69', false), // Choice A
                    new ChoiceDTO('1-3-2', '420', false), // Choice B
                    new ChoiceDTO('1-3-3', '39', false), // Choice C
                    new ChoiceDTO('1-3-4', '42', true), // Choice D
                ]),
            ]
        );

        $answers = Serializer::CreateAnswers(
            $quiz->getUUID(),
            ...[
                // Question 1
                Serializer::CreateAnswer($quiz->getQuestions()[0]->getUUID(),
                    ...[
                        $quiz->getQuestions()[0]->getChoices()[0]->getUUID(), // 0 => A
                    ]
                ),

                // Question 2
                Serializer::CreateAnswer($quiz->getQuestions()[1]->getUUID(),
                    ...[
                        $quiz->getQuestions()[1]->getChoices()[1]->getUUID(), // 1 => B
                        $quiz->getQuestions()[1]->getChoices()[2]->getUUID(), // 2 => C
                    ]
                ),

                // Question 3
                Serializer::CreateAnswer($quiz->getQuestions()[2]->getUUID(),
                    ...[
                        $quiz->getQuestions()[2]->getChoices()[3]->getUUID(), // 3 => D
                    ]
                ),

                // Extra incorrect question answer
                Serializer::CreateAnswer($quiz->getQuestions()[2]->getUUID(),
                    ...[
                        $quiz->getQuestions()[2]->getChoices()[2]->getUUID(), // 2 => C
                    ]
                ),
            ]
        );

        $quizResultService = new QuizResultService(
            $quiz,
            $answers
        );

        $result = $quizResultService->getResult();

        $this->assertEquals(1, $result);
    }


    public function testWithExtraRandomAnswerChoiceUUID()
    {
        $quiz = Serializer::CreateQuiz(
            '1',
            'Test?',
            ...[
                // Question 1
                Serializer::CreateQuestion('1-1', 'Hehe?', ...[
                    new ChoiceDTO('1-1-2', 'Hehe', true),   // Choice A
                    new ChoiceDTO('1-1-3', 'haha', false),  // Choice B
                    new ChoiceDTO('1-1-4', 'what?', false), // Choice C
                    new ChoiceDTO('1-1-1', '...', false),   // Choice D
                ]),

                // Question 2
                Serializer::CreateQuestion('1-2', 'The correct answers are B and C.', ...[
                    new ChoiceDTO('1-2-1', 'A', false), // Choice A
                    new ChoiceDTO('1-2-2', 'B', true), // Choice B
                    new ChoiceDTO('1-2-3', 'C', true), // Choice C
                    new ChoiceDTO('1-2-4', 'D', false), // Choice D
                ]),

                // Question 3
                Serializer::CreateQuestion('1-3', 'Answer to everything.', ...[
                    new ChoiceDTO('1-3-1', '69', false), // Choice A
                    new ChoiceDTO('1-3-2', '420', false), // Choice B
                    new ChoiceDTO('1-3-3', '39', false), // Choice C
                    new ChoiceDTO('1-3-4', '42', true), // Choice D
                ]),
            ]
        );

        $answers = Serializer::CreateAnswers(
            $quiz->getUUID(),
            ...[
                // Question 1
                Serializer::CreateAnswer($quiz->getQuestions()[0]->getUUID(),
                    ...[
                        $quiz->getQuestions()[0]->getChoices()[0]->getUUID(), // 0 => A
                    ]
                ),

                // Question 2
                Serializer::CreateAnswer($quiz->getQuestions()[1]->getUUID(),
                    ...[
                        $quiz->getQuestions()[1]->getChoices()[1]->getUUID(), // 1 => B
                        $quiz->getQuestions()[1]->getChoices()[2]->getUUID(), // 2 => C
                    ]
                ),

                // Question 3
                Serializer::CreateAnswer($quiz->getQuestions()[2]->getUUID(),
                    ...[
                        $quiz->getQuestions()[2]->getChoices()[3]->getUUID(), // 3 => D
                    ]
                ),

                // Extra random question answer
                Serializer::CreateAnswer($quiz->getQuestions()[2]->getUUID(),
                    ...[
                        'Random UUID',
                    ]
                ),
            ]
        );

        $quizResultService = new QuizResultService(
            $quiz,
            $answers
        );

        $result = $quizResultService->getResult();

        $this->assertEquals(1, $result);
    }

    public function testWithExtraRandomAnswerUUID()
    {
        $quiz = Serializer::CreateQuiz(
            '1',
            'Test?',
            ...[
                // Question 1
                Serializer::CreateQuestion('1-1', 'Hehe?', ...[
                    new ChoiceDTO('1-1-2', 'Hehe', true),   // Choice A
                    new ChoiceDTO('1-1-3', 'haha', false),  // Choice B
                    new ChoiceDTO('1-1-4', 'what?', false), // Choice C
                    new ChoiceDTO('1-1-1', '...', false),   // Choice D
                ]),

                // Question 2
                Serializer::CreateQuestion('1-2', 'The correct answers are B and C.', ...[
                    new ChoiceDTO('1-2-1', 'A', false), // Choice A
                    new ChoiceDTO('1-2-2', 'B', true), // Choice B
                    new ChoiceDTO('1-2-3', 'C', true), // Choice C
                    new ChoiceDTO('1-2-4', 'D', false), // Choice D
                ]),

                // Question 3
                Serializer::CreateQuestion('1-3', 'Answer to everything.', ...[
                    new ChoiceDTO('1-3-1', '69', false), // Choice A
                    new ChoiceDTO('1-3-2', '420', false), // Choice B
                    new ChoiceDTO('1-3-3', '39', false), // Choice C
                    new ChoiceDTO('1-3-4', '42', true), // Choice D
                ]),
            ]
        );

        $answers = Serializer::CreateAnswers(
            $quiz->getUUID(),
            ...[
                // Question 1
                Serializer::CreateAnswer($quiz->getQuestions()[0]->getUUID(),
                    ...[
                        $quiz->getQuestions()[0]->getChoices()[0]->getUUID(), // 0 => A
                    ]
                ),

                // Question 2
                Serializer::CreateAnswer($quiz->getQuestions()[1]->getUUID(),
                    ...[
                        $quiz->getQuestions()[1]->getChoices()[1]->getUUID(), // 1 => B
                        $quiz->getQuestions()[1]->getChoices()[2]->getUUID(), // 2 => C
                    ]
                ),

                // Question 3
                Serializer::CreateAnswer($quiz->getQuestions()[2]->getUUID(),
                    ...[
                        $quiz->getQuestions()[2]->getChoices()[3]->getUUID(), // 3 => D
                    ]
                ),

                // Extra random question answer
                Serializer::CreateAnswer('Random',
                    ...[
                        'Random UUID',
                    ]
                ),
            ]
        );

        $quizResultService = new QuizResultService(
            $quiz,
            $answers
        );

        $result = $quizResultService->getResult();

        $this->assertEquals(1, $result);
    }

    public function testWithOneMissingAnswer()
    {
        $quiz = Serializer::CreateQuiz(
            '1',
            'Test?',
            ...[
                // Question 1
                Serializer::CreateQuestion('1-1', 'Hehe?', ...[
                    new ChoiceDTO('1-1-2', 'Hehe', true),   // Choice A
                    new ChoiceDTO('1-1-3', 'haha', false),  // Choice B
                    new ChoiceDTO('1-1-4', 'what?', false), // Choice C
                    new ChoiceDTO('1-1-1', '...', false),   // Choice D
                ]),

                // Question 2
                Serializer::CreateQuestion('1-2', 'The correct answers are B and C.', ...[
                    new ChoiceDTO('1-2-1', 'A', false), // Choice A
                    new ChoiceDTO('1-2-2', 'B', true), // Choice B
                    new ChoiceDTO('1-2-3', 'C', true), // Choice C
                    new ChoiceDTO('1-2-4', 'D', false), // Choice D
                ]),

                // Question 3
                Serializer::CreateQuestion('1-3', 'Answer to everything.', ...[
                    new ChoiceDTO('1-3-1', '69', false), // Choice A
                    new ChoiceDTO('1-3-2', '420', false), // Choice B
                    new ChoiceDTO('1-3-3', '39', false), // Choice C
                    new ChoiceDTO('1-3-4', '42', true), // Choice D
                ]),

                // Question 4
                Serializer::CreateQuestion('1-4', 'Answer to everything.', ...[
                    new ChoiceDTO('1-4-1', '69', false), // Choice A
                    new ChoiceDTO('1-4-2', '420', false), // Choice B
                    new ChoiceDTO('1-4-3', '39', false), // Choice C
                    new ChoiceDTO('1-4-4', '42', true), // Choice D
                ]),
            ]
        );

        $answers = Serializer::CreateAnswers(
            $quiz->getUUID(),
            ...[
                // Question 1
                Serializer::CreateAnswer($quiz->getQuestions()[0]->getUUID(),
                    ...[
                        $quiz->getQuestions()[0]->getChoices()[0]->getUUID(), // 0 => A
                    ]
                ),

                // Question 2
                Serializer::CreateAnswer($quiz->getQuestions()[1]->getUUID(),
                    ...[
                        $quiz->getQuestions()[1]->getChoices()[1]->getUUID(), // 1 => B
                        $quiz->getQuestions()[1]->getChoices()[2]->getUUID(), // 2 => C
                    ]
                ),

                // Question 3
                Serializer::CreateAnswer($quiz->getQuestions()[2]->getUUID(),
                    ...[
                        $quiz->getQuestions()[2]->getChoices()[3]->getUUID(), // 3 => D
                    ]
                ),

                // Question 4
                // ... missing
            ]
        );

        $quizResultService = new QuizResultService(
            $quiz,
            $answers
        );

        $result = $quizResultService->getResult();

        $this->assertEquals(0.75, $result);
    }
}
