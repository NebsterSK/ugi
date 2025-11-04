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
        $entries = Entry::newState()->orderBy('created_at', 'DESC')->get();

        return view('entries.index')->with('entries', $entries);
    }

    public function favorite(): View
    {
        $entries = Entry::favorite()->orderBy('favorited_at', 'DESC')->orderBy('created_at', 'DESC')->get();

        return view('entries.favorite')->with('entries', $entries);
    }

    public function seen(): View
    {
        $entries = Entry::seen()->orderBy('seen_at', 'DESC')->orderBy('created_at', 'DESC')->get();

        return view('entries.seen')->with('entries', $entries);
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

        return back();
    }
}
