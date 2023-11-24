<?php

use Crwlr\Crawler\Loader\Http\Messages\RespondedRequest;
use Crwlr\Crawler\Steps\Dom;
use Crwlr\Crawler\Steps\Loading\Http;
use Crwlr\Crawler\Steps\Step;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;



class MyStep extends Step
{


    protected function validateAndSanitizeInput(mixed $input): Crawler
    {
        if ($input === null) {
            throw new InvalidArgumentException('Input cannot be null.');
        }

        if ($input instanceof Crawler) {
            return $input;
        }

        if (is_string($input) || $input instanceof Stringable) {
            return new Crawler($input);
        }

        if ($input instanceof RespondedRequest) {
            $response = $input->response;
            return new Crawler((string)$response->getBody());
        }

        throw new InvalidArgumentException('Input must be string, stringable, or HTTP response (RespondedRequest).');
    }

    protected function invoke(mixed $input): Generator
    {
        $crawler = $this->validateAndSanitizeInput($input);

        if ($crawler instanceof Crawler) {

            // Extract the menu items and links
            if ($crawler->filter('#categorymenu')->count() > 0) {
                $menuData = $crawler->filter('#categorymenu .main')->each(function (Crawler $category) {
                    $link = $category->filter('a')->first()->link();
                    return [
                        'MenuDebth' => $category->filter('a')->first()->text(),
                        'urlDebth' => $link->getUri(),
                    ];
                });

                    yield $menuData;
            }

            // Extract the map-city-link
             if ($crawler->filter('#svgmap')->count() > 0) {
                 /*
                $regionData = $crawler->filter('#svgmap [id^="title_"]')->each(function (Crawler $region) {
                    //$link = $region->filter('a')->first()->link();
                    return [
                        'text' => $region->filter('title')->first()->text(),
                    ];
                });
                    yield $regionData;
            }
                 */

                 //Working Fine
                 $regionData = $crawler->filter('.grid .grid-item')->each(function (Crawler $region) {

                     return [
                         'text' => $region->filter('a')->first()->text(),
                     ];
                 });
                 yield $regionData;
             }


            else {
                $url = $crawler->getUri();
                yield (string)$url;
            }
        }
    }
}
