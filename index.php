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
            ->paginate('.paginator_wrapper', 1)
    )
    ->addStep(
        Html::root()
            ->extract([
                'title' => '[id^="adrows_"] h3',
                'url' => Dom::cssSelector('[id^="adrows_"] a[href^="/"]')->link(),
               //'p' => Dom::cssSelector('p')->first()->innerText(),
                'price' => '.nowrap',
            ])
           ->refineOutput('price',fn($output) => str_replace('лв','fostata',$output))
             // ->refineOutput('price', \Crwlr\Crawler\Steps\Refiners\StringRefiner::replace('лв.','fostata'))
            ->addToResult()

    )
    ->runAndTraverse();

/*

(new MyCrawler())
    ->setStore(new SimpleCsvFileStore('./store','alo.bg'))
    ->input('https://www.alo.bg/obiavi/avto-moto/avtomobili-djipove-pikapi/')
    ->addStep(Http::get())
    ->addStep(
        Html::root()
        ->extract([
            'title' => 'h3',
            'info' =>'[id^="adrows_"]',
        ])
        ->addLaterToResult(['title'])
    )
    ->addStep(Http::get()->useInputKey('info'))
    ->addStep(
        Html::root()
        ->extract([
            'name' => 'h3',
            'url' => 'a',
            'price' => 'nowrap'
        ])
        ->addToResult()
    )
    ->runAndTraverse();


/*

//Crawling Whole Website

(new MyCrawler())
    ->setStore(new SimpleCsvFileStore('./Store-WholeWeb', 'alo.bg'))
    ->input('https://www.alo.bg')
    ->addStep(
        Http::crawl()
            ->pathStartsWith('/obiavi/zapoznanstva-eskort/')
            ->depth(2)

        ->addToResult()
    )
->runAndTraverse();

*/