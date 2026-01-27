<?php

namespace App\Service;

class BracketValidator implements ValidatorInterface
{
    public function validate(string $string): ValidationResult
    {
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
             return new ValidationResult("", self::OK_REQUEST);
         }else{
             return new ValidationResult("Некорректное количество скобок, ( больше", self::BAD_REQUEST);
         }
    }        
}