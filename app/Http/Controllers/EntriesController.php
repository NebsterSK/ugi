<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntryUpdateRequest;
use App\Models\Entry;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EntriesController extends Controller
{
    public function index(): View
    {
        return view('entries.index');
    }

    public function favorite(): View
    {
        return view('entries.favorite');
    }

    public function seen(): View
    {
        return view('entries.seen');
    }

    public function show(Entry $entry): View
    {
        $entry->update([
            'seen_at' => now(),
        ]);

        return view('entries.show')->with('entry', $entry);
    }

    public function update(EntryUpdateRequest $request, Entry $entry): RedirectResponse
    {
        $entry->update([
            'comment' => $request->string('comment'),
        ]);

        return back();
    }

    public function toggleFavorite(Entry $entry): RedirectResponse
    {
        $entry->update([
            'favorited_at' => $entry->favorited_at ? null : now(),
        ]);

        return back();
    }

    public function toggleIgnore(Entry $entry): RedirectResponse
    {
        $entry->update([
            'is_ignored' => ! $entry->is_ignored,
        ]);

        return to_route('entries.index');
    }
}
