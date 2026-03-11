<?php

namespace Pryaniki\App\Presentation\Controllers\Commands\Elasticsearch;

use Pryaniki\App\Application\Services\SearchIndexManagerInterface;

abstract class BaseAction
{
    protected SearchIndexManagerInterface $indexManager;
    public function __construct(SearchIndexManagerInterface $indexManager)
    {
        $this->indexManager = $indexManager;
    }

    abstract public function run(): void;
}