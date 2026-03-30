<?php

declare(strict_types=1);

namespace App\Dto;

class EmailValidateEntryDto {
    private array $emailList;

    public function __construct(array $emailList) {
        $this->emailList = $emailList;
    }

    public function getEmailList(): array {
        return $this->emailList;
    }
}
