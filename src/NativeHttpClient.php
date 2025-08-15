<?php

namespace Elisad5791\Phpapp;

class NativeHttpClient implements HttpClientInterface {
    public function get(string $url): string 
    {
        return file_get_contents($url);
    }
}