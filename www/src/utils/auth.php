<?php

function auth()
{
    if (!isset($_POST['email']))
        throw new Exception('not found required argument');


    if (empty($_POST['email']))
        throw new Exception('empty email argument');
}
