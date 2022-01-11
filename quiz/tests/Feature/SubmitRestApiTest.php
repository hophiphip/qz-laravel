<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 *  NOTE: All tests here run against the initial Quiz with UUID == 1
 */

class SubmitRestApiTest extends TestCase
{
    public function testRequestWithMissingQuizUUID()
    {
        $response = $this->post('/api/submit', [
            'quizUUID' => null,
            'answers' => [[
                'questionUUID' => '1-1',
                'choices' => [ '1-1-1' ],
            ]],
        ]);

        $response->assertStatus(400);
    }

    public function testRequestWithMissingAnswers()
    {
        $response = $this->post('/api/submit', [
            'quizUUID' => '1',
            'answers' => null,
        ]);

        $response->assertStatus(400);
    }

    public function testRequestWithNonExistentQuizUUID()
    {
        $response = $this->post('/api/submit', [
            'quizUUID' => '-1',
            'answers' => [[
                'questionUUID' => '1-1',
                'choices' => [ '1-1-1' ],
            ]],
        ]);

        $response->assertStatus(404);
    }

    public function testRequestWithOneCorrectAnswer()
    {
        $response = $this->post('/api/submit', [
            'quizUUID' => '1',
            'answers' => [
                [
                    'questionUUID' => '1-1',
                    'choices' => ['1-1-1'],
                ],
            ],
        ]);

        $response->assertStatus(201)->assertExactJson(['result' => 0.33]);
    }

    public function testRequestWithTwoCorrectAnswers()
    {
        $response = $this->post('/api/submit', [
            'quizUUID' => '1',
            'answers' => [
                [
                    'questionUUID' => '1-1',
                    'choices' => ['1-1-1'],
                ],
                [
                    'questionUUID' => '1-2',
                    'choices' => ['1-2-2', '1-2-3'],
                ],
            ],
        ]);

        $response->assertStatus(201)->assertExactJson(['result' => 0.67]);
    }

    public function testRequestWithThreeCorrectAnswers()
    {
        $response = $this->post('/api/submit', [
            'quizUUID' => '1',
            'answers' => [
                [
                    'questionUUID' => '1-1',
                    'choices' => ['1-1-1'],
                ],
                [
                    'questionUUID' => '1-2',
                    'choices' => ['1-2-2', '1-2-3'],
                ],
                [
                    'questionUUID' => '1-3',
                    'choices' => ['1-3-4'],
                ],
            ],
        ]);

        $response->assertStatus(201)->assertExactJson(['result' => 1]);
    }

    public function testRequestWithSampleCase2Answers()
    {
        $response = $this->post('/api/submit', [
            'quizUUID' => '1',
            'answers' => [
                [
                    'questionUUID' => '1-1',
                    'choices' => ['1-1-1'],
                ],
                [
                    'questionUUID' => '1-2',
                    'choices' => ['1-2-2'],
                ],
                [
                    'questionUUID' => '1-3',
                    'choices' => ['1-3-4'],
                ],
            ],
        ]);

        $response->assertStatus(201)->assertExactJson(['result' => 0.67]);
    }

    public function testRequestWithSampleCase3Answers()
    {
        $response = $this->post('/api/submit', [
            'quizUUID' => '1',
            'answers' => [
                [
                    'questionUUID' => '1-1',
                    'choices' => ['1-1-1'],
                ],
                [
                    'questionUUID' => '1-2',
                    'choices' => ['1-2-1'],
                ],
                [
                    'questionUUID' => '1-3',
                    'choices' => ['1-3-1'],
                ],
            ],
        ]);

        $response->assertStatus(201)->assertExactJson(['result' => 0.33]);
    }

    public function testRequestWithSampleCase4Answers()
    {
        $response = $this->post('/api/submit', [
            'quizUUID' => '1',
            'answers' => [
                [
                    'questionUUID' => '1-1',
                    'choices' => ['1-1-1'],
                ],
                [
                    'questionUUID' => '1-2',
                    'choices' => ['1-2-1','1-2-2','1-2-3'],
                ],
                [
                    'questionUUID' => '1-3',
                    'choices' => ['1-3-4'],
                ],
            ],
        ]);

        $response->assertStatus(201)->assertExactJson(['result' => 0.67]);
    }

    public function testRequestWithSampleCase5Answers()
    {
        $response = $this->post('/api/submit', [
            'quizUUID' => '1',
            'answers' => [
                [
                    'questionUUID' => '1-1',
                    'choices' => ['1-1-1', '1-1-4'],
                ],
                [
                    'questionUUID' => '1-2',
                    'choices' => ['1-2-2','1-2-3'],
                ],
                [
                    'questionUUID' => '1-3',
                    'choices' => ['1-3-4'],
                ],
            ],
        ]);

        $response->assertStatus(201)->assertExactJson(['result' => 0.67]);
    }

