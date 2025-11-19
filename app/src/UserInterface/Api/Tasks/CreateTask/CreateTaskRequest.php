<?php

declare(strict_types=1);

namespace App\UserInterface\Api\Tasks\CreateTask;

use Symfony\Component\Validator\Constraints as Assert;

class CreateTaskRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Email]
        public string $email,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $title,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $description,
    )
    {
    }

}
