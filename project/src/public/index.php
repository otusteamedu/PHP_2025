<?php

function validateLength($str) {
    if(empty($str)) {
        throw new Exception("string is required");
    }
}
function validateBrackers($str) {
    validateLength($str);
    $openBreackers=0;
    for ($i=0; $i < strlen($str); $i++) {
        if($str[$i] == '(') {
            $openBreackers++;
        } elseif($str[$i] == ')') {
            $openBreackers--;
        }

        if($openBreackers < 0) {
            throw new Exception("Wrong syntax on $i");
        }
    }
    if($openBreackers != 0) {
        throw new Exception("Wrong syntax on $i");
    }

}

try {
    validateBrackers($_POST['string']??'');
    echo '<code>' . $_POST['string'] . '</code> - right syntax';
} catch (Exception $e) {
    http_response_code(400);
    echo $e->getMessage();
}
