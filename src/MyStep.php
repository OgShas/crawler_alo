<?php

use Crwlr\Crawler\Loader\Http\Messages\RespondedRequest;
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
            if ($crawler->filter('#categorymenu')->count() > 0) {

                $mainNames = $crawler->filter('.main')->each(function (Crawler $main) {
                    return $main->text();
                });

                yield $mainNames;
            } else if ($crawler->filter('#svgmap')->count() > 0) {
                $regionTitles = $crawler->filter('.svgpath')->each(function (Crawler $region) {
                    return $region->text();
                });

                yield $regionTitles;
            } else {

                $url = $crawler->getUri();
                yield $url;
            }
        }
    }
}



            /*

            $contentContainer = $crawler->filter('.right-listing');
            $categoryMenu = $crawler->filter('#categorymenu');

            if ($categoryMenu->count() > 0)
            {
                $menuLink = $categoryMenu->filter('.main');

                foreach ($menuLink as $menuLinks)
                {
                    $linksMenu = new Crawler($menuLinks);
                    yield $linksMenu->innerText();
                }

            }

            else if ($contentContainer->count() > 0)
                {
                $svgMapElement = $contentContainer->filter('.result-count ');

                if ($svgMapElement->count() > 0) {
                    $svgPathElements = $crawler->filter('.h1');

                    foreach ($svgPathElements as $svgPathElement) {
                        $svgPathCrawler = new Crawler($svgPathElement);
                          yield $svgPathCrawler->innerText();
                       // yield $svgPathCrawler->link();
                    }
                } else {
                    // #svgmap not found, return the URL
                    $url = $crawler->getUri();
                    $typeOfUrl = gettype($url);
                    echo $typeOfUrl;
                    yield (string)$url;
                }
            }
        }
    }
}


*/