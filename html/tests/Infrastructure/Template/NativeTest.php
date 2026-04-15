<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Infrastructure\Template;

use Otus\Queue\Infrastructure\Template\Native;
use Otus\Queue\Infrastructure\Template\TemplateException;
use PHPUnit\Framework\TestCase;

final class NativeTest extends TestCase
{
    public function testRenderReturnsCompiledTemplateOutput(): void
    {
        $dir = sys_get_temp_dir() . '/otus_template_' . uniqid('', true);
        mkdir($dir, 0777, true);

        file_put_contents($dir . '/hello.php', '<h1><?= $name ?></h1>');

        $template = new Native($dir);

        self::assertSame('<h1>Alex</h1>', $template->render('hello', ['name' => 'Alex']));

        unlink($dir . '/hello.php');
        rmdir($dir);
    }

    public function testRenderThrowsWhenTemplateNotFound(): void
    {
        $template = new Native(sys_get_temp_dir());

        $this->expectException(TemplateException::class);
        $this->expectExceptionMessage('Template not found');

        $template->render('missing_template');
    }
}
