<?php

namespace App\Http\Controllers;

use App\Models\SniffRun;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IndexController extends Controller
{
    protected const int SNIFF_RUNS_LIMIT = 50;

    public function __invoke(Request $request): View
    {
        $sniffRuns = $request->user()
            ? SniffRun::query()->latest('started_at')->latest('id')->limit(self::SNIFF_RUNS_LIMIT)->get()
            : collect();

        return view('index')->with('sniffRuns', $sniffRuns);
    }
}
