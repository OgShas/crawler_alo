<?php

require_once __DIR__ . '/vendor/autoload.php';

use Crawler\MyCrawler;
use Crwlr\Crawler\Steps\Dom;
use Crwlr\Crawler\Steps\Html;
use Crwlr\Crawler\Steps\Loading\Http;
use Crwlr\Crawler\Stores\SimpleCsvFileStore;

include "src/MyStep.php";

$customStep = new MyStep();



//  Current Project -


(new MyCrawler())
    ->setStore(new SimpleCsvFileStore('./store-Custom', 'alo.bg'))

    //Step 1 - Open alo.bg
    ->input('https://www.alo.bg')
    ->addStep(
        Http::get()
    )
    //Step 2 -take menu 1 List items
    ->addStep(Html::each('#categorymenu .main')
        ->extract([
            //'menuItem' => Dom::cssSelector('a')->first()->innerText(),
            'url' => Dom::cssSelector('a')->first()->link()
        ])//addLaterToResult('menuItem ')
    )
    //Step 2 - take menu/2 list item (debth/2)
    ->addStep(
        Http::get()
            ->useInputKey('url')
    )
    //Step 3 - extract every item from menu (debth/2)
    ->addStep(
        Html::each('#categorymenu .main')
            ->extract([
               'MenuName'  => Dom::cssSelector('a')->first()->innerText(),
                'urlDebth' => Dom::cssSelector('a')->first()->link(),
            ])->addToResult()
    )//->runAndTraverse(); //-> show result debth-2  //  -> Get all links

    ->addStep(
         Http::get()
             ->useInputKey('urlDebth')
              )
        ->addStep($customStep->addToResult()
        )->runAndTraverse();


            /*
        )->input('urlDebth')

    ->addStep(
        Http::get()
    )
    ->addStep(
        Html::each('#categorymenu .main')
            ->extract([
                'MenuDebth2'  => Dom::cssSelector('a')->first()->innerText(),
                'urlDebth2' => Dom::cssSelector('a')->first()->link(),
            ])->addToResult()//->addLaterToResult('MenuDebth')
    )
    ->addStep(
                Http::get()
                    ->useInputKey('urlDebth2')
            )
            ->addStep(
        Html::each('#content_container [id^="adrows_"]')
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


*/
