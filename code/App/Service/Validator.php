<?php

namespace Service;

class Validator{

	private $code;
	private $message;

	public function validate(){
	    if (empty($_POST['string']) ) {
			$this->code = 400;
	        $this->message = "string пустой";
	    }else{
		    $string = $_POST['string']; 
			$countOpenBrackets = 0;
			for ($i=0; $i < strlen($string); $i++) { 
				if ($string[$i] == "("){
					$countOpenBrackets = $countOpenBrackets + 1; 
				}elseif( $string[$i] == ")"){
					if ($countOpenBrackets == 0){
		        		$this->code = 400;
						$this->message = "Некорректное количество скобок или их расположение";
						return;
					}
					$countOpenBrackets = $countOpenBrackets - 1;
				}
			}
			if ($countOpenBrackets == 0){
				$this->code = 200;
				$this->message = "Всё хорошо";
			}else{
				$this->code = 400;
				$this->message = "Некорректное количество скобок, ( больше";
			}
		} 	    
	}

	public function getCode(){
		return $this->code;
	}

	public function getMessage(){
		return $this->message;
	}
}