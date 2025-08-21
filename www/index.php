<?php

try {

    if (!isset($_POST['string']))
        throw new Exception('not found required argument');


    if (empty($_POST['string']) || strlen($_POST['string']) === 1)
        throw new Exception('empty string argument');

    $str = $_POST['string'];

    while (stripos($str, '()') !== false)
        $str = str_replace('()', '', $str);


    if (!empty($str))
        throw new Exception('no valid argument');

    header('HTTP/1.1 200');
    echo 'success!';
} catch (\Throwable $th) {
    header('HTTP/1.1 400 Bad Request');
    echo $th->getMessage();
}
