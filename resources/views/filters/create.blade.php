@extends('layouts/app')

@section('content')
    <div class="container">
        <h1>New filter</h1>

        <form action="{{ route('filters.store') }}" method="POST">
            @csrf

            @include('filters.form')
        </form>
    </div>
@endsection
