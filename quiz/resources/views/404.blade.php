<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<!-- TODO: Use better method to center message contents and mb. use `gohome.blade.php` to go to the home page? -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Quiz Not Found</title>

    <link rel="icon" href="{{ asset('favicon.png') }}">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />

    <style>
        html, body {
            height: 100%;
            margin: 0;

            background: #353535;
        }

        .contents {
            height: 90%;
            width: 100%;

            text-align: center;

            display: table;
        }

        .contents .message {
            color: #fff;

            display: table-cell;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="contents">
        <div class="message">
            <h1>404 | Quiz not found</h1>

            <a href="/">
                <h4 style="color:#fff;">Go to the home page</h4>
            </a>
        </div>
    </div>
</body>

</html>
