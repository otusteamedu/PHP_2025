<?php

namespace Elisad5791\Phpapp;

interface HttpClientInterface {
    public function get(string $url): string;
}