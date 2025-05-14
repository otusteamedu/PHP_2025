<?php declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Application\Port\ReportServiceInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

final class ReportService implements ReportServiceInterface
{
    public function __construct(
        private KernelInterface  $kernel,
    ){}

    public function dumpReport(string $htmlReport, string $fileName):string
    {
        $filePath = $this->kernel->getProjectDir() . '/public/uploads/' . $fileName . '.html';
        $filesystem = new Filesystem();
        $filesystem->dumpFile($filePath, $htmlReport);
        return $filePath;
    }
}
