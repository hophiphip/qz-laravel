<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Service\QuizResultService;
use App\Utils\Serializer;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class QuizController extends Controller
{
    /**
     * Submit quiz answers
     *
     * @param Request $request contains `quizUUID` and quiz `answers`
     * @return Response|Application|ResponseFactory
     */
    public function submit(Request $request): Response|Application|ResponseFactory
    {
        $request->validate([
            'quizUUID' => 'required|string', // uuid field is required
            'answers' => 'present|array',    // answers field is required, but can be empty
        ]);

        // Get request parameters
        $quizUUID = strval($request->input('quizUUID'));
        $quizAnswers = $request->input('answers');

        $quiz = Quiz::findAsDTO($quizUUID);

        // Quiz doesn't exist
        if ($quiz == null) {
            return response(json_encode([
                'error' => 'quiz does not exist',
            ]), 404)->header('Content-Type', 'application/json');
        }

        // Construct answers
        $answers = Serializer::mixedToAnswers($quizUUID, $quizAnswers);

        // Could not parse quiz answers
        if ($answers == null) {
            return response(json_encode([
                'error' => 'incorrect request parameters'
            ]), 422)->header('Content-Type', 'application/json');
        }

        // Validate the quiz
        $quizResultService = new QuizResultService(
            $quiz,
            $answers
        );

        $result = $quizResultService->getResult();

        return response(json_encode([
            'result' => $result
        ]), 201)->header('Content-Type', 'application/json');
    }

    /**
     * Create a new quiz
     *
     * @param Request $request contains a new quiz questions and choices
     * @return Response|Application|ResponseFactory
     */
    public function store(Request $request): Response|Application|ResponseFactory
    {
        $request->validate([
            'title' => 'required|string',
            'questions' => 'required|array',
        ]);

        // Parse request params
        $quiz = Serializer::quizFromRequestParams($request->input('title'), $request->input('questions'));

        if ($quiz == null) {
            return response(json_encode([
                'error' => 'incorrect quiz parameters'
            ]), 422)->header('Content-Type', 'application/json');
        }

        $newQuiz = Quiz::create($quiz);

        return response(json_encode([
            'uuid' => $newQuiz->id,
        ]), 201)->header('Content-Type', 'application/json');
    }
}
