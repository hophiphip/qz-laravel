<?php

namespace App\Utils;

use App\DTO\AnswerDTO;
use App\DTO\AnswersDTO;
use App\DTO\ChoiceDTO;
use App\DTO\QuestionDTO;
use App\DTO\QuizDTO;
use App\Models\Quiz;
use Illuminate\Support\Facades\Log;

// NOTE: All ugly workarounds go here :)

class Serializer
{
    public static function CreateQuestion(string $uuid, string $text, ChoiceDTO ...$choices): QuestionDTO {
        $question = new QuestionDTO($uuid, $text);

        foreach ($choices as $choice) {
            $question->addChoice($choice);
        }

        return $question;
    }

    public static function CreateQuiz(string $uuid, string $title, QuestionDTO ...$questions): QuizDTO {
        $quiz = new QuizDTO($uuid, $title);

        foreach ($questions as $question) {
            $quiz->addQuestion($question);
        }

        return $quiz;
    }

    public static function CreateAnswer(string $questionUUID, string ...$choicesUUID): AnswerDTO {
        $answerResult = new AnswerDTO($questionUUID);

        foreach ($choicesUUID as $choiceUUID) {
            $answerResult->addChoiceUUID($choiceUUID);
        }

        return $answerResult;
    }

    public static function CreateAnswers(string $quizUUID, AnswerDTO ...$answers): AnswersDTO {
        $answersResult = new AnswersDTO($quizUUID);

        foreach ($answers as $answer) {
            $answersResult->addAnswer($answer);
        }

        return $answersResult;
    }

    /**
     * Convert QuizDTO to array (basically Quiz model).
     *
     * TODO: WTF IS THIS ?!?!1
     * I wish, I could change DTOs and do it the proper way, but here we are...
     *
     * @param QuizDTO $quiz
     * @return array
     */
    public static function quizToArray(QuizDTO $quiz): array {
        $result = [];

        $result['uuid'] = $quiz->getUUID();
        $result['title'] = $quiz->getTitle();
        $result['questions'] = [];

        // Add questions
        foreach ($quiz->getQuestions() as $question) {
            $resultQuestion = [];

            $resultQuestion['uuid'] = $question->getUUID();
            $resultQuestion['text'] = $question->getText();
            $resultQuestion['choices'] = [];

            // Add choices
            foreach ($question->getChoices() as $choice) {
                $resultChoice = [];

                $resultChoice['uuid'] = $choice->getUUID();
                $resultChoice['text'] = $choice->getText();
                $resultChoice['isCorrect'] = $choice->isCorrect();

                $resultQuestion['choices'][] = $resultChoice;
            }

            $result['questions'][] = $resultQuestion;
        }

        return $result;
    }


    /**
     * Convert MongoDB model to QuizDTO.
     *
     * TODO: NOTE this is a workaround for now. The main task is to get a working app, and after that I'll try to fix this mess.
     *
     * @param Quiz|null $quizModel
     * @return QuizDTO|null
     */
    public static function modelToQuiz(?Quiz $quizModel): ?QuizDTO {
        if ($quizModel != null && isset($quizModel['uuid']) && isset($quizModel['title']) && isset($quizModel['questions']) && is_array($quizModel['questions'])) {
            $quizUUID = $quizModel['uuid'];
            $quizTitle = $quizModel['title'];
            $quizQuestions = $quizModel['questions'];

            $resultQuiz = new QuizDTO($quizUUID, $quizTitle);

            // Set quiz questions
            foreach ($quizQuestions as $quizQuestion) {
                if (isset($quizQuestion['uuid']) && isset($quizQuestion['text']) && isset($quizQuestion['choices']) && is_array($quizQuestion['choices'])) {
                    $questionUUID = $quizQuestion['uuid'];
                    $questionText = $quizQuestion['text'];
                    $questionChoices = $quizQuestion['choices'];

                    $resultQuestion = new QuestionDTO($questionUUID, $questionText);

                    // Set questions choices
                    foreach ($questionChoices as $questionChoice) {
                        if (isset($questionChoice['uuid']) && isset($questionChoice['text']) && isset($questionChoice['isCorrect'])) {
                            $choiceUUID = $questionChoice['uuid'];
                            $choiceText = $questionChoice['text'];
                            $choiceCorrectness = $questionChoice['isCorrect'];

                            $resultChoice = new ChoiceDTO($choiceUUID, $choiceText, $choiceCorrectness);

                            $resultQuestion->addChoice($resultChoice);
                        } else {
                            return null;
                        }
                    }

                    $resultQuiz->addQuestion($resultQuestion);
                } else {
                    return null;
                }
            }

            return $resultQuiz;
        } else {
            return null;
        }
    }

