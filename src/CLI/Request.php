<?php

namespace Blarkinov\ElasticApp\CLI;

use Blarkinov\ElasticApp\Controllers\MainController;
use Blarkinov\ElasticApp\Service\ElasticSearch\ElasticSearch;
use Blarkinov\ElasticApp\Service\Validator;
use Throwable;

class Request
{

    public function __construct(private array $args) {}

    public function handle()
    {

        $mainController = new MainController();

        try {
            $validator = new Validator($this->args);
            $validator->validate();


            $es = new ElasticSearch($this->args);
            $es->search($this->args);
            
        } catch (\Throwable $th) {
            $mainController->badRequest($th->getMessage());
        }
    }


    /**
     * @return array ['response'=>JSON,'httpcode'=>int]
     */
    public function send(array $options): array
    {
        try {

            $ch = curl_init();

            curl_setopt_array($ch, $options);

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            return ['response' => $response, 'httpcode' => $httpcode];
        } catch (\Throwable $th) {
            return ['response' => false, 'httpcode' => '0'];
        }
    }
}
