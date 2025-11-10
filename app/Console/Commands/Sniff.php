<?php

namespace App\Console\Commands;

use App\Models\Entry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

class Sniff extends Command
{
    protected $signature = 'sniff';
    protected $description = 'Sniff us a new home.';

    protected const string BASE_URL = 'https://www.nehnutelnosti.sk/vysledky/predaj?locations=100012514&locations=100012524&locations=100012513&locations=100012511&categories=300001&categories=14&priceTo=280000&areaFrom=70&priceFrom=240000';

    protected const string SELECTOR_ENTRY = 'div.MuiGrid2-root.MuiGrid2-direction-xs-row.MuiGrid2-grid-xs-12.MuiGrid2-grid-md-8';
    protected const string SELECTOR_ENTRY_URL = 'a.MuiBox-root';
    protected const string SELECTOR_TITLE = 'h2.MuiTypography-root.MuiTypography-h4';
    protected const string SELECTOR_ADDRESS = 'div.MuiStack-root > p.MuiTypography-root.MuiTypography-body2.MuiTypography-noWrap';
    protected const string SELECTOR_ROOMS = 'div.MuiStack-root > p.MuiTypography-root.MuiTypography-body2.MuiTypography-noWrap';
    protected const string SELECTOR_AREA = 'div.MuiStack-root > p.MuiTypography-root.MuiTypography-body2';
    protected const string SELECTOR_PRICE = 'a.MuiStack-root > p.MuiTypography-root.MuiTypography-h5';
    protected const string SELECTOR_PRICE_PER_SQM = 'a.MuiStack-root > p.MuiTypography-root.MuiTypography-label1';

    public function handle(): int
    {
        $page = 1;

        do {
            if ($page === 1) {
                $url = self::BASE_URL;
            } else {
                $url = self::BASE_URL . '&page=' . $page;
            }

            $this->info('Requesting: ' . $url);

            $response = Http::get($url);

            $crawler = new Crawler($response->body());

            $crawler->filter(self::SELECTOR_ENTRY)->each(function (Crawler $node) {
                $entryUrl = $node->filter(self::SELECTOR_ENTRY_URL)->first()->attr('href');
                $internalId = Str::of($entryUrl)->after('https://www.nehnutelnosti.sk/detail/')->before('/')->toString();

                $title = $node->filter(self::SELECTOR_TITLE)->first()->text();

                $wholeAddress = $node->filter(self::SELECTOR_ADDRESS)->first()->text();
                $street = Str::of($wholeAddress)->before(',');
                $district = Str::of($wholeAddress)->after(', Bratislava-')->before(',')->replace('Bratislava-', '');

                $roomsText = $node->filter(self::SELECTOR_ROOMS)->slice(1)->text();
                $rooms = Str::of($roomsText)->before(' ')->toInteger();

                $areaText = $node->filter(self::SELECTOR_AREA)->slice(2)->text();
                $area = Str::of($areaText)->before(' m')->toInteger();

                $priceText = $node->filter(self::SELECTOR_PRICE)->first()->text();
                $price = Str::of($priceText)->before(' €')->replace("\u{A0}", '')->toInteger();

                $pricePerSqmText = $node->filter(self::SELECTOR_PRICE_PER_SQM)->first()->text();
                $pricePerSqm = Str::of($pricePerSqmText)->before(' €')->replace("\u{A0}", '')->toInteger();

                try {
                    Entry::upsert([
                        'internal_id' => $internalId,
                        'url' => $entryUrl,
                        'title' => $title,
                        'rooms' => $rooms,
                        'street' => $street,
                        'district' => $district,
                        'area' => $area,
                        'price' => $price,
                        'price_per_sqm' => $pricePerSqm,
                    ], [
                        'internal_id',
                    ], [
                        'title',
                        'rooms',
                        'street',
                        'district',
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

            $page++;

            sleep(rand(3, 6));
        } while ($response->ok());

        return self::SUCCESS;
    }
}
