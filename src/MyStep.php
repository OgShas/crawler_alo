<?php

use Crwlr\Crawler\Loader\Http\Messages\RespondedRequest;
use Crwlr\Crawler\Steps\Dom;
use Crwlr\Crawler\Steps\Loading\Http;
use Crwlr\Crawler\Steps\Step;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;


class MyStep extends Step
{
    private ?string $baseUrl = null;

    private ?string $noResultName = null;

    private ?string $noResultUrl = null;

    protected function validateAndSanitizeInput(mixed $input): Crawler
    {
        $this->noResultName = $input['subMenuName'];
        $this->noResultUrl = $input['subMenuUrl'];

        if ($input['subMenuUrlResponse'] instanceof RespondedRequest) {
            $this->baseUrl = $input['subMenuUrlResponse']->effectiveUri();
        }

        return $this->validateAndSanitizeToDomCrawlerInstance($input['subMenuUrlResponse']);
    }

    protected function invoke(mixed $input): Generator
    {
        /** @var Crawler $crawler */
        $crawler = $input;

        // Extract the menu items and links
        if ($crawler->filter('#categorymenu')->count() > 0) {
            $menusData = $crawler->filter('#categorymenu .main')->each(function (Crawler $category) {
                $link = $category->filter('a')->first()->link();

                return [
                    'subSubName' => $category->filter('a')->first()->text(),
                    'subSubUrl' => $link->getUri(),
                ];
            });

            foreach ($menusData as $menuData) {
                yield $menuData;
            }

            return;
        }

        // Extract the map-city-link
        if ($crawler->filter('#svgmap')->count() > 0) {
            $regionsData = $crawler->filter('#svgmap #paths [region_id]')->each(function (Crawler $region) use ($crawler) {
                $region_id = $region->attr('region_id');
                $title = $region->text();

                return [
                    'subSubName' => $title,
                    'subSubUrl' => $this->baseUrl . '?region_id=' . $region_id,
                ];
            });

            foreach ($regionsData as $regionData) {
                yield $regionData;
            }

            return;
        }

        yield [
            'subSubName' => $this->noResultName,
            'subSubUrl' => $this->noResultUrl,
        ];
    }
}
