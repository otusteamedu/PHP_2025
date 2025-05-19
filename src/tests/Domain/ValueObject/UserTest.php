<?php

use App\Domain\ValueObject\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserCanBeCreatedWithValidNumericValue(): void
    {
        $user = new User("1234");
        $this->assertEquals("1234", $user->getValue());
    }
    
}