<?php

namespace Infrastructure\Http\Validators;

use Application\Validators\Validator;
use Infrastructure\Exceptions\ValidationException;

class EventControllerValidator extends Validator
{
    /**
     * @throws ValidationException
     */
    public function create(): void {
        $data = $this->request->getData();

        if (is_string($data['type']) === false) {
            $this->fail("Тип должен быть строкой");
        }

        if (is_string($data['title']) === false) {
            $this->fail("Наименование должно быть строкой");
        }

        if (is_numeric($data['priority']) === false) {
            $this->fail("Приоритет должен быть числом");
        }

        if (empty($data['comment']) === false && is_string($data['comment']) === false) {
            $this->fail("Комментарий должен быть строкой");
        }
    }

    /**
     * @return void
     * @throws ValidationException
     */
    public function one(): void {
        $data = $this->request->getQuery();

        if (empty($data['id']) && is_string($data['id']) === false) {
            $this->fail("Поле id должно быть строкой");
        }
    }

    /**
     * @return void
     * @throws ValidationException
     */
    public function delete(): void {
        $data = $this->request->getQuery();

        if (empty($data['id']) && is_string($data['id']) === false) {
            $this->fail("Поле id должно быть строкой");
        }
    }
}