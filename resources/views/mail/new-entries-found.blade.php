@php
/** @var Illuminate\Database\Eloquent\Collection<int, App\Models\Entry> $entries */
@endphp

<x-mail::message>
# {{ $entries->count() }} new {{ Str::plural('apartment', $entries->count()) }}

@foreach($entries as $entry)
**[{{ $entry->title }}]({{ route('entries.show', $entry) }})**<br>
{{ $entry->district }}, {{ $entry->street }} · {{ $entry->rooms }} rooms · {{ $entry->area }} m2<br>
€ {{ number_format($entry->price, 0, ',', ' ') }} · € {{ number_format($entry->price_per_sqm, 0, ',', ' ') }} / m2

@endforeach
<x-mail::button :url="route('entries.index')">
Show all new
</x-mail::button>
</x-mail::message>
