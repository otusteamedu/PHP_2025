<?php

namespace Blarkinov\ElasticApp\App;

use Blarkinov\ElasticApp\CLI\Request;
use Blarkinov\ElasticApp\CLI\Response;
use Blarkinov\ElasticApp\Service\ElasticSearch\ElasticSearch;

class App
{
    public function run(array $args)
    {
        if ((new ElasticSearch)->createDB()) {
            if ((new ElasticSearch)->fillFull()) (new Request($args))->handle();
        } else (new Response())->send(1, 'error create DB');


        //  (new ElasticSearch)->delete();
    }
}
