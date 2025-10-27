<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntryUpdateRequest;
use App\Models\Entry;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ParseController extends Controller
{
    public function index(): View
    {
        $entries = Entry::where('is_ignored', false)->orderBy('created_at', 'DESC')->get();

        return view('entries.index')->with('entries', $entries);
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

    public function favorite(Entry $entry): RedirectResponse
    {
        $entry->update([
            'is_favorite' => ! $entry->is_favorite,
        ]);

        return back();
    }

    public function ignore(Entry $entry): RedirectResponse
    {
        $entry->update([
            'is_ignored' => ! $entry->is_ignored,
        ]);

        return back();
    }
}
