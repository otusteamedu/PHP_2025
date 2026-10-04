<?php

declare(strict_types=1);

namespace App\Infrastructure\Mail\Factory;

use App\Core\Container\Config\Contracts\DotEnvConfigInterface;
use App\Infrastructure\Mail\Mailer\ReportMailer;
use App\Infrastructure\Mail\Mailer\ReportMailerInterface;
use App\Infrastructure\Mail\Mailer\TemporarilyUnavailableReportMailer;
use App\Infrastructure\Mail\Mailpit\MailpitChaosClient;
use App\Infrastructure\Mail\Mailpit\MailpitChaosTrigger;
use App\Infrastructure\Mail\Simulation\ReportMailerSimulation;
use App\Infrastructure\Mail\Smtp\SmtpCode;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;

class ReportMailerFactory
{
    public function __construct(
        private readonly DotEnvConfigInterface $config,
        private readonly MailpitChaosClient $chaosClient,
    ) {
    }

    public function create(ReportMailerSimulation $mode): ReportMailerInterface
    {
        $mailer = $this->createMailer();
        $maxAttempts = (int) ($this->config->get('RABBITMQ_BANK_REPORTS_MAX_ATTEMPTS') ?? 3);

        return match ($mode) {
            ReportMailerSimulation::Ok => $this->createOk($mailer),
            ReportMailerSimulation::Down => $this->createDown($mailer),
            ReportMailerSimulation::Flaky => $this->createFlaky($mailer, $maxAttempts),
        };
    }

    private function createMailer(): ReportMailer
    {
        $dsn = Dsn::fromString($this->config->get('MAILER_DSN'));
        $transport = new EsmtpTransportFactory()->create($dsn);

        return new ReportMailer(
            mailer: new Mailer($transport),
            fromEmail: $this->config->get('BANK_REPORTS_MAILER_FROM', 'reports@bank.local'),
        );
    }

    /**
     * Базовый режим: почта уходит доставляется без помех
     * Mailpit Chaos: вероятность 0% - ошибка 451 не возникает.
     */
    private function createOk(ReportMailer $mailer): ReportMailerInterface
    {
        $this->chaosClient->setChaos(MailpitChaosTrigger::Recipient, SmtpCode::LocalError, probability: 0);

        return $mailer;
    }

    /**
     * Эмулирует полную недоступность SMTP-сервера.
     * Mailpit Chaos: вероятность 100% - ошибка 451 возникает на каждый запрос.
     */
    private function createDown(ReportMailer $mailer): ReportMailerInterface
    {
        $this->chaosClient->setChaos(MailpitChaosTrigger::Recipient, SmtpCode::LocalError, probability: 100);

        return $mailer;
    }

    /**
     * Эмулирует временную недоступность SMTP-сервера.
     * Mailpit Chaos не умеет эмулировать временные ошибки, поэтому реджекты генерирует декоратор.
     */
    private function createFlaky(ReportMailer $mailer, int $maxAttempts): ReportMailerInterface
    {
        $this->chaosClient->setChaos(MailpitChaosTrigger::Recipient, SmtpCode::LocalError, probability: 0);

        return new TemporarilyUnavailableReportMailer(
            innerMailer: $mailer,
            maxAttempts: $maxAttempts,
        );
    }
}
