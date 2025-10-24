@extends('_layout')

@section('content')
    <div class="container">
        <h1>{{ $entry->title }}</h1>

        <a href="{{ route('favorite', $entry->id) }}">Favorite</a>

        <hr>

        <a href="{{ $entry->url }}" target="_blank">{{ $entry->url }}</a>
    </div>
@endsection
