@php
    /** @var Illuminate\Support\Collection<string, array{ignored: int, seen: int, favorite: int, new: int, total: int}> $entriesPerMonth */
    /** @var Illuminate\Support\Collection<int, array{from: int, to: int, count: int}> $priceHistogram */
    /** @var Illuminate\Support\Collection<int, array{from: int, to: int, count: int}> $pricePerSqmHistogram */
    /** @var Illuminate\Support\Collection<string, int|null> $medianPricePerSqmPerMonth */
    /** @var Illuminate\Support\Collection<int, array{district: string, count: int, price: int, price_per_sqm: int, area: int}> $districts */

    $states = [
        'new' => ['label' => 'New', 'color' => 'var(--bs-primary)'],
        'favorite' => ['label' => 'Favorite', 'color' => 'var(--bs-warning)'],
        'seen' => ['label' => 'Seen', 'color' => 'var(--bs-info)'],
        'ignored' => ['label' => 'Ignored', 'color' => 'var(--bs-secondary)'],
    ];

    $number = fn (?int $value): string => number_format((int) $value, 0, ',', ' ');
@endphp

@extends('layouts/app')

@section('content')
    <style>
        .chart,
        .bar-chart {
            display: flex;
            align-items: flex-end;
            gap: .5rem;
            height: 320px;
            overflow-x: auto;
            padding-top: 1.5rem;
        }

        .chart-column,
        .bar-chart-column {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
            flex: 1 0 48px;
            height: 100%;
        }

        .chart-stack,
        .bar-chart-bar {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            width: 100%;
            border-radius: .25rem .25rem 0 0;
            overflow: hidden;
        }

        .chart-stack-empty,
        .bar-chart-bar-empty {
            min-height: 2px;
            background-color: var(--bs-secondary-bg);
        }

        .chart-segment {
            width: 100%;
        }
    </style>

    <div class="container-fluid">
        <h1><i class="fa-solid fa-chart-column"></i> Statistics</h1>

        <div class="row row-cols-2 row-cols-md-4 g-3 my-2">
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Total apartments</div>
                        <div class="fs-3 fw-bold">{{ $number($total) }}</div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small">This month</div>
                        <div class="fs-3 fw-bold">{{ $number($currentMonth) }}</div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Average per month</div>
                        <div class="fs-3 fw-bold">{{ $number($average) }}</div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Best month</div>
                        <div class="fs-3 fw-bold">{{ $number($best) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span>Apartments added per month</span>

                <span class="d-flex flex-wrap gap-3 small text-muted">
                    @foreach($states as $state)
                        <span class="d-inline-flex align-items-center gap-1">
                            <span style="display: inline-block; width: .75rem; height: .75rem; border-radius: .15rem; background-color: {{ $state['color'] }};"></span>
                            {{ $state['label'] }}
                        </span>
                    @endforeach
                </span>
            </div>

            <div class="card-body">
                @if($entriesPerMonth->isEmpty())
                    <p class="text-muted mb-0">No apartments yet.</p>
                @else
                    <div class="chart mb-5">
                        @foreach($entriesPerMonth as $month => $counts)
                            <div class="chart-column">
                                <div class="small fw-semibold">{{ $counts['total'] }}</div>

                                <div class="chart-stack @if($counts['total'] === 0) chart-stack-empty @endif"
                                     style="height: {{ $best > 0 ? round($counts['total'] / $best * 100) : 0 }}%"
                                     title="{{ $month }}: {{ $counts['total'] }} ({{ collect($states)->map(fn ($state, $key) => "{$state['label']}: {$counts[$key]}")->implode(', ') }})">
                                    @foreach($states as $key => $state)
                                        @if($counts[$key] > 0)
                                            <div class="chart-segment"
                                                 style="flex: {{ $counts[$key] }} 0 0; background-color: {{ $state['color'] }};"></div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex gap-2" style="overflow-x: auto;">
                        @foreach($entriesPerMonth as $month => $counts)
                            <div class="text-center small text-muted" style="flex: 1 0 48px;">{{ $month }}</div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if($entriesPerMonth->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header">Median price per m² per month</div>

                <div class="card-body">
                    <x-bar-chart color="var(--bs-success)"
                                 :bars="$medianPricePerSqmPerMonth->map(fn (?int $median, string $month) => [
                                     'label' => $month,
                                     'value' => $median,
                                     'display' => $median ? $number($median) : '–',
                                     'title' => $median ? "{$month}: € {$number($median)} / m²" : "{$month}: no apartments",
                                 ])->values()->all()" />

                    <p class="text-muted small mt-3 mb-0">
                        Median asking price per m² of the apartments added in that month. Not a sold-price index — it moves with what happens to be listed.
                    </p>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-12 col-xl-6">
                    <div class="card h-100">
                        <div class="card-header">Price distribution</div>

                        <div class="card-body">
                            <x-bar-chart :bars="$priceHistogram->map(fn (array $bucket) => [
                                'label' => round($bucket['from'] / 1000).'k',
                                'value' => $bucket['count'],
                                'title' => '€ '.$number($bucket['from']).' – '.$number($bucket['to']).': '.$bucket['count'].' apartments',
                            ])->all()" />

                            <p class="text-muted small mt-3 mb-0">Buckets of € 20 000.</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="card h-100">
                        <div class="card-header">Price per m² distribution</div>

                        <div class="card-body">
                            <x-bar-chart color="var(--bs-success)"
                                         :bars="$pricePerSqmHistogram->map(fn (array $bucket) => [
                                             'label' => $number($bucket['from']),
                                             'value' => $bucket['count'],
                                             'title' => '€ '.$number($bucket['from']).' – '.$number($bucket['to']).' / m²: '.$bucket['count'].' apartments',
                                         ])->all()" />

                            <p class="text-muted small mt-3 mb-0">Buckets of € 200 / m².</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Districts</div>

                <div class="card-body">
                    <x-bar-chart height="200px"
                                 color="var(--bs-info)"
                                 :bars="$districts->map(fn (array $district) => [
                                     'label' => $district['district'],
                                     'value' => $district['count'],
                                     'title' => $district['district'].': '.$district['count'].' apartments, median € '.$number($district['price_per_sqm']).' / m²',
                                 ])->all()" />

                    <table class="table table-bordered table-striped table-hover w-auto mt-4 mb-0">
                        <thead>
                            <tr>
                                <th>District</th>
                                <th class="text-end">Apartments</th>
                                <th class="text-end">Median price</th>
                                <th class="text-end">Median price per m²</th>
                                <th class="text-end">Median area</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($districts as $district)
                                <tr>
                                    <td>{{ $district['district'] }}</td>
                                    <td class="text-end">{{ $district['count'] }}</td>
                                    <td class="text-end">€ {{ $number($district['price']) }}</td>
                                    <td class="text-end">€ {{ $number($district['price_per_sqm']) }}</td>
                                    <td class="text-end">{{ $district['area'] }} m²</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Per month</div>

                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover w-auto mb-0">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th class="text-end">New</th>
                                <th class="text-end">Favorite</th>
                                <th class="text-end">Seen</th>
                                <th class="text-end">Ignored</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Median price per m²</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($entriesPerMonth->reverse() as $month => $counts)
                                <tr>
                                    <td>{{ $month }}</td>
                                    <td class="text-end">{{ $counts['new'] }}</td>
                                    <td class="text-end">{{ $counts['favorite'] }}</td>
                                    <td class="text-end">{{ $counts['seen'] }}</td>
                                    <td class="text-end">{{ $counts['ignored'] }}</td>
                                    <td class="text-end fw-semibold">{{ $number($counts['total']) }}</td>
                                    <td class="text-end">
                                        @if($medianPricePerSqmPerMonth[$month])
                                            € {{ $number($medianPricePerSqmPerMonth[$month]) }}
                                        @else
                                            <span class="text-muted">–</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
