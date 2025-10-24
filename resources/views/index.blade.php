<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>

{{--        @vite(['resources/css/app.css'])--}}
    </head>

    <body>
    <h2>Favorite</h2>

    @foreach($entries->where('is_favorite', true)->where('is_seen', true) as $entry)
        <p>{{ $entry->title }}</p>
    @endforeach

    <hr>

    <h2>New</h2>

    @foreach($entries->where('is_seen', false)->where('is_favorite', false) as $entry)
        <p>{{ $entry->title }}</p>
    @endforeach

    <hr>

    <h2>Seen</h2>

    @foreach($entries->where('is_seen', true)->where('is_favorite', false) as $entry)
        <p>{{ $entry->title }}</p>
    @endforeach

{{--        @vite(['resources/js/app.js'])--}}
    </body>
</html>
