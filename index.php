<?php


include 'MyCrawler.php';
include 'MyStore.php';

use Crwlr\Crawler\Steps;
use Crwlr\Crawler\Steps\Dom;
use Crwlr\Crawler\Steps\Html;
use Crwlr\Crawler\Steps\Loading\Http;

$storeDirectory = './Store';
$filename = 'alo.bg.csv';

$crawler = new MyCrawler();
$crawler->setStore(new MyStore($storeDirectory, $filename));


$crawler->input('https://www.alo.bg/obiavi/avto-moto/avtomobili-djipove-pikapi/')
    ->addStep(Http::get()->maxOutputs(1))
    ->addStep(
        Html::each('#content_container > [id^="adrows_"]')
            ->extract([
                'title' => 'h3',
                'url' => Dom::cssSelector('a')->first()->link(),
                'p' => Dom::cssSelector('p')->first()->innerText(),
            ])
            ->addToResult()
    )
    ->runAndTraverse();


/*
foreach ($crawler->run() as $result) {

    $title = $result->get('title');
    echo $title."\n";
    $url = $result->get('url');
    echo $url."\n";
    $par = $result->get('p');
    echo  $par."\n";
}
*/