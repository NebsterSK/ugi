@php
/** @var Illuminate\Support\Collection<int, App\Models\SniffRun> $sniffRuns */
@endphp

@extends('layouts.app')

@section('content')
    <div class="container">
        @auth
            <h1 class="h3"><i class="fa-solid fa-dog"></i> Sniff runs</h1>

            @if($sniffRuns->isEmpty())
                <p class="text-muted">No sniff runs yet.</p>
            @else
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Started</th>
                                <th>Status</th>
                                <th class="text-end" title="New entries">New</th>
                                <th class="text-end" title="Listings created or updated">Saved</th>
                                <th class="text-end d-none d-sm-table-cell" title="Listings skipped as duplicates">Skipped</th>
                                <th class="text-end" title="Listings that could not be parsed or saved">Failed</th>
                                <th class="text-end d-none d-md-table-cell">Filters</th>
                                <th class="text-end d-none d-md-table-cell">Duration</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($sniffRuns as $sniffRun)
                                <tr>
                                    <td class="text-nowrap">{{ $sniffRun->started_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if($sniffRun->isInterrupted())
                                            <span class="badge text-bg-secondary">Interrupted</span>
                                        @else
                                            <span class="badge text-bg-{{ $sniffRun->status->color() }}">{{ $sniffRun->status->label() }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ $sniffRun->new_entries_count }}</td>
                                    <td class="text-end">{{ $sniffRun->saved_count }}</td>
                                    <td class="text-end d-none d-sm-table-cell">{{ $sniffRun->skipped_count }}</td>
                                    <td @class(['text-end', 'text-danger fw-bold' => $sniffRun->failed_count > 0])>{{ $sniffRun->failed_count }}</td>
                                    <td class="text-end d-none d-md-table-cell">{{ $sniffRun->filters_count }}</td>
                                    <td class="text-end text-nowrap d-none d-md-table-cell">
                                        {{ $sniffRun->finished_at ? $sniffRun->started_at->diffAsCarbonInterval($sniffRun->finished_at)->forHumans(['short' => true]) : '–' }}
                                    </td>
                                </tr>

                                @if($sniffRun->error)
                                    <tr>
                                        <td colspan="8" class="small text-break text-{{ $sniffRun->status->color() }}-emphasis bg-{{ $sniffRun->status->color() }}-subtle">{{ $sniffRun->error }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endauth

        <img src="{{ Vite::asset('resources/images/ugi2.png') }}" class="img-fluid d-block mx-auto" alt="Ugi" />
    </div>
@endsection
