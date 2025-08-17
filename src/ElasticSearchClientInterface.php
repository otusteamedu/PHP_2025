<?php

namespace Elisad5791\Phpapp;

interface ElasticSearchClientInterface
{
    public function get(array $params): array;
    public function search(array $params): array;
    public function bulk(array $params): void;
    public function delete(array $params): void;
}