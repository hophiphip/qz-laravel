<?php

namespace Database\Factories;

use App\DTO\ChoiceDTO;
use App\DTO\QuizDTO;
use App\Models\Quiz;
use App\Utils\Serializer;

use Illuminate\Database\Eloquent\Factories\Factory;


class QuizFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Quiz::class;

    /**
     * Initial quiz
     *
     * @return QuizDTO
     */
    public static function initial(): QuizDTO {
        return Serializer::CreateQuiz(
            '1',
            'Initial',
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

    /**
     * Define the model's default state.
     *
     * @return QuizDTO
     */
    public function definition(): QuizDTO
    {
        $uuid = $this->faker->uuid();

        return Serializer::CreateQuiz(
            $uuid,
            $this->faker->title(),
            ...[
                // Question 1
                Serializer::CreateQuestion($uuid . '-1', $this->faker->text(), ...[
                    new ChoiceDTO($uuid . '-1-1', $this->faker->text(), $this->faker->boolean()),
                    new ChoiceDTO($uuid . '-1-2', $this->faker->text(), $this->faker->boolean()),
                    new ChoiceDTO($uuid . '-1-3', $this->faker->text(), $this->faker->boolean()),
                    new ChoiceDTO($uuid . '-1-4', $this->faker->text(), $this->faker->boolean()),
                ]),

                // Question 2
                Serializer::CreateQuestion($uuid . '-2', $this->faker->text(), ...[
                    new ChoiceDTO($uuid . '-2-1', $this->faker->text(), $this->faker->boolean()),
                    new ChoiceDTO($uuid . '-2-2', $this->faker->text(), $this->faker->boolean()),
                    new ChoiceDTO($uuid . '-2-3', $this->faker->text(), $this->faker->boolean()),
                    new ChoiceDTO($uuid . '-2-4', $this->faker->text(), $this->faker->boolean()),
                ]),

                // Question 3
                Serializer::CreateQuestion($uuid . '-3', $this->faker->text(), ...[
                    new ChoiceDTO($uuid . '-3-1', $this->faker->text(), $this->faker->boolean()),
                    new ChoiceDTO($uuid . '-3-2', $this->faker->text(), $this->faker->boolean()),
                    new ChoiceDTO($uuid . '-3-3', $this->faker->text(), $this->faker->boolean()),
                    new ChoiceDTO($uuid . '-3-4', $this->faker->text(), $this->faker->boolean()),
                ]),
            ]
        );
    }
}
