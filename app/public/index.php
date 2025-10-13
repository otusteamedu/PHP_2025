<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Http\Request;
use App\ValidateInputField\ValidateFields;
use App\ValidateInputField\ValidateRequestMethodPost;

if(ValidateRequestMethodPost::validate() !== null){
    echo ValidateRequestMethodPost::validate();
    exit();
}

echo ValidateFields::validate(Request::getBody());
