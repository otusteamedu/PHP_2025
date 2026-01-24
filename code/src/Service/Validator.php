<?php

namespace App\Service;

class Validator implements ValidatorInterface{

	const BAD_REQUEST = 400;
	const OK_REQUEST = 200;

	public function validate(string $string): ValidationResult{
	    if (strlen($string) == 0) {
			return new ValidationResult("string пустой", self::BAD_REQUEST);
	    }else{
			$countOpenBrackets = 0;
			for ($i=0; $i < strlen($string); $i++) { 
				if ($string[$i] == "("){
					$countOpenBrackets = $countOpenBrackets + 1; 
				}elseif( $string[$i] == ")"){
					if ($countOpenBrackets == 0){
						return new ValidationResult("Некорректное количество скобок или их расположение", self::BAD_REQUEST);
					}
					$countOpenBrackets = $countOpenBrackets - 1;
				}
			}
			if ($countOpenBrackets == 0){
				return new ValidationResult("Все хорошо", self::OK_REQUEST);
			}else{
				return new ValidationResult("Некорректное количество скобок, ( больше", self::BAD_REQUEST);
			}
		} 	    
	}
}