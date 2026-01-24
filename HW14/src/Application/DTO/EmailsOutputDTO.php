<?php
declare(strict_types=1);

namespace App\Application\DTO;

final class EmailsOutputDTO
{
    /**
     * @var EmailValidationInfoDTO[] $validationResults
     */
    public array $validationResults;

    public function __construct(array $validationResults)
    {
        $this->validationResults = $validationResults;
    }

    public function toArray(): array
    {
        return [
            'validationResults' => array_map(
                static fn (EmailValidationInfoDTO $dto): array => $dto->toArray(),
                $this->validationResults
            ),
        ];
    }
}
