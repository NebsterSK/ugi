@extends('layouts/app')

@section('content')
    <div class="container">
        <h1>Favorite</h1>

        @foreach($entries as $entry)
            <p>
                <a href="{{ route('entries.show', $entry->id) }}">{{ $entry->title }}</a>
            </p>
        @endforeach
    </div>
@endsection
