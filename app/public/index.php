<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use User\Php2025\src\Http\Request;
use User\Php2025\src\ValidateInputField\ValidateFields;
use User\Php2025\src\ValidateInputField\ValidateRequestMethodPost;

if(ValidateRequestMethodPost::validate() !== null){
    echo ValidateRequestMethodPost::validate();
    exit();
}

echo ValidateFields::validate(Request::getBody());
