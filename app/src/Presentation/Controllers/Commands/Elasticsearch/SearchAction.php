<?php
declare(strict_types=1);

namespace Pryaniki\App\Presentation\Controllers\Commands\Elasticsearch;

class SearchAction extends BaseAction
{
    public function run(): void
    {
        $this->indexManager->search();
    }
}