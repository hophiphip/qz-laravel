<?php

use App\Models\Quiz;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Index page, that lists all quizzes.
 *
 */
Route::get('/', function () {
    return view('index', [
        'quizzes' => Quiz::allAsDTO(),
    ]);
});


/**
 * Quiz page. Shows quiz questions and allows to submit and validate a quiz.
 *
 */
Route::get('/_/{uuid}', function ($uuid) {
    $quiz = Quiz::findAsDTO(strval($uuid));

    if ($quiz) {
        return view('quiz', [
            'quiz' => $quiz,
        ]);
    } else {
        return view('404');
    }
});


/**
 * New quiz creation page.
 */
Route::get('/new', function () {
    return view('new');
});
