<?php
require_once 'validation.php';

function process_emails($filename) {
    
    $emails = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $results = [];

    foreach ($emails as $email) {
        $results[$email] = validate_email($email);
    }
    
    return $results;
}
?>