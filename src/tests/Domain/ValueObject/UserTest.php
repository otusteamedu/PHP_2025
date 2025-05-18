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
    
    public function testUserCannotBeCreatedWithNonNumericValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Пользователь John не сущесвует!");
        
        new User("John");
    }
    
    public function testUserCannotBeCreatedWithEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Пользователь  не сущесвует!");
        
        new User("");
    }
    
    public function testUserCannotBeCreatedWithSpecialCharacters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Пользователь @user не сущесвует!");
        
        new User("@user");
    }
}