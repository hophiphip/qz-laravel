<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Utils\Serializer;
use Database\Factories\QuizFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Quiz::create(
            Serializer::quizToArray(QuizFactory::initial())
        );
    }
}
