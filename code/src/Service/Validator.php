<?php

namespace App\Service;

class Validator implements ValidatorInterface
{
    public $validators = [];

    public function __construct(array $validators)
    {
        foreach($validators as $validator){
        	$this->addValidator($validator);
        }
    }

    public function addValidator(ValidatorInterface $validator): void
    {
    	$this->validators[] = $validator;
    }
    
    public function validate(string $string): ValidationResult
    {
        foreach($this->validators as $validator){
            $result = $validator->validate($string);
            if (!$result->isValid()) {
                return $result;
            }
        }
        return new ValidationResult("Все хорошо", self::OK_REQUEST);
    }	
}