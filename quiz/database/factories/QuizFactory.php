<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Utils\Serializer;

use Illuminate\Database\Eloquent\Factories\Factory;

// TODO: Use this factory during the tests

class QuizFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Quiz::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $sampleQuizzes = Serializer::SampleQuizzes();

        return [
            Serializer::quizToArray($sampleQuizzes[0]),          // First quiz
            Serializer::quizToArray(end($sampleQuizzes)), // Last quiz
        ];
    }
}
