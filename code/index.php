<?php

if (array_key_exists('string', $_POST)) {
    if (empty($_POST['string']) ) {
        http_response_code(400);
        throw new Exception("string пустой");
    }
    $string = $_POST['string']; 
	$countOpenBrackets = 0;
	for ($i=0; $i < strlen($string); $i++) { 
		if ($string[$i] == "("){
			$countOpenBrackets = $countOpenBrackets + 1; 
		}elseif( $string[$i] == ")"){
			if ($countOpenBrackets == 0){
        		http_response_code(400);
				throw new Exception("Некорректное количество скобок или их расположение");
			}
			$countOpenBrackets = $countOpenBrackets - 1;
		}
	}
	if ($countOpenBrackets == 0){
		http_response_code(200);
		echo "Всё хорошо";
	}else{
		http_response_code(400);
		throw new Exception("Некорректное количество скобок, ( больше");
	}    
}
