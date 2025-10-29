<?php

declare(strict_types=1);

namespace App\Tests\News\Application\UseCase\DownloadNewsReport;

use App\News\Application\UseCase\DownloadNewsReport\DownloadNewsReportRequest;
use App\News\Application\UseCase\DownloadNewsReport\DownloadNewsReportResponse;
use App\News\Application\UseCase\DownloadNewsReport\DownloadNewsReportUseCase;
use App\Shared\Application\Service\FileHelperInterface;
use PHPUnit\Framework\TestCase;

class DownloadNewsReportUseCaseTest extends TestCase
{
    private DownloadNewsReportUseCase $useCase;
    private FileHelperInterface $fileHelperMock;

    protected function setUp(): void
    {
        $this->fileHelperMock = $this->createMock(FileHelperInterface::class);
        $this->useCase = new DownloadNewsReportUseCase($this->fileHelperMock);
    }

    public function testInvokeReturnsCorrectResponseSuccessfully(): void
    {
        //Arrange
        $fileName = 'report123.html';
        $mimeType = 'text/html';
        $stream = fopen('php://memory', 'r+');

        $this->fileHelperMock->expects($this->once())
            ->method('getFileMimeType')
            ->with($fileName)
            ->willReturn($mimeType);

        $this->fileHelperMock->expects($this->once())
            ->method('readStream')
            ->with($fileName)
            ->willReturn($stream);

        // Act
        $request = new DownloadNewsReportRequest($fileName);
        $response = ($this->useCase)($request);

        // Assert
        $this->assertInstanceOf(DownloadNewsReportResponse::class, $response);
        $this->assertSame($stream, $response->stream);
        $this->assertSame($mimeType, $response->mimeType);
    }

    public function testInvokeWithNonExistentFile(): void
    {
        $fileName = 'nonexistent.html';

        $this->fileHelperMock->method('readStream')
            ->willReturn(null);

        $request = new DownloadNewsReportRequest($fileName);
        $response = ($this->useCase)($request);

        $this->assertNull($response->stream);
        $this->assertInstanceOf(DownloadNewsReportResponse::class, $response);
    }
}