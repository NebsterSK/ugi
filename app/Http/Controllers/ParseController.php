<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class ParseController extends Controller
{
    public function parse()
    {
        $response = Http::get('https://www.nehnutelnosti.sk/vysledky/predaj?locations=100012514&locations=100012524&locations=100012513&locations=100012511&categories=300001&categories=14&priceTo=270000&areaFrom=70&priceFrom=220000');
        $crawler = new Crawler($response->body());

        $crawler->filter('h2.MuiTypography-root.MuiTypography-h4')->each(function (Crawler $node) {
            echo $node->text() . '<br>';
        });

//        dd($response->getBody());

        return 1;
    }
}
