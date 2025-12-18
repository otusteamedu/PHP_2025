<?php

declare(strict_types=1);

namespace Tests\acceptance;

use App\Tests\AcceptanceTester;
use Codeception\Util\HttpCode;

class CalculateCest
{
    public function testCalculatePageWorks(AcceptanceTester $I): void
    {
        $I->sendGet('/calculate');
        $I->seeResponseCodeIs(HttpCode::OK);
    }
}
