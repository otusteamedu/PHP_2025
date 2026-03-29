<?php

namespace Tests\Services;

use App\Controller\EmailValidatorController;
use App\Http\Request;
use App\Service\EmailValidatorService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use App\Exception\HttpException;

class EmailValidatorControllerTest extends TestCase
{
    public function testCorrectEmailWhenJsonBody()
    {
        $request = $this->createMock(Request::Class);
        $request->method('getArrayBody')->willReturn(['emails' => ['test@mail.ru', 'mail@yandex.com']]);
        $controller = new EmailValidatorController();
        $response = $controller($request);

        $this->assertSame(200, $response->getHttpCode());
    }
    
    public function testCorrectEmailWhenForm()
    {
        $request = $this->createMock(Request::Class);
        $request->method('getPostData')->willReturn(['emails' => ['test@mail.ru', 'mail@yandex.com']]);
        $controller = new EmailValidatorController();
        $response = $controller($request);

        $this->assertSame(200, $response->getHttpCode());
    }
 
    public function testValidateEmailEmpty()
    {
        $request = $this->createMock(Request::Class);
        $request->method('getArrayBody')->willReturn(null);

        $this->expectException(HttpException::class);

        $controller = new EmailValidatorController();
        $response = $controller($request);
    }
    
}