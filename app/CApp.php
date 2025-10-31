<?php
declare(strict_types=1);

namespace MyApp;

class CApp
{
    private CRequester $CRequester;

    public function __construct()
    {
        $Validator = new CValidator();
        $Responser = new CResponser();
        $this->CRequester = new CRequester($Validator, $Responser);
    }

    public function startup(): ?string
    {
        return $this->CRequester->handle($_POST);
    }
}