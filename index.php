<?php

require_once __DIR__ . '/vendor/autoload.php';

use Crawler\MyCrawler;
use Crwlr\Crawler\Steps\Dom;
use Crwlr\Crawler\Steps\Html;
use Crwlr\Crawler\Steps\Loading\Http;
use Crwlr\Crawler\Stores\SimpleCsvFileStore;

(new MyCrawler())
    ->setStore(new SimpleCsvFileStore('./store', 'alo.bg'))
    ->input('https://www.alo.bg/obiavi/avto-moto/avtomobili-djipove-pikapi/')
    ->addStep(
        Http::get()
            ->paginate('.paginator_wrapper', 10)
    )
    ->addStep(
        Html::each('[id^="adrows_"]')
            ->extract([
                'title' => 'h3',
                'url' => Dom::cssSelector('a')->first()->link(),
                'p' => Dom::cssSelector('p')->first()->innerText(),
            ])
            ->addToResult()
    )
    ->runAndTraverse();
