@php
/** @var Illuminate\Database\Eloquent\Collection<int, App\Models\Filter> $filters */
@endphp

@extends('layouts/app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0"><i class="fa-solid fa-filter"></i> Filters</h1>

            <a href="{{ route('filters.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> New filter</a>
        </div>

        @session('status')
            <div class="alert alert-success">{{ $value }}</div>
        @endsession

        @if($filters->isEmpty())
            <p class="text-muted">No filters yet. Sniff has nothing to search for.</p>
        @else
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Property type</th>
                        <th>Locations</th>
                        <th>Price</th>
                        <th>Area from</th>
                        <th>Active</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($filters as $filter)
                        <tr @class(['text-muted' => ! $filter->is_active])>
                            <td>{{ $filter->name }}</td>
                            <td>{{ $filter->property_type->label() }}</td>
                            <td>{{ $filter->locations->map->label()->implode(', ') }}</td>
                            <td class="text-nowrap">
                                {{ $filter->price_from !== null ? '€ '.number_format($filter->price_from, 0, ',', ' ') : '–' }}
                                –
                                {{ $filter->price_to !== null ? '€ '.number_format($filter->price_to, 0, ',', ' ') : '–' }}
                            </td>
                            <td>{{ $filter->area_from !== null ? $filter->area_from.' m2' : '–' }}</td>
                            <td>
                                @if($filter->is_active)
                                    <a href="{{ route('filters.toggleActive', $filter) }}" class="btn btn-sm btn-success"><i class="fa-solid fa-toggle-on"></i> Active</a>
                                @else
                                    <a href="{{ route('filters.toggleActive', $filter) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-toggle-off"></i> Inactive</a>
                                @endif
                            </td>
                            <td class="text-nowrap text-end">
                                <form action="{{ route('filters.run', $filter) }}" method="POST" class="d-inline">
                                    @csrf

                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Run now"><i class="fa-solid fa-play"></i></button>
                                </form>

                                <a href="{{ $filter->url() }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Open on nehnutelnosti.sk"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>

                                <a href="{{ route('filters.duplicate', $filter) }}" class="btn btn-sm btn-outline-secondary" title="Duplicate"><i class="fa-regular fa-copy"></i></a>

                                <a href="{{ route('filters.edit', $filter) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a>

                                <form action="{{ route('filters.destroy', $filter) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this filter?')">
                                    @csrf

                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
