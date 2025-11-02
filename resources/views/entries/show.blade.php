@extends('layouts/app')

@section('content')
    <div class="container">
        <h1>{{ $entry->title }}</h1>

        <a href="{{ $entry->url }}" target="_blank">{{ $entry->url }}</a>

        <hr>

        <a href="{{ route('entries.toggleFavorite', $entry->id) }}" class="btn btn-info">Favorite</a>

        <a href="{{ route('entries.toggleIgnore', $entry->id) }}" class="btn btn-secondary">Ignore</a>

        <hr>

        <form action="" method="POST">
            @csrf

            @method('PUT')

            <label for="comment">Comment</label>
            <textarea id="comment" class="form-control mb-2" name="comment" rows="4">{{ $entry->comment }}</textarea>

            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
@endsection
