<?php

require_once __DIR__ . '/vendor/autoload.php';

use Crawler\MyCrawler;
use Crwlr\Crawler\Steps\Dom;
use Crwlr\Crawler\Steps\Html;
use Crwlr\Crawler\Steps\Loading\Http;
use Crwlr\Crawler\Stores\SimpleCsvFileStore;

include "src/MyStep.php";

(new MyCrawler())
    ->setStore(new SimpleCsvFileStore('./store-Custom', 'alo.bg'))
    ->input('https://www.alo.bg')
    //Step 1 - Get alo.bg
    ->addStep(Http::get())
    //Step 2 -take menu 1 List items
    ->addStep(
        Html::each('#categorymenu .main')
            ->extract([
                'mainMenuName' => Dom::cssSelector('a')->first()->text(),
                'mainMenuUrl' => Dom::cssSelector('a')->first()->link()
            ])
           // ->maxOutputs(3)
    )
    //Step 2 - take menu/2 list item (depth/2)
    ->addStep(
        Http::get()
            ->keepInputData()
            ->useInputKey('mainMenuUrl')
            ->outputKey('mainMenuUrlResponse')
            //->maxOutputs(3)
    )
    //Step 3 - extract every item from menu (depth/2)
    ->addStep(
        Html::each('#categorymenu .main')
            ->useInputKey('mainMenuUrlResponse')
            ->keepInputData()
            ->extract([
                'subMenuName' => Dom::cssSelector('a')->first()->text(),
                'subMenuUrl' => Dom::cssSelector('a')->first()->link(),
            ])
           //->maxOutputs(3)
    )
    ->addStep(
        Http::get()
            ->keepInputData()
            ->useInputKey('subMenuUrl')
            ->outputKey('subMenuUrlResponse')
            //->maxOutputs(3)
    )
    ->addStep(
        (new MyStep())
            ->keepInputData()
            ->addLaterToResult([
                'mainMenuName',
//                'mainMenuUrl',
                'subSubName',
//                'subSubUrl',
            ])
            //->maxOutputs(3)
    )
    ->addStep(
        Http::get()->paginate('.paginator_wrapper', 3)
            ->useInputKey('subSubUrl')
    )
    ->addStep(
        Html::each('#content_container [id^="adrows_"]')
            ->extract([
                'title' => Dom::cssSelector('h3')->first(),
                'url' => Dom::cssSelector('a')->first()->link(),
                'price' => Dom::cssSelector('.nowrap')->first(),
                'description' => Dom::cssSelector('p')->first()->innerText(),
            ])
            ->refineOutput('price', function (mixed $output) {
                if (is_array($output)) {
                    return $output;
                }

                $output = str_replace(html_entity_decode('&nbsp;', ENT_COMPAT, ''), ' ', $output);
                $output = str_replace(['лв.', ' '], '', $output);

                return (float)$output;
            })
            ->addToResult([
                'title',
                'url',
                'price',
                'description',
            ])
    )
    ->runAndTraverse();
