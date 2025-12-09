<?php

namespace Tests\Functional;

use Tests\Support\FunctionalTester;

class RequestCest
{

    public function tryRequest(FunctionalTester $I)
    {
        $email = [
            'email' => 'user@example.com',
        ];

        $I->sendPOST('/', $email);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('this is a valid email');
    }
}
