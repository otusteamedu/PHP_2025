<?php

function validate()
{
    $str = $_POST['string'];

    while (stripos($str, '()') !== false)
        $str = str_replace('()', '', $str);

    if (!empty($str))
        throw new Exception('no valid argument');
}
