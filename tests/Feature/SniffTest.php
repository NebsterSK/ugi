<?php

use App\Console\Commands\Sniff;
use App\Models\Filter;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Filter::query()->delete();

    Sleep::fake();
    Http::fake(['*' => Http::response('<html><body></body></html>')]);
});

it('sniffs every active filter', function () {
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
    Filter::factory()->inactive()->create();

    $this->artisan(Sniff::class)
        ->expectsOutput('No active filters, nothing to sniff.')
        ->assertSuccessful();

    Http::assertNothingSent();
});
