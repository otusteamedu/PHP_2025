<?php

function auth()
{
    if (!isset($_POST['string']))
        throw new Exception('not found required argument');


    if (empty($_POST['string']) || strlen($_POST['string']) === 1)
        throw new Exception('empty string argument');
}
