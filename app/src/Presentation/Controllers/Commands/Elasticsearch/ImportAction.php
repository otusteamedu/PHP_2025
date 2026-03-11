<?php

namespace Pryaniki\App\Presentation\Controllers\Commands\Elasticsearch;

class ImportAction extends BaseAction
{
    public function run(): void
    {
        $this->indexManager->import();
    }
}