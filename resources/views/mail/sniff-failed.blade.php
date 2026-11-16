@php
/** @var string $error */
/** @var Illuminate\Support\Carbon $startedAt */
@endphp

<x-mail::message>
# Sniff failed

The sniff started at {{ $startedAt->format('Y-m-d H:i') }} failed:

<x-mail::panel>
{{ $error }}
</x-mail::panel>

<x-mail::button :url="url('/')">
Show sniff runs
</x-mail::button>
</x-mail::message>
