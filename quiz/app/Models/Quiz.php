<?php

namespace App\Models;

use App\DTO\QuizDTO;
use App\Utils\Serializer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

// TODO: Add try..catch fallback in case of database connection errors -> API must return at least one sample quiz

class Quiz extends Model
{
    use HasFactory;

    /**
     * @var string $collection contains collection name
     */
    protected $collection = 'quiz_collection';

    /**
     * @var string $primaryKey collection primary key
     *
     *  NOTE: MongoDB documents are automatically stored with a unique ID that is stored in the _id property.
     *      If you wish to use your own ID, substitute the $primaryKey property and set it to your own primary key attribute name.
     */
    protected $primaryKey = 'uuid';

    /**
     *  @var array $fillable contains collection fields names
     */
    protected $fillable = [
        'uuid',
        'title',
        'questions',
    ];

    /**
     * Return all quizzes from DB as DTOs.
     *
     * @param string[] $columns
     * @return array
     */
    public static function allAsDTO(array $columns = ['*']): array {
        $quizzes = Quiz::all($columns);
        $quizzesReturned = [];

        foreach ($quizzes as $quiz) {
            $quizzesReturned[] = Serializer::modelToQuiz($quiz);
        }

        return $quizzesReturned;
    }

    /**
     * Return quiz by UUID as DTO.
     *
     * @param mixed $uuid
     * @param mixed|null $default
     * @return QuizDTO|null
     */
    public static function findAsDTO(mixed $uuid, mixed $default = null): QuizDTO|null {
        $select = Quiz::where('uuid', $uuid)->limit(1);

        if ($select->count() == 0) {
            return null;
        }

        $quiz = $select->first();

        return Serializer::modelToQuiz($quiz);
    }
}
