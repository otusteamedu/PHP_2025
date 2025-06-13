<?php

namespace App;

use App\http\Request;
use App\validation\ValidatorInterface;

class Processor {
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator) {
        $this->validator = $validator;
    }

    public function process(Request $request): array {
        $strings = preg_split("/\r?\n/", trim($request->getInput()));
        return array_values(array_filter($strings, fn($string) => $this->validator->isValid($string)));
    }
}
