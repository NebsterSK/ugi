@php
/** @var App\Models\Entry $entry */
@endphp

@extends('layouts/app')

@section('content')
    <div class="container">
        <h1>{{ $entry->title }}</h1>

        <a href="{{ $entry->url }}" target="_blank">{{ $entry->url }}</a>

        <hr>

        @if($entry->is_favorited)
            <a href="{{ route('entries.toggleFavorite', $entry->id) }}" class="btn btn-success"><i class="fa-solid fa-star"></i></a>
        @else
            <a href="{{ route('entries.toggleFavorite', $entry->id) }}" class="btn btn-outline-secondary"><i class="fa-regular fa-star"></i></a>
        @endif

        <hr>

        <p>Rooms: {{ $entry->rooms }}</p>

        <p>
            Street: {{ $entry->street }}<br>

            District: {{ $entry->district }}
        </p>

        <p>
            Area: {{ $entry->area }} m2<br>

            Price: € {{ number_format($entry->price, 0, ',', ' ') }}<br>

            Price per m2: € {{ number_format($entry->price_per_sqm, 0, ',', ' ') }} / m2
        </p>

        <hr>

        <form action="" method="POST">
            @csrf

            @method('PUT')

            <label for="comment">Comment</label>
            <textarea id="comment" class="form-control mb-2" name="comment" rows="4">{{ $entry->comment }}</textarea>

            <button type="submit" class="btn btn-primary">Save</button>
        </form>

        <hr>

        <a href="{{ route('entries.toggleIgnore', $entry->id) }}" class="btn btn-outline-danger"><i class="fa-regular fa-trash-can"></i></a>
    </div>
@endsection
