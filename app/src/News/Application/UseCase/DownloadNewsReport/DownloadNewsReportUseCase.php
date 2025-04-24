<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\DownloadNewsReport;

use App\Shared\Domain\Service\FileHelper;
use League\Flysystem\FilesystemException;

readonly class DownloadNewsReportUseCase
{
    public function __construct(
        private FileHelper $reportFileHelper
    ) {
    }

    /**
     * @throws FilesystemException
     */
    public function __invoke(DownloadNewsReportRequest $request): DownloadNewsReportResponse
    {
        $mimeType = $this->reportFileHelper->getFileMimeType($request->fileName);
        $stream = $this->reportFileHelper->readStream($request->fileName);

        return new DownloadNewsReportResponse($stream, $mimeType);
    }

}
