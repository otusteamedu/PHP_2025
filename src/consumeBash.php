<?php

$number1 = $_POST['number1'];
$number2 = $_POST['number2'];

echo shell_exec('bash consume.bash' . ' ' . $number1 . ' ' . $number2);