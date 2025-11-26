<?php
function validate_email($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Некорректный Email";
    }

    $domain = substr(strrchr($email, "@"), 1);
    
    return checkdnsrr($domain, "MX") ? "Корректный Email" : "Некорректный Email";
}
?>