<?php

namespace Tests\Unit;

use App\DTO\ChoiceDTO;
use App\DTO\QuestionDTO;
use App\DTO\QuizDTO;
use App\DTO\AnswerDTO;
use App\DTO\AnswersDTO;
use App\Service\QuizResultService;
use PHPUnit\Framework\TestCase;

/**
 *  Test data:
 *      Quiz:
 *      {
 *          uuid: '1',
 *          title: 'Animals',
 *          questions: [
 *              {
 *                  uuid: '1-1',
 *                  text: 'Who is bigger?',
 *                  choices: [
 *                      {
 *                          uuid: '1-1-1',
 *                          text: 'an elephant',
 *                          isCorrect: 'true',
 *                      },
 *                      {
 *                          uuid: '1-1-2'.
 *                          text: 'a mouse',
 *                          isCorrect: 'false',
 *                      }
 *                  ]
 *              },
 *              {
 *                  uuid: '1-2',
 *                  text: 'Who is smaller?',
 *                  choices: [
 *                      {
 *                          uuid: '1-2-1',
 *                          text: 'an elephant',
 *                          isCorrect: 'false',
 *                      },
 *                      {
 *                          uuid: '1-2-2'.
 *                          text: 'a mouse',
 *                          isCorrect: 'true',
 *                      }
 *                  ]
 *              }
 *          ]
 *      }
 *
 *
 *      Answers:
 *      {
 *          quizUUID: '1',
 *          answers: [
 *              {
 *                  questionUUID: '1-1',
 *                  choices: [ '1-1-1' ]
 *              },
 *              {
 *                  questionUUID: '1-2',
 *                  choices: [ '1-2-1' ]
 *              }
 *          ]
 *      }
 */

class BaseQuizTest extends TestCase
{
    protected $quizDTO;
    protected $answersDTO;

    protected function setUp(): void
    {
        $this->quizDTO = $this->makeQuizDTO();
        $this->answersDTO = $this->makeAnswersDTO();
    }

    public function testBasicTest()
    {
        $quiz = $this->makeQuizDTO();

        $quizResultService = new QuizResultService(
            $this->quizDTO,
            $this->answersDTO
        );

        $result = $quizResultService->getResult();

        $this->assertEquals(0.50, $result);
    }

    protected function makeQuizDTO(): QuizDTO
    {
        $choice11 = new ChoiceDTO(
            '1-1-1',
            'an elephant',
            true
        );

        $choice12 = new ChoiceDTO(
            '1-1-2',
            'a mouse',
            false
        );

        $question1 = new QuestionDTO(
            '1-1',
            'Who is bigger?'
        );

        $question1->addChoice($choice11);
        $question1->addChoice($choice12);

        $choice21 = new ChoiceDTO(
            '1-2-1',
            'an elephant',
            false
        );

        $choice22 = new ChoiceDTO(
            '1-2-2',
            'a mouse',
            true
        );

        $question2 = new QuestionDTO(
            '1-2',
            'Who is smaller?'
        );

        $question2->addChoice($choice21);
        $question2->addChoice($choice22);


        $quiz = new QuizDTO(
            '1',
            'Animals'
        );

        $quiz->addQuestion($question1);
        $quiz->addQuestion($question2);

        return $quiz;
    }

    protected function makeAnswersDTO(): AnswersDTO
    {
        $answers = new AnswersDTO(
            $this->quizDTO->getUUID()
        );

        $questions = $this->quizDTO->getQuestions();

        //correct answer
        $answer1 = new AnswerDTO($questions[0]->getUUID());
        $choices1 = $questions[0]->getChoices();
        $answer1->addChoiceUUID($choices1[0]->getUUID());
        $answers->addAnswer($answer1);

        //wrong answer
        $answer2 = new AnswerDTO($questions[1]->getUUID());
        $choices2 = $questions[1]->getChoices();
        $answer2->addChoiceUUID($choices2[0]->getUUID());
        $answers->addAnswer($answer2);

        return $answers;
    }
}
