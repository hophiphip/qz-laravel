<?php

use App\Models\Quiz;
use App\Utils\Serializer;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuizCollection extends Migration
{
    /**
     * The name of the database connection to use.
     *
     * @var string
     */
    protected $connection = 'mongodb';

    /**
     * The name of the collection table
     *
     * @var string
     */
    protected string $tableName = 'quiz_collection';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Populate the database with some initial values

        Quiz::create(
            Serializer::quizToArray(Serializer::SampleQuiz())
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)
            ->table($this->tableName, function (Blueprint $collection)
            {
                $collection->drop();
            });
    }
}
