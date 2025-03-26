<?php
$_SERVER['DOCUMENT_ROOT'] = '/var/www/app/public';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/App.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/BookStoreService.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/CommandInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Decorators/SearchResultsDecoratorInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Decorators/ConsoleTableDecorator.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Commands/Search.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Commands/FillStore.php';

