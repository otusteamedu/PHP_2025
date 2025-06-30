<?php

$router->post('api/requests', 'RequestController@create');
$router->get('api/requests/{id}', 'RequestController@status');
