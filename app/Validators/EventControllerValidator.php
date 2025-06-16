<?php

namespace App\Validators;

use App\Exceptions\ValidationException;

class EventControllerValidator extends Validator
{
    /**
     * @throws ValidationException
     */
    public function get() {
        $data = $this->request->getData();

        if (is_array($data['params']) === false || empty($data['params'])) {
            $this->fail("Должен присутствовать массив условий");
        }

        foreach ($data['params'] as $value) {
            if (is_numeric($value) === false) {
                $this->fail("Значения условий должны быть номером");
            }
        }
    }

    /**
     * @throws ValidationException
     */
    public function create() {
        $data = $this->request->getData();

        if (is_string($data['priority']) === false) {
            $this->fail("Приоритет должен быть строкой");
        }

        if (is_array($data['conditions']) === false || empty($data['conditions'])) {
            $this->fail("Должен присутствовать массив условий");
        }

        foreach ($data['conditions'] as $value) {
            if (is_numeric($value) === false) {
                $this->fail("Значения условий должны быть номером");
            }
        }

        if (is_array($data['event']) === false || empty($data['event'])) {
            $this->fail("Должен присутствовать массив события");
        }
    }
}