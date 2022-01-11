<?php

namespace Tests\Unit;

use App\DTO\ChoiceDTO;
use App\DTO\QuizDTO;
use App\Service\QuizResultService;
use PHPUnit\Framework\TestCase;
use App\Utils\Serializer;

class SampleCasesQuizTest extends TestCase
{
    protected QuizDTO $quiz;

    protected function setUp(): void
    {
        $this->quiz = Serializer::CreateQuiz(
            '1',
            'Test?',
            ...[
                // Question 1
                Serializer::CreateQuestion('1-1', 'Hehe?', ...[
                    new ChoiceDTO('1-1-1', 'Hehe', true),   // Choice A
                    new ChoiceDTO('1-1-2', 'haha', false),  // Choice B
                    new ChoiceDTO('1-1-3', 'what?', false), // Choice C
                    new ChoiceDTO('1-1-4', '...', false),   // Choice D
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
    }

    public function testCase1()
    {
        $answers = Serializer::CreateAnswers(
            $this->quiz->getUUID(),
            ...[
                // Question 1
                Serializer::CreateAnswer($this->quiz->getQuestions()[0]->getUUID(),
                ...[
                        $this->quiz->getQuestions()[0]->getChoices()[0]->getUUID(), // 0 => A
                    ]
                ),

                // Question 2
                Serializer::CreateAnswer($this->quiz->getQuestions()[1]->getUUID(),
                ...[
                        $this->quiz->getQuestions()[1]->getChoices()[1]->getUUID(), // 1 => B
                        $this->quiz->getQuestions()[1]->getChoices()[2]->getUUID(), // 2 => C
                    ]
                ),

                // Question 3
                Serializer::CreateAnswer($this->quiz->getQuestions()[2]->getUUID(),
                ...[
                        $this->quiz->getQuestions()[2]->getChoices()[3]->getUUID(), // 3 => D
                    ]
                ),
            ]
        );

        $quizResultService = new QuizResultService(
            $this->quiz,
            $answers
        );

        $result = $quizResultService->getResult();

        $this->assertEquals(1, $result);
    }

    public function testCase2()
    {
        $answers = Serializer::CreateAnswers(
            $this->quiz->getUUID(),
            ...[
                // Question 1
                Serializer::CreateAnswer($this->quiz->getQuestions()[0]->getUUID(),
                    ...[
                        $this->quiz->getQuestions()[0]->getChoices()[0]->getUUID(), // 0 => A
                    ]
                ),

                // Question 2
                Serializer::CreateAnswer($this->quiz->getQuestions()[1]->getUUID(),
                    ...[
                        $this->quiz->getQuestions()[1]->getChoices()[1]->getUUID(), // 1 => B
                    ]
                ),

                // Question 3
                Serializer::CreateAnswer($this->quiz->getQuestions()[2]->getUUID(),
                    ...[
                        $this->quiz->getQuestions()[2]->getChoices()[3]->getUUID(), // 3 => D
                    ]
                ),
            ]
        );

        $quizResultService = new QuizResultService(
            $this->quiz,
            $answers
        );

        $result = $quizResultService->getResult();

        $this->assertEquals(0.67, $result);
    }

    public function testCase3()
    {
        $answers = Serializer::CreateAnswers(
            $this->quiz->getUUID(),
            ...[
                // Question 1
                Serializer::CreateAnswer($this->quiz->getQuestions()[0]->getUUID(),
                    ...[
                        $this->quiz->getQuestions()[0]->getChoices()[0]->getUUID(), // 0 => A
                    ]
                ),

                // Question 2
                Serializer::CreateAnswer($this->quiz->getQuestions()[1]->getUUID(),
                    ...[
                        $this->quiz->getQuestions()[1]->getChoices()[0]->getUUID(), // 0 => A
                    ]
                ),

                // Question 3
                Serializer::CreateAnswer($this->quiz->getQuestions()[2]->getUUID(),
                    ...[
                        $this->quiz->getQuestions()[2]->getChoices()[0]->getUUID(), // 0 => A
                    ]
                ),
            ]
        );

        $quizResultService = new QuizResultService(
            $this->quiz,
            $answers
        );

        $result = $quizResultService->getResult();

        $this->assertEquals(0.33, $result);
    }

    public function testCase4()
    {
        $answers = Serializer::CreateAnswers(
            $this->quiz->getUUID(),
            ...[
                // Question 1
                Serializer::CreateAnswer($this->quiz->getQuestions()[0]->getUUID(),
                    ...[
                        $this->quiz->getQuestions()[0]->getChoices()[0]->getUUID(), // 0 => A
                    ]
                ),

                // Question 2
                Serializer::CreateAnswer($this->quiz->getQuestions()[1]->getUUID(),
                    ...[
                        $this->quiz->getQuestions()[1]->getChoices()[0]->getUUID(), // 0 => A
                        $this->quiz->getQuestions()[1]->getChoices()[1]->getUUID(), // 1 => B
                        $this->quiz->getQuestions()[1]->getChoices()[2]->getUUID(), // 2 => C
                    ]
                ),

                // Question 3
                Serializer::CreateAnswer($this->quiz->getQuestions()[2]->getUUID(),
                    ...[
                        $this->quiz->getQuestions()[2]->getChoices()[3]->getUUID(), // 3 => D
                    ]
                ),
            ]
        );

        $quizResultService = new QuizResultService(
            $this->quiz,
            $answers
        );

        $result = $quizResultService->getResult();

        $this->assertEquals(0.67, $result);
    }

    public function testCase5()
    {
        $answers = Serializer::CreateAnswers(
            $this->quiz->getUUID(),
            ...[
                // Question 1
                Serializer::CreateAnswer($this->quiz->getQuestions()[0]->getUUID(),
                    ...[
                        $this->quiz->getQuestions()[0]->getChoices()[0]->getUUID(), // 0 => A
                        $this->quiz->getQuestions()[0]->getChoices()[3]->getUUID(), // 3 => D
                    ]
                ),

                // Question 2
                Serializer::CreateAnswer($this->quiz->getQuestions()[1]->getUUID(),
                    ...[
                        $this->quiz->getQuestions()[1]->getChoices()[1]->getUUID(), // 1 => B
                        $this->quiz->getQuestions()[1]->getChoices()[2]->getUUID(), // 2 => C
                    ]
                ),

                // Question 3
                Serializer::CreateAnswer($this->quiz->getQuestions()[2]->getUUID(),
                    ...[
                        $this->quiz->getQuestions()[2]->getChoices()[3]->getUUID(), // 3 => D
                    ]
                ),
            ]
        );

        $quizResultService = new QuizResultService(
            $this->quiz,
            $answers
        );

        $result = $quizResultService->getResult();

        $this->assertEquals(0.67, $result);
    }
}
