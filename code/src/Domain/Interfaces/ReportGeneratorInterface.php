<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

interface ReportGeneratorInterface
{
    /**
     * Генерирует банковскую отчет
     *
     * @param string $name Имя клиента
     * @param string $email Email клиента
     * @param int $year Год выписки
     * @return string HTML-содержимое отчёта
     */
    public function generate(string $name, string $email, int $year): string;
}
