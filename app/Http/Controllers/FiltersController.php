<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Models\Filter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FiltersController extends Controller
{
    public function index(): View
    {
        return view('filters.index')->with('filters', Filter::query()->orderBy('name')->get());
    }

    public function create(): View
    {
        return view('filters.create')->with('filter', new Filter(['is_active' => true]));
    }

    public function store(FilterRequest $request): RedirectResponse
    {
        Filter::create($request->filterAttributes());

        return to_route('filters.index');
    }

    public function edit(Filter $filter): View
    {
        return view('filters.edit')->with('filter', $filter);
    }

    public function update(FilterRequest $request, Filter $filter): RedirectResponse
    {
        $filter->update($request->filterAttributes());

        return to_route('filters.index');
    }

    public function destroy(Filter $filter): RedirectResponse
    {
        $filter->delete();

        return to_route('filters.index');
    }

    public function toggleActive(Filter $filter): RedirectResponse
    {
        $filter->update([
            'is_active' => ! $filter->is_active,
        ]);

        return back();
    }
}
