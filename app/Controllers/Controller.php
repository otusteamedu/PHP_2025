<?php

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Http\Request;
use App\Validators\Validator;

class Controller
{
    /** @var string|null */
    protected ?string $validator;

    /** @var Request */
    protected Request $request;

    /**
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
    public function initValidation(Request $request) {
        $this->request = $request;

        if (empty($this->validator) === false) {
            /** @var Validator $validator */
            $validator = (new $this->validator($this->request));
            $validator->validate();
        }
    }
}