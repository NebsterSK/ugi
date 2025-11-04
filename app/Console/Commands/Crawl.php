<?php

namespace App\Console\Commands;

use App\Models\Entry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

class Crawl extends Command
{
    protected $signature = 'crawl';

    protected $description = '';

    public function handle(): int
    {
        $baseUrl = 'https://www.nehnutelnosti.sk/vysledky/predaj?locations=100012514&locations=100012524&locations=100012513&locations=100012511&categories=300001&categories=14&priceTo=270000&areaFrom=70&priceFrom=220000';
        $page = 1;

//        do {
            if ($page === 1) {
                $url = $baseUrl;
            } else {
                $url = $baseUrl . '&page=' . $page;
            }

            $this->info('Requesting: ' . $url);

            $response = Http::get($url);

            $crawler = new Crawler($response->body());

            $crawler->filter('div.MuiGrid2-root.MuiGrid2-direction-xs-row.MuiGrid2-grid-xs-12.MuiGrid2-grid-md-8')->each(function (Crawler $node) {
                $url = $node->filter('a.MuiBox-root')->first()->attr('href');
                $internalId = Str::of($url)->after('https://www.nehnutelnosti.sk/detail/')->before('/')->toString();

                $title = $node->filter('h2.MuiTypography-root.MuiTypography-h4')->first()->text();

                $wholeAddress = $node->filter('div.MuiStack-root > p.MuiTypography-root.MuiTypography-body2.MuiTypography-noWrap')->first()->text();
                $street = Str::of($wholeAddress)->before(',');
                $area = Str::of($wholeAddress)->after(', Bratislava')->before(',');

                $roomsText = $node->filter('div.MuiStack-root > p.MuiTypography-root.MuiTypography-body2.MuiTypography-noWrap')->slice(1)->text();
                $rooms = Str::of($roomsText)->before(' ')->toInteger();

                $priceText = $node->filter('a.MuiStack-root > p.MuiTypography-root.MuiTypography-h5')->first()->text();
                $price = Str::of($priceText)->before(' €')->replace("\u{A0}", '')->toInteger();

                $priceText = $node->filter('a.MuiStack-root > p.MuiTypography-root.MuiTypography-label1')->first()->text();
                $price_per_sqm = Str::of($priceText)->before(' €')->replace("\u{A0}", '')->toInteger();

                try {
                    Entry::upsert([
                        'internal_id' => $internalId,
                        'url' => $url,
                        'title' => $title,
                        'rooms' => $rooms,
                        'street' => $street,
                        'area' => $area,
                        'price' => $price,
                        'price_per_sqm' => $price_per_sqm,
                    ], [
                        'internal_id',
                    ], [
                        'title',
                        'rooms',
                        'street',
                        'area',
                        'price',
                        'price_per_sqm',
                    ]);
                } catch (Throwable $e) {
                    Log::error('Entry was not created or updated', [
                        'exception_message' => $e->getMessage(),
                        'exception_file' => $e->getFile(),
                        'exception_line' => $e->getLine(),
                        'internal_id' => $internalId,
                        'title' => $title,
                    ]);

                    $this->error('Entry was not created or updated: ' . $title);
                }

                $this->line('Entry created or updated: ' . $title);
            });

//            $page++;

//            sleep(rand(3, 6));
//        } while ($response->ok());

        return self::SUCCESS;
    }
}
