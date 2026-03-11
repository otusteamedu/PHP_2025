<?php

namespace Pryaniki\App\Application\Services;

interface SearchIndexManagerInterface
{
    public function createIndex(): void;

    public function resetIndex(): void;

    public function import(): void;

    public function search(): void;
}