@php
/** @var App\Models\Filter $filter */
@endphp

@extends('layouts/app')

@section('content')
    <div class="container">
        <h1>{{ $filter->name }}</h1>

        <form action="{{ route('filters.update', $filter) }}" method="POST">
            @csrf

            @method('PUT')

            @include('filters.form')
        </form>
    </div>
@endsection
