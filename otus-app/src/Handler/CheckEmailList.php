<?php

declare(strict_types=1);

namespace App\Handler;

use App\Exception\CustomException;
use App\Service\EmailChecker;
use Exception;

class CheckEmailList
{
    /**
     * @throws Exception
     */
    public function process(array $data): string
    {
        $emailList = $data['emailList'] ?? null;

        if ($emailList === null) {
            throw new CustomException('Invalid request body', 400);
        }

        $emailChecker = new EmailChecker();
        $emailChecker->checkIsValidEmailList($emailList);

        return 'Email list is valid';
    }
}
