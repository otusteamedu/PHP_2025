<?php

namespace Pryaniki\Tests\Presentation\Console\Input;

use PHPUnit\Framework\TestCase;
use Pryaniki\App\Presentation\Console\Input\ArgEmailInput;

class ArgEmailInputTest extends TestCase
{
    private array $originalServer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalServer = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->originalServer;

        parent::tearDown();
    }

    public function testReturnsEmailArgument(): void
    {
        // Arrange
        $_SERVER['argv'] = [
            'app.php',
            'test@gmail.com',
        ];

        // Act
        $email = ArgEmailInput::getEmail();

        // Assert
        self::assertSame(
            'test@gmail.com',
            $email,
        );
    }

    public function testReturnsEmptyStringWhenArgumentIsMissing(): void
    {
        // Arrange
        $_SERVER['argv'] = [
            'app.php',
        ];

        // Act
        $email = ArgEmailInput::getEmail();

        // Assert
        self::assertSame(
            '',
            $email,
        );
    }
}