<?php

use Crwlr\Crawler\Result;
use Crwlr\Crawler\Stores\SimpleCsvFileStore;
use Crwlr\Crawler\Stores\Store;

class MyStore extends Store
{
    protected $storeDirectory;
    protected $fileName;

    public function __construct(string $storeDirectory, string $fileName)
    {
        $this->storeDirectory = $storeDirectory;
        $this->fileName = $fileName;
    }

    public function store(Result $result): void
    {
        if (!is_dir($this->storeDirectory)) {
            mkdir($this->storeDirectory, 0777, true);
        }
        $csvFileStore = new SimpleCsvFileStore($this->storeDirectory, $this->fileName);
        $csvFileStore->store($result);
        $this->logger->info('Result stored at: ' . $this->storeDirectory . '/' . $this->storeDirectory);
    }

}