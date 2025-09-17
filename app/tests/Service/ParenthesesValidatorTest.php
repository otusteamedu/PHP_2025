<?php

namespace App\Tests\Service;

use App\Service\ParenthesesValidator;
use PHPUnit\Framework\TestCase;

class ParenthesesValidatorTest extends TestCase
{
    private ParenthesesValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new ParenthesesValidator();
    }

    public function testBalancedSimple(): void
    {
        $this->assertTrue($this->validator->isBalanced("(()())"));
        $this->assertTrue($this->validator->isBalanced("(())()"));
        $this->assertTrue($this->validator->isBalanced("()()()"));
    }

    public function testUnbalancedMoreClosing(): void
    {
        $this->assertFalse($this->validator->isBalanced("())"));
        $this->assertFalse($this->validator->isBalanced(")("));
        $this->assertFalse($this->validator->isBalanced(")()("));
    }

    public function testUnbalancedMoreOpening(): void
    {
        $this->assertFalse($this->validator->isBalanced("((()"));
        $this->assertFalse($this->validator->isBalanced("(("));
    }

    public function testIgnoresOtherCharacters(): void
    {
        $this->assertTrue($this->validator->isBalanced("a(b)c(d)e"));
        $this->assertFalse($this->validator->isBalanced("a)b(c")); // ранний уход в минус
    }
}