    public function testRequestWithExtraCorrectChoices()
    {
        $response = $this->post('/api/submit', [
            'quizUUID' => '1',
            'answers' => [
                [
                    'questionUUID' => '1-1',
                    'choices' => ['1-1-1', '1-1-1', '1-1-1'],
                ],
                [
                    'questionUUID' => '1-2',
                    'choices' => ['1-2-2', '1-2-3', '1-2-2', '1-2-3'],
                ],
                [
                    'questionUUID' => '1-3',
                    'choices' => ['1-3-4', '1-3-4'],
                ],
            ],
        ]);

        $response->assertStatus(201)->assertExactJson(['result' => 1]);
    }

    public function testRequestWithExtraIncorrectChoices()
    {
        $response = $this->post('/api/submit', [
            'quizUUID' => '1',
            'answers' => [
                [
                    'questionUUID' => '1-1',
                    'choices' => ['1-1-1', '1-1-1', '1-1-2'],
                ],
                [
                    'questionUUID' => '1-2',
                    'choices' => ['1-2-2', '1-2-3', '1-2-2', '1-2-5'],
                ],
                [
                    'questionUUID' => '1-3',
                    'choices' => ['1-3-4', '1-3-44'],
                ],
            ],
        ]);

        $response->assertStatus(201)->assertExactJson(['result' => 0]);
    }

    public function testRequestWithExtraIncorrectRandomChoices()
    {
        $response = $this->post('/api/submit', [
            'quizUUID' => '1',
            'answers' => [
                [
                    'questionUUID' => '1-1',
                    'choices' => ['1-1-1', '1-1-1', 'Random'],
                ],
                [
                    'questionUUID' => '1-2',
                    'choices' => ['1-2-2', '1-2-3', 'Random'],
                ],
                [
                    'questionUUID' => '1-3',
                    'choices' => ['1-3-4', 'Random'],
                ],
            ],
        ]);

        $response->assertStatus(201)->assertExactJson(['result' => 0]);
    }

    /**
     * NOTE: All extra correct answers are treated as one correct answer
     *  Example:
     *      [ '1-1-1', '1-1-1' ] is same as [ '1-1-1' ]
     */
    public function testRequestWithExtraCorrectAnswer()
    {
        $response = $this->post('/api/submit', [
            'quizUUID' => '1',
            'answers' => [
                [
                    'questionUUID' => '1-1',
                    'choices' => ['1-1-1'],
                ],
                [
                    'questionUUID' => '1-2',
                    'choices' => ['1-2-2', '1-2-3'],
                ],
                [
                    'questionUUID' => '1-3',
                    'choices' => ['1-3-4'],
                ],
                [
                    'questionUUID' => '1-3',
                    'choices' => ['1-3-4'],
                ],
                [
                    'questionUUID' => '1-3',
                    'choices' => ['1-3-4'],
                ],
            ],
        ]);

        $response->assertStatus(201)->assertExactJson(['result' => 1]);
    }

    /**
     * NOTE: All extra incorrect answers are ignored
     * Example:
     *  When the correct choice is '1-1-1' and the provided answers are
     *  {
     *      questionUUID: '1-3',
     *      choices: '1-3-4'
     *  },
     *  ...
     *  {
     *      questionUUID: '1-3',
     *      choices: '1-3-44',
     *  },
     *
     *  The first answer will be validated and the second one will be ignored.
     */
    public function testRequestWithExtraIncorrectAnswer()
    {
        $response = $this->post('/api/submit', [
            'quizUUID' => '1',
            'answers' => [
                [
                    'questionUUID' => '1-1',
                    'choices' => ['1-1-1'],
                ],
                [
                    'questionUUID' => '1-2',
                    'choices' => ['1-2-2', '1-2-3'],
                ],
                [
                    'questionUUID' => '1-3',
                    'choices' => ['1-3-4'],
                ],
                [
                    'questionUUID' => '1-3',
                    'choices' => ['1-3-44'],
                ],
            ],
        ]);

        $response->assertStatus(201)->assertExactJson(['result' => 1]);
    }

    public function testRequestWithExtraRandomAnswerUUID()
    {
        $response = $this->post('/api/submit', [
            'quizUUID' => '1',
            'answers' => [
                [
                    'questionUUID' => '1-1',
                    'choices' => ['1-1-1'],
                ],
                [
                    'questionUUID' => '1-2',
                    'choices' => ['1-2-2', '1-2-3'],
                ],
                [
                    'questionUUID' => '1-3',
                    'choices' => ['1-3-4'],
                ],
                [
                    'questionUUID' => '1-33333',
                    'choices' => ['1-3-444444'],
                ],
            ],
        ]);

        $response->assertStatus(201)->assertExactJson(['result' => 1]);
    }

    public function testRequestWithOneMissingAnswer()
    {
        $response = $this->post('/api/submit', [
            'quizUUID' => '1',
            'answers' => [
                [
                    'questionUUID' => '1-1',
                    'choices' => ['1-1-1'],
                ],
                [
                    'questionUUID' => '1-2',
                    'choices' => ['1-2-2', '1-2-3'],
                ],
            ],
        ]);

        $response->assertStatus(201)->assertExactJson(['result' => 0.67]);
    }

    public function testRequestWithAllMissingAnswers()
    {
        $response = $this->post('/api/submit', [
            'quizUUID' => '1',
            'answers' => [

            ],
        ]);

        $response->assertStatus(201)->assertExactJson(['result' => 0]);
    }
}
