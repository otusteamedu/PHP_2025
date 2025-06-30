<?php

$router->get('/', 'HomeController@index');
$router->post('/requests', 'RequestController@create');
$router->get('/requests/{id}', 'RequestController@status');

