<?php

require __DIR__.'/../vendor/autoload.php';

use App\Controllers\SearchController;

$controller = new SearchController();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && (!empty($_GET))) {
  $controller->handleSearch();
} else {
  $controller->showSearchForm();
}