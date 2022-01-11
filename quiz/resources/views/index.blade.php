<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Quizzes</title>

    <link rel="icon" href="{{ asset('favicon.png') }}">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/index.css') }}" rel="stylesheet" />
</head>

<body>
    <h1>Choose your quiz!</h1>

    <section class="basic-grid">
        @foreach($quizzes as $quiz)
            <a href="{{ URL::to('/_/' . $quiz->getUUID()) }}">
                <div class="quiz">
                    <div class="quiz-uuid">UUID: {{$quiz->getUUID() }}</div>
                    <div class="quiz-title">{{$quiz->getTitle() }}</div>
                </div>
            </a>
        @endforeach

        <!-- Button to add a new quiz -->
        <a href="{{ URL::to('/new') }}">
            <div class="quiz">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div style="font-size: 1rem;">Create a quiz</div>
            </div>
        </a>
    </section>
</body>

</html>
