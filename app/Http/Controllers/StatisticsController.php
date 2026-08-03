<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    protected const PRICE_BUCKET = 20000;

    protected const PRICE_PER_SQM_BUCKET = 200;

    public function index(): View
    {
        $entries = Entry::query()
            ->orderBy('created_at')
            ->get(['created_at', 'seen_at', 'favorited_at', 'is_ignored', 'district', 'rooms', 'area', 'price', 'price_per_sqm']);

        $entriesPerMonth = $this->entriesPerMonth($entries);
        $totals = $entriesPerMonth->pluck('total');

        return view('statistics.index')->with([
            'entriesPerMonth' => $entriesPerMonth,
            'total' => $totals->sum(),
            'average' => $totals->isNotEmpty() ? (int) round($totals->avg()) : 0,
            'best' => $totals->isNotEmpty() ? $totals->max() : 0,
            'currentMonth' => $entriesPerMonth->get(now()->format('Y-m'))['total'] ?? 0,
            'priceHistogram' => $this->histogram($entries->pluck('price'), self::PRICE_BUCKET),
            'pricePerSqmHistogram' => $this->histogram($entries->pluck('price_per_sqm'), self::PRICE_PER_SQM_BUCKET),
            'medianPricePerSqmPerMonth' => $this->medianPricePerSqmPerMonth($entries, $entriesPerMonth->keys()),
            'districts' => $this->districts($entries),
        ]);
    }

    /**
     * Number of added apartments per state keyed by month ("Y-m"), including months without any entries.
     *
     * @param  EloquentCollection<int, Entry>  $entries
     * @return Collection<string, array{ignored: int, seen: int, favorite: int, new: int, total: int}>
     */
    protected function entriesPerMonth(EloquentCollection $entries): Collection
    {
        $counts = $this->groupByMonth($entries)
            ->map(fn (Collection $entries): array => [
                'ignored' => $entries->filter(fn (Entry $entry): bool => $entry->is_ignored)->count(),
                'seen' => $entries->filter(fn (Entry $entry): bool => ! $entry->is_ignored && $entry->seen_at && ! $entry->favorited_at)->count(),
                'favorite' => $entries->filter(fn (Entry $entry): bool => ! $entry->is_ignored && $entry->favorited_at)->count(),
                'new' => $entries->filter(fn (Entry $entry): bool => ! $entry->is_ignored && ! $entry->seen_at && ! $entry->favorited_at)->count(),
                'total' => $entries->count(),
            ]);

        if ($counts->isEmpty()) {
            return $counts;
        }

        return $this->allMonths($counts->keys())->mapWithKeys(fn (string $month): array => [
            $month => $counts->get($month, [
                'ignored' => 0,
                'seen' => 0,
                'favorite' => 0,
                'new' => 0,
                'total' => 0,
            ]),
        ]);
    }

    /**
     * Median price per m2 for every month, null for months without any entries.
     *
     * @param  EloquentCollection<int, Entry>  $entries
     * @param  Collection<int, string>  $months
     * @return Collection<string, int|null>
     */
    protected function medianPricePerSqmPerMonth(EloquentCollection $entries, Collection $months): Collection
    {
        $medians = $this->groupByMonth($entries)
            ->map(fn (Collection $entries): int => (int) round($entries->median('price_per_sqm')));

        return $months->mapWithKeys(fn (string $month): array => [$month => $medians->get($month)]);
    }

    /**
     * Apartment counts and medians per district, biggest district first.
     *
     * @param  EloquentCollection<int, Entry>  $entries
     * @return Collection<int, array{district: string, count: int, price: int, price_per_sqm: int, area: int}>
     */
    protected function districts(EloquentCollection $entries): Collection
    {
        return $entries->groupBy('district')
            ->map(fn (Collection $entries, string $district): array => [
                'district' => $district,
                'count' => $entries->count(),
                'price' => (int) round($entries->median('price')),
                'price_per_sqm' => (int) round($entries->median('price_per_sqm')),
                'area' => (int) round($entries->median('area')),
            ])
            ->sortByDesc('count')
            ->values();
    }

    /**
     * Distribution of the given values in buckets of the given size, from the lowest to the highest value.
     *
     * @param  Collection<int, int>  $values
     * @return Collection<int, array{from: int, to: int, count: int}>
     */
    protected function histogram(Collection $values, int $bucketSize): Collection
    {
        if ($values->isEmpty()) {
            return collect();
        }

        $counts = $values->countBy(fn (int $value): int => intdiv($value, $bucketSize) * $bucketSize);

        $buckets = collect();

        for ($from = $counts->keys()->min(); $from <= $counts->keys()->max(); $from += $bucketSize) {
            $buckets->push([
                'from' => $from,
                'to' => $from + $bucketSize,
                'count' => $counts->get($from, 0),
            ]);
        }

        return $buckets;
    }

    /**
     * @param  EloquentCollection<int, Entry>  $entries
     * @return Collection<string, Collection<int, Entry>>
     */
    protected function groupByMonth(EloquentCollection $entries): Collection
    {
        return $entries->groupBy(fn (Entry $entry): string => $entry->created_at->format('Y-m'));
    }

    /**
     * Every month between the first and the last given one, gaps included.
     *
     * @param  Collection<int, string>  $months
     * @return Collection<int, string>
     */
    protected function allMonths(Collection $months): Collection
    {
        $month = Carbon::createFromFormat('Y-m', (string) $months->first())->startOfMonth();
        $lastMonth = Carbon::createFromFormat('Y-m', (string) $months->last())->startOfMonth();

        $allMonths = collect();

        while ($month->lessThanOrEqualTo($lastMonth)) {
            $allMonths->push($month->format('Y-m'));

            $month->addMonth();
        }

        return $allMonths;
    }
}
