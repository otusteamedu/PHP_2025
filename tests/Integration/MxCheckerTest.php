<?php

namespace EmailsVerifier\Tests\Integration;

use EmailsVerifier\Infrastructure\MxChecker;
use PHPUnit\Framework\TestCase;

class MxCheckerTest extends TestCase
{
    private MxChecker $mxChecker;

    protected function setUp(): void
    {
        $this->mxChecker = new MxChecker();
    }

    public function testHasMxRecordValidDomain(): void
    {
        $this->assertTrue($this->mxChecker->hasMxRecord('mail.ru'));
    }

    public function testHasMxRecordInvalidDomain(): void
    {
        $this->assertFalse($this->mxChecker->hasMxRecord('invalid-nonexistent-domain-abc.ru'));
    }
}
