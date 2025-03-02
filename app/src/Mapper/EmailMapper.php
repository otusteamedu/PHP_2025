<?php
declare(strict_types=1);


namespace App\Mapper;

use Symfony\Component\Validator\Constraints as Assert;


class EmailMapper
{
    public function getValidationCollectionEmailList(): Assert\Collection
    {
        return new Assert\Collection(
            [
                'emails' => new Assert\All([
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ])
            ]
        );


    }

}