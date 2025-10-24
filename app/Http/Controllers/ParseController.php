<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Parse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\DomCrawler\Crawler;

class ParseController extends Controller
{
    public function index(): View
    {
        $entries = Entry::get();

        return view('index')->with('entries', $entries);
    }

    public function crawl()
    {
        $url = 'https://www.nehnutelnosti.sk/vysledky/predaj?locations=100012514&locations=100012524&locations=100012513&locations=100012511&categories=300001&categories=14&priceTo=270000&areaFrom=70&priceFrom=220000';

        $response = Http::get($url);

        Parse::create([
            'url' => $url,
            'content' => $response->body(),
        ]);

        return 1;
    }

    public function parse()
    {
        $parse = Parse::find(1);
        Entry::query()->delete();

        $crawler = new Crawler($parse->content);

        $crawler->filter('div.MuiGrid2-root.MuiGrid2-direction-xs-row.MuiGrid2-grid-xs-12.MuiGrid2-grid-md-8')->each(function (Crawler $node) {
            $url = $node->filter('a.MuiBox-root')->first()->attr('href');
            $internalId = Str::of($url)->after('https://www.nehnutelnosti.sk/detail/')->before('/')->toString();
            $title = $node->filter('h2.MuiTypography-root.MuiTypography-h4')->first()->text();

            Entry::create([
                'internal_id' => $internalId,
                'url' => $url,
                'title' => $title,
            ]);
        });

        return 1;
    }
}
