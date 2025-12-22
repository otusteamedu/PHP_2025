<?php

namespace App;

use App\http\Request;
use App\validation\Validator;

class Processor {
    private Validator $validator;

    public function __construct(Validator $validator) {
        $this->validator = $validator;
    }

    public function process(Request $request): array {
        $strings = preg_split("/\r?\n/", trim($request->getInput()));
        return array_values(array_filter($strings, fn($string) => $this->validator->isValid($string)));
    }
}