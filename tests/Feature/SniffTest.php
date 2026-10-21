<?php

use App\Console\Commands\Sniff;
use App\Jobs\SniffFilterJob;
use App\Mail\NewEntriesFound;
use App\Models\Entry;
use App\Models\Filter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Sleep;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filter::query()->delete();

    Sleep::fake();
});

/**
 * @param  array<int, array{internal_id?: string, title?: string, rooms?: int, area?: int}>  $listings
 */
function resultsPage(array $listings = [[]]): string
{
    $html = collect($listings)->map(function (array $listing): string {
        $listing = [
            'internal_id' => 'JuAbc123',
            'title' => 'Pekný 4-izbový byt',
            'rooms' => 4,
            'area' => 82,
            ...$listing,
        ];

        return <<<HTML
            <div class="MuiGrid-root MuiGrid-direction-xs-row MuiGrid-grid-xs-12 MuiGrid-grid-md-8">
                <a class="MuiBox-root" href="https://www.nehnutelnosti.sk/detail/{$listing['internal_id']}/byt">Detail</a>
                <h2 class="MuiTypography-root MuiTypography-h4">{$listing['title']}</h2>
                <div class="MuiStack-root">
                    <p class="MuiTypography-root MuiTypography-body2 MuiTypography-noWrap">Trnavská cesta, Bratislava-Ružinov, okres Bratislava II</p>
                    <p class="MuiTypography-root MuiTypography-body2 MuiTypography-noWrap">{$listing['rooms']} izby</p>
                    <p class="MuiTypography-root MuiTypography-body2">{$listing['area']} m²</p>
                </div>
                <a class="MuiStack-root">
                    <p class="MuiTypography-root MuiTypography-h5">265&nbsp;000 €</p>
                    <p class="MuiTypography-root MuiTypography-label1">3&nbsp;232 € / m²</p>
                </a>
            </div>
            HTML;
    })->implode('');

    return '<html><body>'.$html.'</body></html>';
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

it('emails the entries created during the run', function () {
    Mail::fake();
    config(['mail.notification_recipient' => 'me@example.com']);
    Http::fake(['*' => Http::sequence()->push(resultsPage())->push('<html><body></body></html>')]);

    Filter::factory()->create();
    $this->travel(-1)->days();
    $oldEntry = Entry::factory()->create();
    $this->travelBack();

    $this->artisan(Sniff::class)->assertSuccessful();

    Mail::assertSent(NewEntriesFound::class, fn (NewEntriesFound $mail): bool => $mail->hasTo('me@example.com')
        && $mail->entries->pluck('internal_id')->all() === ['JuAbc123']
        && ! $mail->entries->contains($oldEntry));
});

it('does not email when nothing new was found', function () {
    Mail::fake();
    config(['mail.notification_recipient' => 'me@example.com']);
    Http::fake(['*' => Http::response('<html><body></body></html>')]);

    Filter::factory()->create();

    $this->artisan(Sniff::class)->assertSuccessful();

    Mail::assertNothingSent();
});

it('does not email without a notification address', function () {
    Mail::fake();
    config(['mail.notification_recipient' => null]);
    Http::fake(['*' => Http::sequence()->push(resultsPage())->push('<html><body></body></html>')]);

    Filter::factory()->create();

    $this->artisan(Sniff::class)
        ->expectsOutput('Found 1 new entries, but MAIL_NOTIFICATION_RECIPIENT is not set.')
        ->assertSuccessful();

    Mail::assertNothingSent();
});

it('lists the new entries in the notification email', function () {
    $entry = Entry::factory()->create(['title' => 'Pekný byt', 'district' => 'Rača', 'price' => 265000]);

    $mail = new NewEntriesFound(Entry::all());

    $mail->assertHasSubject('1 new apartment found');
    $mail->assertSeeInHtml('Pekný byt');
    $mail->assertSeeInHtml('Rača');
    $mail->assertSeeInHtml('€ 265 000');
    $mail->assertSeeInHtml(route('entries.show', $entry));
});

it('skips a listing similar to an existing entry', function (array $listing) {
    Http::fake(['*' => Http::sequence()->push(resultsPage([$listing]))->push('<html><body></body></html>')]);

    Filter::factory()->create();
    $existing = Entry::factory()->create(['title' => 'Pekný 4-izbový byt na predaj', 'rooms' => 4, 'area' => 82]);

    $this->artisan(Sniff::class)
        ->expectsOutputToContain('Skipping duplicate of entry #'.$existing->id)
        ->assertSuccessful();

    expect(Entry::count())->toBe(1);
})->with([
    'same title' => [['internal_id' => 'Other1', 'title' => 'Pekný 4-izbový byt na predaj']],
    'nearly the same title' => [['internal_id' => 'Other1', 'title' => 'Pekny 4-izbovy byt  na PREDAJ']],
    'area 2 m2 bigger' => [['internal_id' => 'Other1', 'title' => 'Pekný 4-izbový byt na predaj', 'area' => 84]],
    'area 2 m2 smaller' => [['internal_id' => 'Other1', 'title' => 'Pekný 4-izbový byt na predaj', 'area' => 80]],
]);

it('imports a listing that is not similar enough to any existing entry', function (array $listing) {
    Http::fake(['*' => Http::sequence()->push(resultsPage([$listing]))->push('<html><body></body></html>')]);

    Filter::factory()->create();
    Entry::factory()->create(['title' => 'Pekný 4-izbový byt na predaj', 'rooms' => 4, 'area' => 82]);

    $this->artisan(Sniff::class)->assertSuccessful();

    expect(Entry::where('internal_id', 'Other1')->exists())->toBeTrue();
})->with([
    'different title' => [['internal_id' => 'Other1', 'title' => 'Slnečný byt s balkónom pri parku']],
    'different rooms' => [['internal_id' => 'Other1', 'title' => 'Pekný 4-izbový byt na predaj', 'rooms' => 3]],
    'area 3 m2 different' => [['internal_id' => 'Other1', 'title' => 'Pekný 4-izbový byt na predaj', 'area' => 85]],
]);

it('updates a listing that is already imported instead of treating it as a duplicate', function () {
    Http::fake(['*' => Http::sequence()->push(resultsPage([['internal_id' => 'JuAbc123', 'title' => 'Pekný 4-izbový byt, nová cena']]))->push('<html><body></body></html>')]);

    Filter::factory()->create();
    $existing = Entry::factory()->create(['internal_id' => 'JuAbc123', 'title' => 'Pekný 4-izbový byt', 'rooms' => 4, 'area' => 82]);

    $this->artisan(Sniff::class)->assertSuccessful();

    expect(Entry::count())->toBe(1)
        ->and($existing->refresh()->title)->toBe('Pekný 4-izbový byt, nová cena');
});

it('skips duplicates found in the same run and leaves them out of the email', function () {
    Mail::fake();
    config(['mail.notification_recipient' => 'me@example.com']);
    Http::fake(['*' => Http::sequence()->push(resultsPage([
        ['internal_id' => 'First1', 'title' => 'Pekný 4-izbový byt v Ružinove, novostavba'],
        ['internal_id' => 'Second2', 'title' => 'Pekný 4-izbový byt v Ružinove - novostavba'],
    ]))->push('<html><body></body></html>')]);

    Filter::factory()->create();

    $this->artisan(Sniff::class)->assertSuccessful();

    expect(Entry::pluck('internal_id')->all())->toBe(['First1']);

    Mail::assertSent(NewEntriesFound::class, fn (NewEntriesFound $mail): bool => $mail->entries->pluck('internal_id')->all() === ['First1']);
});

it('sniffs a single filter from the job, even an inactive one', function () {
    Http::fake(['*' => Http::response('<html><body></body></html>')]);

    $filter = Filter::factory()->inactive()->create();
    Filter::factory()->create();

    SniffFilterJob::dispatchSync($filter);

    Http::assertSentCount(1);
    Http::assertSent(fn (Request $request): bool => $request->url() === $filter->url());
});
