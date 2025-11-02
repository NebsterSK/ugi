@extends('layouts/app')

@section('content')
    <div class="container">
        <h1>Seen <span class="h4 text-muted">({{ $entries->count() }})</span></h1>

        @foreach($entries as $entry)
            <div>
                <p>
                    <a href="{{ route('entries.show', $entry->id) }}">{{ $entry->title }}</a>

                    <br>

                    <span class="text-muted small">{{ $entry->created_at->format('j.n.Y') }} ({{ $entry->created_at->diffForHumans() }})</span>
                </p>
            </div>
        @endforeach
    </div>
@endsection
