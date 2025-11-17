<?php

declare(strict_types=1);

namespace App\Handler;

use App\Dto\EmailValidateEntryDto;
use App\RequestService\SomeProviderNameRequestService;
use App\Service\EmailValidationService;
use App\Validator\DefaultEmailValidator;
use App\Validator\DnsEmailValidator;
use App\Validator\SendConfirmationEmailValidator;
use Exception;

class EmailValidateHandler
{
    public function __construct(
        private readonly EmailValidationService $emailValidator,
    ) {
        $this->emailValidator->setValidator(new DefaultEmailValidator());
        $this->emailValidator->setValidator(new DnsEmailValidator());
        $this->emailValidator->setValidator(new SendConfirmationEmailValidator(new SomeProviderNameRequestService()));
    }

    /**
     * @throws Exception
     */
    public function handle(EmailValidateEntryDto $entryDto): bool
    {
        return $this->emailValidator->isValidEmailList($entryDto->getEmailList());
    }
}
