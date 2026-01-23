<?php
declare(strict_types=1);

namespace App\Application\DTO;

final class EmailsInputDTO
{
    /**
     * @var string[] $emails
     */
    public array $emails;

    public function __construct(array $emails)
    {
        $this->emails = $emails;
    }
}
