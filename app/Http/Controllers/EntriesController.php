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
        $entries = Entry::where([
            'is_ignored' => false,
            'is_seen' => false,
            'is_favorite' => false,
        ])->orderBy('created_at', 'DESC')->get();

        return view('entries.index')->with('entries', $entries);
    }

    public function favorite(): View
    {
        $entries = Entry::where([
            'is_ignored' => false,
            'is_seen' => true,
            'is_favorite' => true,
        ])->orderBy('created_at', 'DESC')->get();

        return view('entries.favorite')->with('entries', $entries);
    }

    public function seen(): View
    {
        $entries = Entry::where([
            'is_ignored' => false,
            'is_seen' => true,
            'is_favorite' => false,
        ])->orderBy('created_at', 'DESC')->get();

        return view('entries.seen')->with('entries', $entries);
    }

    public function show(Entry $entry): View
    {
        $entry->update([
            'is_seen' => true,
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
            'is_favorite' => ! $entry->is_favorite,
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