    /**
     * Construct answers from request.
     *
     * @param mixed $quizUUID
     * @param mixed $quizAnswers
     * @return AnswersDTO|null
     */
    public static function mixedToAnswers(mixed $quizUUID, mixed $quizAnswers): ?AnswersDTO {
        if (is_string($quizUUID) && is_array($quizAnswers)) {

            $resultAnswers = new AnswersDTO($quizUUID);

            foreach ($quizAnswers as $quizAnswer) {
                if (isset($quizAnswer['questionUUID']) && isset($quizAnswer['choices']) && is_array($quizAnswer['choices'])) {

                    // Construct answer
                    $answer = new AnswerDTO($quizAnswer['questionUUID']);

                    foreach ($quizAnswer['choices'] as $choiceUUID) {
                        $answer->addChoiceUUID($choiceUUID);
                    }

                    // Update answers
                    $resultAnswers->addAnswer($answer);
                }
            }

            return $resultAnswers;
        } else {
            return null;
        }
    }

    /**
     * Construct quiz array from request
     *
     * NOTE: Input is sanitized (to prevent XSS) with `htmlentities`
     *
     */
    public static function quizFromRequestParams(mixed $title, mixed $questions): ?array {
        $resultQuiz = [];
        $newQuizUUID = strval(new \MongoDB\BSON\ObjectId(null));

        $resultQuiz['uuid'] = $newQuizUUID;
        $resultQuiz['title'] = htmlentities(strval($title), ENT_QUOTES, 'UTF-8', false);
        $resultQuiz['questions'] = [];

        // Add questions
        foreach ($questions as $questionIndex => $question) {
            if (isset($question['text']) &&  isset($question['choices']) && is_array($question['choices'])) {
                $resultQuestion = [];

                $resultQuestion['uuid'] = $newQuizUUID . '-' . $questionIndex;
                $resultQuestion['text'] = htmlentities(strval($question['text']), ENT_QUOTES, 'UTF-8', false);
                $resultQuestion['choices'] = [];

                // Add choices
                foreach ($question['choices'] as $choiceIndex => $choice) {
                    if (isset($choice['text']) && isset($choice['isCorrect'])) {
                        $resultChoice = [];

                        $resultChoice['uuid'] = $resultQuestion['uuid'] . '-' . $choiceIndex;
                        $resultChoice['text'] = htmlentities(strval($choice['text']), ENT_QUOTES, 'UTF-8', false);
                        // TODO: boolval might cause some unpredictable stuff
                        $resultChoice['isCorrect'] = boolval(htmlentities(strval($choice['isCorrect']), ENT_QUOTES, 'UTF-8', false));

                        $resultQuestion['choices'][] = $resultChoice;
                    }

                    // Pass choices without set text and isCorrect fields
                }

                // If question has at least 4 choices then add it to the quiz
                if (count($resultQuestion['choices']) > 3) {
                    $resultQuiz['questions'][] = $resultQuestion;
                }
            }

            // Pass questions without a text and with no choices
        }

        // If quiz has at least one question then it is OK
        if (count($resultQuiz['questions']) > 0) {
            return $resultQuiz;
        } else {
            return null;
        }
    }
}
