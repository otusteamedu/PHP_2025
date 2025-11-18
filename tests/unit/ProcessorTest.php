<?php

use PHPUnit\Framework\TestCase;
use App\Processor;
use App\http\Request;
use App\validation\Validator;

//тесты для Processor::process()
class ProcessorTest extends TestCase
{
    //позитивный
    public function testProcessesMultipleEmails(): void
    {
        $validator = $this->createMock(Validator::class);
        $validator->method('isValid')->willReturnCallback(function ($email) {
            return $email === 'valid@example.com';
        });

        $processor = new Processor($validator);
        $request = new Request(['emails' => "valid@example.com\ninvalid-email"]);
        $result = $processor->process($request);

        $this->assertSame(['valid@example.com'], $result);
    }

    //негативный
    public function testProcessesOnlyInvalidEmails(): void
    {
        $validator = $this->createMock(Validator::class);
        $validator->method('isValid')->willReturn(false);

        $processor = new Processor($validator);
        $request = new Request(['emails' => "invalid-email\nwrong@bad"]);
        $result = $processor->process($request);

        $this->assertSame([], $result);
    }
}
