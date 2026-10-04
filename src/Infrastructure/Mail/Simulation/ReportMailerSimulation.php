<?php

declare(strict_types=1);

namespace App\Infrastructure\Mail\Simulation;

enum ReportMailerSimulation: string
{
    case Ok = 'ok';
    case Down = 'down';
    case Flaky = 'flaky';
}
