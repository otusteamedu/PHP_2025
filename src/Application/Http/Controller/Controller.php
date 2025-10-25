<?php

namespace Application\Http\Controller;

use Application\Http\Request;
use Application\Validators\Validator;
use Infrastructure\Exceptions\ValidationException;

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
    public function initValidation(Request $request): void {
        $this->request = $request;

        if (empty($this->validator) === false) {
            /** @var Validator $validator */
            $validator = (new $this->validator($this->request));
            $validator->validate();
        }
    }
}