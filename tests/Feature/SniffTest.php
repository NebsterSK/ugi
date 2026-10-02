<?php

use App\Console\Commands\Sniff;
use App\Jobs\SniffFilterJob;
use App\Models\Entry;
use App\Models\Filter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filter::query()->delete();

    Sleep::fake();
});

function resultsPage(): string
{
    return <<<'HTML'
        <html><body>
            <div class="MuiGrid-root MuiGrid-direction-xs-row MuiGrid-grid-xs-12 MuiGrid-grid-md-8">
                <a class="MuiBox-root" href="https://www.nehnutelnosti.sk/detail/JuAbc123/pekny-4-izbovy-byt">Detail</a>
                <h2 class="MuiTypography-root MuiTypography-h4">Pekný 4-izbový byt</h2>
                <div class="MuiStack-root">
                    <p class="MuiTypography-root MuiTypography-body2 MuiTypography-noWrap">Trnavská cesta, Bratislava-Ružinov, okres Bratislava II</p>
                    <p class="MuiTypography-root MuiTypography-body2 MuiTypography-noWrap">4 izby</p>
                    <p class="MuiTypography-root MuiTypography-body2">82 m²</p>
                </div>
                <a class="MuiStack-root">
                    <p class="MuiTypography-root MuiTypography-h5">265&nbsp;000 €</p>
                    <p class="MuiTypography-root MuiTypography-label1">3&nbsp;232 € / m²</p>
                </a>
            </div>
        </body></html>
        HTML;
}

it('sniffs every active filter', function () {
    Http::fake(['*' => Http::response('<html><body></body></html>')]);

    $first = Filter::factory()->create();
    $second = Filter::factory()->create();
    $inactive = Filter::factory()->inactive()->create();

    $this->artisan(Sniff::class)->assertSuccessful();

    Http::assertSentCount(2);
    Http::assertSent(fn (Request $request): bool => $request->url() === $first->url());
    Http::assertSent(fn (Request $request): bool => $request->url() === $second->url());
    Http::assertNotSent(fn (Request $request): bool => $request->url() === $inactive->url());
});

it('does nothing without active filters', function () {
    Http::fake();

    Filter::factory()->inactive()->create();

    $this->artisan(Sniff::class)
        ->expectsOutput('No active filters, nothing to sniff.')
        ->assertSuccessful();

    Http::assertNothingSent();
});

it('saves entries from every results page until an empty one', function () {
    Http::fake(['*' => Http::sequence()->push(resultsPage())->push('<html><body></body></html>')]);

    $filter = Filter::factory()->create();

    $this->artisan(Sniff::class)->assertSuccessful();

    Http::assertSent(fn (Request $request): bool => $request->url() === $filter->url(2));

    expect(Entry::sole())
        ->internal_id->toBe('JuAbc123')
        ->title->toBe('Pekný 4-izbový byt')
        ->street->toBe('Trnavská cesta')
        ->district->toBe('Ružinov')
        ->rooms->toBe(4)
        ->area->toBe(82)
        ->price->toBe(265000)
        ->price_per_sqm->toBe(3232);

    Sleep::assertSleptTimes(1);
    Sleep::assertSlept(fn ($duration): bool => $duration->totalSeconds >= 1 && $duration->totalSeconds <= 3);
});

it('sniffs a single filter from the job, even an inactive one', function () {
    Http::fake(['*' => Http::response('<html><body></body></html>')]);

    $filter = Filter::factory()->inactive()->create();
    Filter::factory()->create();

    SniffFilterJob::dispatchSync($filter);

    Http::assertSentCount(1);
    Http::assertSent(fn (Request $request): bool => $request->url() === $filter->url());
});
