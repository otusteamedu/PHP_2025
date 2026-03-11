<?php
declare(strict_types=1);

namespace Pryaniki\App\Presentation\Controllers\Commands\Elasticsearch;

class CreateIndexAction extends BaseAction
{
    public function run(): void
    {
        $this->indexManager->createIndex();
    }
}