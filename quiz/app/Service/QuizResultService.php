<?php

namespace App\Service;

use App\DTO\QuizDTO;
use App\DTO\QuestionDTO;
use App\DTO\ChoiceDTO;
use App\DTO\AnswersDTO;
use App\DTO\AnswerDTO;

use Exception;

class QuizResultService
{
    private QuizDTO $quiz;
    private AnswersDTO $answers;

    public function __construct(QuizDTO $quiz, AnswersDTO $answers)
    {
        $this->quiz = $quiz;
        $this->answers = $answers;
    }

    public function getResult(): float
    {
        /**
         * Check whether UUID's are different
         *  If UUID's are indeed different, then quiz is not solved, return 0
         */
        if (strcmp($this->quiz->getUUID(), $this->answers->getQuizUUID()) !== 0) {
            return 0;
        }

        /**
         *    Array of correct questions for the quiz
         *        `Key` is a question UUID
         *        `Value` is an array of correct choices for this question
         *
         *    Example:
         *      (
         *          '1-1' => [ '1-1-1' ],
         *          '1-2' => [ '1-2-2' ],
         *      );
         */
        $correctChoices = [];

        /**
         * Set correct choices for each question of the quiz
         */
        foreach ($this->quiz->getQuestions() as $question) {
            $questionUUID = $question->getUUID();

            /**
             * Set correct choices for this question
             */
            foreach ($question->getChoices() as $choice) {
                if ($choice->isCorrect()) {
                    $correctChoices[$questionUUID][] = $choice->getUUID();
                }
            }
        }

        /**
         * Calculate correct questions number
         */
        $totalQuestionsCount = count($correctChoices);

        /**
         * Map of correct questions passed in an answer
         *  Is used to handle duplicate answers for questions
         *
         * Example:
         *   (
         *      ['1-1'] = true,
         *   );
         */
        $correctQuestionsMap = [];

        /**
         * NOTE: Seems complicated, but is actually faster than `array_intersect` or `array_diff` approach.
         *
         * Compare an answer choices array to the question choices array
         */
        foreach ($this->answers->getAnswers() as $answer) {
            $questionUUID = $answer->getQuestionUUID();

            /**
             * If random question UUID was provided - ignore it
             */
            if (!isset($correctChoices[$questionUUID])) {
                continue;
            }

            /**
             * Explanation with example
             *
             *  An array before `array_flip`:
             *      (
             *          [1] => '1-1-1',
             *      );
             *
             *  An array after `array_flip`:
             *      (
             *          ['1-1-1'] => 1,
             *      );
             */
            $correctChoicesMap = array_flip(array_unique($correctChoices[$questionUUID]));
            $correctChoicesCount = 0;
            $incorrectChoicesCount = 0;

            /**
             * NOTE: This is basically a diff of two arrays.
             *
             * Iterate over the provided answer choices.
             *      - If choice UUID is set in choices map then +1 correct choices
             *      - If choice UUID is not set in choices map then +1 incorrect/extra choices
             */
            foreach (array_unique($answer->getСhoices()) as $choiceUUID) {
                if (isset($correctChoicesMap[$choiceUUID])) {
                    // Correct choice was provided
                    $correctChoicesCount += 1;
                } else {
                    // An extra incorrect choice was provided
                    $incorrectChoicesCount += 1;
                }
            }

            /**
             * If no extra choices were provided and correct choices count is equal to the count of a quiz correct choices then the question is correct
             */
            if ($incorrectChoicesCount == 0 && $correctChoicesCount == count($correctChoicesMap)) {
                $correctQuestionsMap[$questionUUID] = true;
            }
        }

        return round(count($correctQuestionsMap) / (float) $totalQuestionsCount, 2);
    }
}
