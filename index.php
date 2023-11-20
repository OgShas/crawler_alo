<?php

require_once __DIR__ . '/vendor/autoload.php';

use Crawler\MyCrawler;
use Crwlr\Crawler\Steps\Dom;
use Crwlr\Crawler\Steps\Html;
use Crwlr\Crawler\Steps\Loading\Http;
use Crwlr\Crawler\Stores\SimpleCsvFileStore;


/*
(new MyCrawler())
    ->setStore(new SimpleCsvFileStore('./store', 'alo.bg'))
    ->input('https://www.alo.bg/obiavi/avto-moto/avtomobili-djipove-pikapi/')

    ->addStep(
        Http::get()
            ->paginate('.paginator_wrapper', 1)
    )
    ->addStep(
        Html::each('[id^="adrows_"]')
            ->extract([
                'title' => Dom::cssSelector('h3')->first(),
                'url' => Dom::cssSelector('a')->first()->link(),
                'price' => Dom::cssSelector('.nowrap')->first(),
//                'p' => Dom::cssSelector('p')->first()->innerText(),
            ])
            ->refineOutput('price', function (mixed $output) {

                $output = str_replace(html_entity_decode('&nbsp;',ENT_COMPAT,''),' ',$output);
                $output = str_replace(['лв.',' '], '',$output);

                return (float) $output;
            })
            ->addToResult()

    )
    ->runAndTraverse();

*/


(new MyCrawler())
    ->setStore(new SimpleCsvFileStore('./store-Custom', 'alo.bg'))
    ->input('https://www.alo.bg/obiavi/elektronika/')
    ->addStep(
        Http::get()
           // ->paginate('.paginator_wrapper', 1)
    )
    ->addStep(Html::each('#categorymenu .main')
        ->extract([
            'title' => Dom::cssSelector('a')->first()->innerText(),
            'url' => Dom::cssSelector('a')->first()->link()
        ])
    )->input('url')
    ->addStep(
        Http::get()
    )
    ->addStep(
        Html::each('[id^="adrows_"]')
            ->extract([
                'title' => Dom::cssSelector('h3')->first(),
                'url' => Dom::cssSelector('a')->first()->link(),
                'price' => Dom::cssSelector('.nowrap')->first(),
                 'p' => Dom::cssSelector('p')->first()->innerText(),
            ])
            ->refineOutput('price', function (mixed $output) {

                $output = str_replace(html_entity_decode('&nbsp;',ENT_COMPAT,''),' ',$output);
                $output = str_replace(['лв.',' '], '',$output);

                return (float) $output;
            })
            ->addToResult()
    )
    ->runAndTraverse();

