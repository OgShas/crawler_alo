<?php

require 'vendor/autoload.php';

use Crwlr\Crawler\Cache\FileCache;
use Crwlr\Crawler\HttpCrawler;
use Crwlr\Crawler\Loader\Http\HttpLoader;
use Crwlr\Crawler\UserAgents\BotUserAgent;
use Crwlr\Crawler\UserAgents\UserAgentInterface;
use Psr\Log\LoggerInterface;

class MyCrawler extends HttpCrawler
{
    protected function userAgent(): UserAgentInterface
    {
        return BotUserAgent::make('MyBot');
    }

    protected function loader(UserAgentInterface $userAgent, \Psr\Log\LoggerInterface $logger): array|\Crwlr\Crawler\Loader\LoaderInterface
    {
        $loader = new HttpLoader($userAgent, logger: $logger);

        $cache = new FileCache(__DIR__ . '/cache');
        $cache->ttl(60);

        $loader->setCache($cache);

        return $loader;
    }
}

