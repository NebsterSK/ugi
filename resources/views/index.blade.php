@extends('_layout')

@section('content')
    <div class="container">
        <h2>Favorite</h2>

        @foreach($entries->where('is_favorite', true)->where('is_seen', true) as $entry)
            <p>
                <a href="{{ route('show', $entry->id) }}">{{ $entry->title }}</a>
            </p>
        @endforeach

        <hr>

        <h2>New</h2>

        @foreach($entries->where('is_seen', false)->where('is_favorite', false) as $entry)
            <p>
                <a href="{{ route('show', $entry->id) }}">{{ $entry->title }}</a>
            </p>
        @endforeach

        <hr>

        <h2>Seen</h2>

        @foreach($entries->where('is_seen', true)->where('is_favorite', false) as $entry)
            <p>
                <a href="{{ route('show', $entry->id) }}">{{ $entry->title }}</a>
            </p>
        @endforeach
    </div>
@endsection
