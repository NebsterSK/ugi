@extends('layouts/app')

@section('content')
    <div class="container-fluid">
        <h1><i class="fa-solid fa-eye"></i> Seen</h1>

        <livewire:SeenEntryTable />
    </div>
@endsection
